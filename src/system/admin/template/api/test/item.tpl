<style type="text/css">
    #api-test-form textarea {
        font-family: Consolas, Monospace;
        padding-left:5px;
    }

    #api-test-form .col-sm-2 {
        width: 150px;
    }

    .params-table input {
        width: 100%;
    }

    .params-table .add-param-td {
        text-align: right;
    }

    .init-param {
        display: none;
    }

    input.param-name, input.param-name:active, input.param-name:focus {
        color: #0881E4 !important;
        font-weight: bold;
    }

    input.param-name::-webkit-input-placeholder {
        font-weight: normal;
    }

　　 input.param-name:-moz-placeholder {
        font-weight: normal;
    }

　　 input.param-name::-moz-placeholder {
        font-weight: normal;
    }

　　 input.param-name:-ms-input-placeholder {
        font-weight: normal;
        　　
    }
    #request-loading {
        display: none;
    }
</style>
<script src="{$sysurl}/assets/js/common.js"></script>

<div class="row">
    <div class="col-xs-12">
        <form class="form-horizontal" id="api-test-form" action="/api.test/ajaxRequest" method="post">
            <div class="form-group">
                <input value="{$data['id']}" type="hidden" name="id"/>
                <input value="{$data['appid']}" type="hidden" name="appid"/>

                <label class="control-label col-xs-12 col-sm-2 no-padding-right">请求接口:</label>
                <div class="col-xs-12 col-sm-10">
                    <div class="clearfix">
                        <select class="select2 col-sm-12" name="api_id" style="padding:0;" onchange="onApiChange(this.value)">
                            <option value="0">可搜索当前应用的接口进行测试，选择可自动补充测试参数</option>
                            {foreach from=$apiList index=$index key=$key item=$cat}
                                <optgroup label="{$cat['ancestor_names']}"></optgroup>
                                {foreach from=$cat['api_list'] index=$index key=$k item=$v}
                                    <option value="{$v['id']}" {if $data['api_id'] == $v['id'] }selected{/if}>{$cat['ancestor_names']}：{$v['address']}&nbsp;&nbsp;({$v['name']})</option>
                                {/foreach}
                            {/foreach}
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-xs-12 col-sm-2 no-padding-right">测试名称:</label>
                <div class="col-xs-12 col-sm-10">
                    <div class="clearfix">
                        <input value="{$data['name']}" type="text" name="name" placeholder="用以保存当前测试接口的设置" class="col-xs-12 col-sm-12"/>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-xs-12 col-sm-2 no-padding-right required">请求方式:</label>
                <div class="col-xs-12 col-sm-8">
                    <div class="clearfix">
                        <select class="select2" name="request_method" style="width:300px;">
                            {foreach from=$methods index=$index key=$method item=$name}
                                <option value="{$method}" {if $data['request_method'] == $method }selected{/if}>{$name}</option>
                            {/foreach}
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-xs-12 col-sm-2 no-padding-right required">接口地址:</label>
                <div class="col-xs-12 col-sm-10">
                    <div class="clearfix">
                        {if $api['address_test'] || $api['address_prod']}
                            <select class="select2 col-sm-12" name="address" style="padding:0;">
                                <option value="{$api['address_test']}">测试地址：{$api['address_test']}</option>
                                <option value="{$api['address_prod']}">生产地址：{$api['address_prod']}</option>
                            </select>
                        {else}
                            <input value="{$data['address']}" type="text" name="address" placeholder="请填写接口的完整绝对路径地址" class="col-xs-12 col-sm-12"/>
                        {/if}
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-xs-12 col-sm-2 no-padding-right">连接超时:</label>
                <div class="col-xs-12 col-sm-10">
                    <div class="clearfix">
                        <input value="{$data['connection_timeout']}" type="text" style="width:200px;" name="connection_timeout" placeholder="请输入请求连接超时时间" class="col-xs-12 col-sm-12"/>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-xs-12 col-sm-2 no-padding-right">执行超时:</label>
                <div class="col-xs-12 col-sm-10">
                    <div class="clearfix">
                        <input value="{$data['timeout']}" type="text" style="width:200px;" name="timeout" placeholder="请输入请求执行超时时间" class="col-xs-12 col-sm-12"/>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-xs-12 col-sm-2 no-padding-right">会话保持:</label>
                <div class="col-xs-12 col-sm-10">
                    <div class="clearfix">
                        <div class="checkbox">
                            <label style="padding-left:0;">
                                <input name="keep_session" value="1" type="checkbox" class="ace" {if $data['keep_session']}checked{/if}>
                                <span class="lbl"> 每一次请求都将保持与目标服务器的SESSION状态</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-xs-12 col-sm-2 no-padding-right">请求参数:</label>

                <div class="col-xs-12 col-sm-10">
                    <div class="clearfix">
                        <table class="table table-striped table-bordered table-hover params-table request-params-table">
                            <thead>
                            <tr>
                                <th style="width:130px;" class="center">参数名称</th>
                                <th>参数内容</th>
                                <th style="width:50px;" class="center">操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr class="init-param">
                                <td>
                                    <input value="" class="param-name" type="text" name="request_params[name][]" placeholder="请输入参数名称"/>
                                </td>
                                <td>
                                    <input value="" type="text" name="request_params[content][]" placeholder="请输入参数内容"/>
                                </td>
                                <td class="center">
                                    <div class="visible-md visible-lg hidden-sm hidden-xs action-buttons">
                                        <a href="javascript:;" onclick="deleteParam(this)" class="red" title="删除"
                                           data-rel="tooltip">
                                            <i class="ace-icon fa fa-trash-o bigger-130"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            {foreach from=$data['request_params'] index=$index key=$key item=$value}
                                <tr>
                                    <td>
                                        <input value="{$key}" class="param-name" type="text" name="request_params[name][]" placeholder="请输入参数名称"/>
                                    </td>
                                    <td>
                                        <input value="{$value}" type="text" name="request_params[content][]" placeholder="请输入参数内容"/>
                                    </td>
                                    <td class="center">
                                        <div class="visible-md visible-lg hidden-sm hidden-xs action-buttons">
                                            <a href="javascript:;" onclick="deleteParam(this)" class="red" title="删除"
                                               data-rel="tooltip">
                                                <i class="ace-icon fa fa-trash-o bigger-130"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            {/foreach}
                            <tr>
                                <td colspan="3" class="add-param-td">
                                    <button class="btn btn-sm btn-primary add-request-param-button" type="button"><i class="ace-icon fa fa-plus bigger-110"></i>添加参数</button>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-xs-12 col-sm-2 no-padding-right">返回结果:</label>
                <div class="col-xs-12 col-sm-10">
                    <div class="clearfix">
                        <div id="request-loading"><i class="ace-icon fa fa-spinner fa-spin orange bigger-130"></i> 请求中...</div>
                        <textarea name="response_content_raw" placeholder="接口的原始输出信息将会显示在这里" class="col-xs-12 col-sm-12" style="height:200px;">{$data['response_content_raw']}</textarea>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-xs-12 col-sm-2 no-padding-right"></label>
                <div class="col-xs-12 col-sm-10">
                    <div class="clearfix">
                        <textarea name="response_content" placeholder="接口的执行结果将会显示在这里" class="col-xs-12 col-sm-12" style="height:200px;">{$data['response_content']}</textarea>
                    </div>
                </div>
            </div>


            <div class="form-group">
                <label class="control-label col-xs-12 col-sm-2 no-padding-right"></label>
                <div class="col-xs-12 col-sm-10">
                    <div class="clearfix">
                        <button class="btn btn-info" type="submit">
                            <i class="ace-icon fa fa-check bigger-110"></i>
                            提交执行&nbsp;&nbsp;&nbsp;
                        </button>
                    </div>
                </div>
            </div>
            <br />
            <br />
            <br />
        </form>
    </div>
</div>


<script type="text/javascript">
    // 选择api进行测试
    function onApiChange(apiId)
    {
        window.location.href='/api.test/?appid={$_GET['appid']}&apiid='+apiId;
    }

    // 删除参数(请求或者返回)
    function deleteParam(obj)
    {
        $(obj).parents('tr').remove();
    }

    $(document).ready(function () {
        // 添加请求参数按钮
        $('.add-request-param-button').click(function(){
            var html = '<tr class="request-param-tr-values">' + $(this).parents('tbody').find('tr:eq(0)').html() + '</tr>';
            $(this).parents('tbody').find('tr:last').before(html);
        });

        {if !$_GET['id']}
        // 初始化参数表数据
        $('.request-params-table').each(function(){
            if($(this).find('tbody tr').length < 4) {
                $(this).find('.add-request-param-button').click();
            }
        });
        {/if}

        // 表单验证
        $('#api-test-form').validate({
            errorElement: 'div',
            errorClass  : 'help-block',
            focusInvalid: true,
            rules       : {
                address: {
                    required: true
                }
            },
            messages    : {
                address: {
                    required: "接口地址不能为空"
                }
            },
            submitHandler: function(form) {
                $("textarea[name='response_content_raw']").val('');
                $("textarea[name='response_content']").val('');
                $(form).ajaxSubmit({
                    dataType: 'json',
                    beforeSend: function() {
                        $('#request-loading').show();
                    },
                    complete: function(XMLHttpRequest, status){
                        $('#request-loading').hide();
                    },
                    success : function(result){
                        if (result.result) {
                            $("textarea[name='response_content_raw']").val(result.data.response_content_raw);
                            $("textarea[name='response_content']").val(result.data.response_content);
                            reloadApiTestList();
                        }
                    }
                });
            },
            highlight: function (e) {
                $(e).closest('.form-group').removeClass('has-info').addClass('has-error');
            },
            success: function (e) {
                $(e).closest('.form-group').removeClass('has-error').addClass('has-info');
                $(e).remove();
            },
            errorPlacement: function (error, element) {
                if(element.is(':checkbox') || element.is(':radio')) {
                    var controls = element.closest('div[class*="col-"]');
                    if(controls.find(':checkbox,:radio').length > 1) {
                        controls.append(error);
                    } else {
                        error.insertAfter(element.nextAll('.lbl:eq(0)').eq(0));
                    }
                } else if(element.is('.select2')) {
                    error.insertAfter(element.siblings('[class*="select2-container"]:eq(0)'));
                } else if(element.is('.chosen-select')) {
                    error.insertAfter(element.siblings('[class*="chosen-container"]:eq(0)'));
                } else {
                    error.insertAfter(element.parent());
                }
            }
        });
    });
</script>



