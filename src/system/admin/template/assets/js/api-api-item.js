var codeMirrorJson     = null;
var codeMirrorXml      = null;
var codeMirrorTemplate = null;
var codeMirrorOther    = null;
CodeMirror.modeURL     = "/static/plugin/codemirror-5.21.0/mode/%N/%N.js";
var codeMirrorOptions  = {
    theme:                   "default",
    indentUnit:              4,
    smartIndent:             true,
    lineWrapping:            false,
    lineNumbers:             false,
    showCursorWhenSelecting: true,
    autofocus:               false,
    autoCloseTags:           true,
    matchBrackets:           true,
    autoRefresh:             true,
    readOnly:                false
};
var tabToSpaceFunction = {
    Tab: function(cm) {
        cm.indentSelection('add');
    }
};

// 覆盖原有的jquery html()方法，会将表单值也一起复制赋值
(function ($) {
    var oldHTML = $.fn.html;
    $.fn.formhtml = function () {
        if (arguments.length) return oldHTML.apply(this, arguments);
        $("input,button", this).each(function () {
            this.setAttribute('value', $(this).val());
        });
        $("textarea", this).each(function () {
            this.innerHTML = $(this).val();
        });
        $(":radio,:checkbox", this).each(function () {
            // im not really even sure you need to do this for "checked"
            // but what the heck, better safe than sorry
            if (this.checked) this.setAttribute('checked', 'checked');
            else this.removeAttribute('checked');
        });
        $("option", this).each(function () {
            // also not sure, but, better safe...
            if (this.selected) this.setAttribute('selected', 'selected');
            else this.removeAttribute('selected');
        });
        return oldHTML.apply(this);
    };
    //optional to override real .html() if you want
    $.fn.html = $.fn.formhtml;
})(jQuery);


// 参数往上排序
function moveParamUp(obj)
{
    var currentTrObj  = $(obj).parents('tr');
    var nextTrObj     = currentTrObj.next('tr');
    var previousTrObj = currentTrObj.prev('tr');
    if (!previousTrObj.hasClass('init-param')) {
        var ppreviousTrObj = previousTrObj.prev('tr');
        var tmp1 = ppreviousTrObj.html();
        var tmp2 = previousTrObj.html();
        ppreviousTrObj.html(currentTrObj.html());
        previousTrObj.html(nextTrObj.html());
        currentTrObj.html(tmp1);
        nextTrObj.html(tmp2);
    }
}

// 参数往下排序
function moveParamDown(obj)
{
    var currentTrObj  = $(obj).parents('tr');
    var nextTrObj     = currentTrObj.next('tr');
    var nnextTrObj    = nextTrObj.next('tr');
    if (nnextTrObj.find('input').length > 0) {
        var nnnextTrObj = nnextTrObj.next('tr');
        var tmp1 = nnextTrObj.html();
        var tmp2 = nnnextTrObj.html();
        nnextTrObj.html(currentTrObj.html());
        nnnextTrObj.html(nextTrObj.html());
        currentTrObj.html(tmp1);
        nextTrObj.html(tmp2);
    }
}

// 删除参数(请求或者返回)
function deleteParam(obj)
{
    var doDelete = function(obj) {
        var preType = 0; // 1:参数属性, 2:参数说明
        $(obj).parents('tr').attr('deleted', true);
        $(obj).parents('tbody').find('tr').each(function(){
            if ($(this).attr('deleted')) {
                $(this).remove();
            } else {
                if ($(this).find('textarea').length > 0) {
                    if (preType != 1) {
                        preType = 2;
                        $(this).remove();
                    } else {
                        preType = 2;
                    }
                } else {
                    preType = 1;
                }
            }
        });
    }
    if ($(obj).parents('tr').find('input:eq(0)').val().length > 0) {
        var message = "<div class='bigger-110'>确定删除该参数？</div>";
        bootbox.dialog({
            message: message,
            buttons: {
                "button" : {
                    "label" : "取消",
                    "className" : "btn-sm"
                },
                "danger" : {
                    "label" : "删除",
                    "className" : "btn-sm btn-danger",
                    "callback": function() {
                        doDelete(obj);
                    }
                }
            }
        });
    } else {
        doDelete(obj);
    }
}

$(document).ready(function(){
    UE.delEditor('editor');
    UE.getEditor('editor');

    /**
     * 返回示例代码高亮处理
     */
    codeMirrorJson     = CodeMirror.fromTextArea($('#response-type-json textarea')[0], codeMirrorOptions);
    codeMirrorXml      = CodeMirror.fromTextArea($('#response-type-xml textarea')[0], codeMirrorOptions);
    codeMirrorTemplate = CodeMirror.fromTextArea($('#response-type-template textarea')[0], codeMirrorOptions);
    codeMirrorOther    = CodeMirror.fromTextArea($('#response-type-other textarea')[0], codeMirrorOptions);
    codeMirrorJson.setOption("extraKeys",       tabToSpaceFunction);
    codeMirrorXml.setOption("extraKeys",        tabToSpaceFunction);
    codeMirrorTemplate.setOption("extraKeys",   tabToSpaceFunction);
    codeMirrorOther.setOption("extraKeys",      tabToSpaceFunction);
    codeMirrorJson.setOption("mode",            'javascript');
    codeMirrorXml.setOption("mode",             'xml');
    codeMirrorTemplate.setOption("mode",        'htmlmixed');
    codeMirrorOther.setOption("mode",           'htmlmixed');
    CodeMirror.autoLoadMode(codeMirrorJson,     'javascript');
    CodeMirror.autoLoadMode(codeMirrorXml,      'xml');
    CodeMirror.autoLoadMode(codeMirrorTemplate, 'htmlmixed');
    CodeMirror.autoLoadMode(codeMirrorOther,    'htmlmixed');

    // 添加请求参数按钮
    $('.add-request-param-button').click(function(){
        var html = '<tr class="request-param-tr-values">' + $(this).parents('tbody').find('tr:eq(0)').html() + '</tr>';
        html    += '<tr class="request-param-tr-brief">' + $(this).parents('tbody').find('tr:eq(1)').html() + '</tr>';
        $(this).parents('tbody').find('tr:last').before(html);
    });

    // 添加返回参数按钮
    $('.add-response-param-button').click(function(){
        var html = '<tr class="response-param-tr-values">' + $(this).parents('tbody').find('tr:eq(0)').html() + '</tr>';
        html    += '<tr class="response-param-tr-brief">' + $(this).parents('tbody').find('tr:eq(1)').html() + '</tr>';
        $(this).parents('tbody').find('tr:last').before(html);
    });

    // 通过返回示例自动识别返回参数
    $('.check-response-params').click(function(){
        var jsonValue = codeMirrorJson.getValue();
        var xmlValue  = codeMirrorXml.getValue();
        if (jsonValue.length < 1 && xmlValue < 1) {
            return;
        }
        var doRequest = function() {
            $.ajax({
                url     : '/api.api/ajaxCheckRespnseParams',
                type    : 'post',
                data    : {
                    json: jsonValue,
                    xml : xmlValue
                },
                dataType: 'json',
                success : function(result){
                    if (result.result) {
                        $('.response-param-tr-values').remove();
                        $('.response-param-tr-brief').remove();
                        for (var i = 0; i < result.data.length; ++i) {
                            $('.response-params-table .add-response-param-button').click();
                            $('.response-param-tr-values:last').find("input[name='content[response_params][name][]']").val(result.data[i].name);
                            $('.response-param-tr-values:last').find("select[name='content[response_params][type][]']").val(result.data[i].type);
                            $('.response-param-tr-values:last').find("input[name='content[response_params][example][]']").val(result.data[i].example);
                        }
                        $.gritter.add({
                            title: '成功提示',
                            text : '返回参数已自动识别',
                            class_name: 'gritter-success gritter-right'
                        });
                    } else {
                        $.gritter.add({
                            title: '错误提示',
                            text : result.message,
                            class_name: 'gritter-error gritter-right'
                        });
                    }
                }
            });
        }
        var responseParamNameInputLength = $("input[name='content[response_params][name][]']").length;
        if (responseParamNameInputLength > 2
            || (responseParamNameInputLength == 2
            && $("input[name='content[response_params][name][]']:eq(1)").val().length > 1)) {
            bootbox.dialog({
                message: "自动识别返回示例中的数据格式将会覆盖当前已设置的返回参数，是否继续？",
                buttons:
                    {
                        "button" : {"label" : "取消", "className" : "btn-sm"},
                        "danger" : {"label" : "继续", "className" : "btn-sm btn-info",
                            "callback": function() {
                                doRequest();
                            }
                        }
                    }
            });
        } else {
            doRequest();
        }
    });

    /**
     * 提交并验证数据
     */
    $('#api-save-form').validate({
        errorElement: 'div',
        errorClass  : 'help-block',
        focusInvalid: true,
        rules       : {
            name: {
                required: true
            },
            address: {
                required: true
            }
        },
        messages    : {
            name: {
                required: "接口名称不能为空"
            },
            address: {
                required: "接口地址不能为空"
            }
        },
        submitHandler: function(form) {
            $(form).ajaxSubmit({
                dataType: 'json',
                success : function(result){
                    if (result.result) {
                        $.gritter.add({
                            title: '成功提示',
                            text : result.message,
                            class_name: 'gritter-success gritter-right'
                        });
                        // reloadCategoryAndApiList();
                        reloadCategory();
                    } else {
                        $.gritter.add({
                            title: '错误提示',
                            text : result.message,
                            class_name: 'gritter-error gritter-right'
                        });
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