<?php
/**
 * 后台管理菜单列表，注释项为未正式发布功能。
 *
 * 1. 菜单最多为三级；
 * 2. 第一级和最后一级菜单可以带icon标签，表示图标；
 * 3. 菜单格式：
    array(
        'name' => 菜单名字,
        'icon' => 菜单前的小图标(来源于font awesome),
        'acts' => 对应的控制器方法名称,
        'url'  => 跳转链接,
        'subs' => 子级菜单数组,
    ),
 *
 * @var array
 */
return array(
    array(
        'name' => '后台首页',
        'icon' => 'menu-icon fa fa-home',
        'acts' => array('default', 'index'),
        'url'  => '/default',
    ),
    'menu' => array(
        'name' => '菜单管理',
        'icon' => 'book',
        'sub'  => array(
            'index'    => array(
                'name' => '菜单管理',
                'icon' => '',
            ),
            'category' => array(
                'name' => '菜单分类',
                'icon' => '',
            ),
        ),
    ),
    'article' => array(
        'name' => '文章管理',
        'icon' => 'edit',
        'sub'  => array(
            'index'    => array(
                'name' => '文章管理',
                'icon' => '',
            ),
            'showEdit' => array(
                'name' => '文章发布',
                'icon' => '',
            ),
            
            'category' => array(
                'name' => '文章分类',
                'icon' => '',
            ),
        ),
    ),
    'picture' => array(
        'name' => '图片管理',
        'icon' => 'picture',
        'sub'  => array(
            'index'    => array(
                'name' => '图片管理',
                'icon' => '',
            ),
            'showEdit' => array(
                'name' => '图片发布',
                'icon' => '',
            ),
            'category' => array(
                'name' => '图片分类',
                'icon' => '',
            ),
        ),
    ),
    'link' => array(
        'name' => '连接管理',
        'icon' => 'exchange',
        'sub'  => array(
            'index'    => array(
                'name' => '连接管理',
                'icon' => '',
            ),
            'showEdit' => array(
                'name' => '新建连接',
                'icon' => '',
            ),
            'category' => array(
                'name' => '连接分类',
                'icon' => '',
            ),
        ),
    ),
    
    'frag' => array(
        'name' => '碎片管理',
        'icon' => 'asterisk',
        'sub'  => array(
            'index'    => array(
                'name' => '碎片管理',
                'icon' => '',
            ),
            'showEdit' => array(
                'name' => '添加碎片',
                'icon' => '',
            ),
            'category' => array(
                'name' => '碎片分类',
                'icon' => '',
            ),
        ),
    ),
    'comment' => array(
        'name' => '评论管理',
        'icon' => 'comment',
        'sub'  => array(
            'index'    => array(
                'name' => '评论管理',
                'icon' => '',
            ),
        ),
    ),
    
    'guestbook' => array(
        'name' => '留言管理',
        'icon' => 'comment-alt',
        'sub'  => array(
            'index'    => array(
                'name' => '留言管理',
                'icon' => '',
            ),
        ),
    ),
    array(
        'name' => '系统管理',
        'icon' => 'menu-icon fa fa-cogs',
        'subs' => array(
            array(
                'name' => '操作日志',
                'acts' => array('operation-log', 'index'),
                'url'  => '/operation-log/index',
            ),
            array(
                'name' => '文件管理',
                'acts' => array('setting', 'filemanager'),
                'url'  => '/setting/filemanager',
            ),
        ),
    ),
    array(
        'name' => '服务管理',
        'icon' => 'menu-icon fa fa-cloud',
        'subs' => array(
            array(
                'name' => '应用管理',
                'acts' => array('api-app', 'index'),
                'url'  => '/api-app/index',
            ),
            array(
                'name' => '接口管理',
                'acts' => array('api-api', 'index'),
                'url'  => '/api-api/index',
            ),
            array(
                'name' => '接口测试',
                'acts' => array('api-test', 'index'),
                'url'  => '/api-test/index',
            ),
            /*
            array(
                'name' => '本地服务',
                'acts' => array('api-api', 'local'),
                'url'  => '/api-api/local',
            ),
            */
        ),
    ),

    array(
        'name' => '微信管理',
        'icon' => 'menu-icon fa fa-wechat',
        'subs' => array(
            array(
                'name' => '菜单管理',
                'subs'  => array(
                    array(
                        'name' => '自定义菜单',
                        'icon' => 'fa fa-th',
                        'acts' => array('wechat-menu', 'index'),
                        'url'  => '/wechat-menu',
                    ),
                ),
            ),

            array(
                'name' => '消息管理',
                'icon' => 'comments',
                'subs'  => array(
                    array(
                        'name' => '自动回复',
                        'icon' => 'fa fa-comments',
                        'acts' => array('wechat-reply', 'index'),
                        'url'  => '/wechat-reply',
                    ),
                ),
            ),

            array(
                'name' => '素材管理',
                'subs'  => array(
                    array(
                        'name' => '图片素材',
                        'icon' => 'fa fa-image',
                        'acts' => array('wechat-material', 'image'),
                        'url'  => '/wechat-material/image',
                    ),
                    /*
                    array(
                        'name' => '视频素材',
                        'icon' => 'fa fa-video-camera',
                        'acts' => array('wechat-material', 'video'),
                        'url'  => '/wechat-material/video',
                    ),
                    array(
                        'name' => '语音素材',
                        'icon' => 'fa fa-headphones',
                        'acts' => array('wechat-material', 'voice'),
                        'url'  => '/wechat-material/voice',
                    ),
                    */
                    array(
                        'name' => '图文素材',
                        'icon' => 'fa fa-pencil-square-o',
                        'acts' => array('wechat-material', 'news'),
                        'url'  => '/wechat-material/news',
                    ),
                ),
            ),
        ),
    ),

    array(
        'name' => '溯源管理',
        'icon' => 'menu-icon fa fa-cubes',
        'subs' => array(
            array(
                'name' => '产品分类',
                'acts' => array('trace.category', 'index'),
                'url'  => '/trace.category/index',
            ),
            array(
                'name' => '产品流程',
                'acts' => array('trace.flow', 'index'),
                'url'  => '/trace.flow/index',
            ),
            array(
                'name' => '产品管理',
                'acts' => array('trace.product', 'index'),
                'url'  => '/trace.product/index',
            ),
        ),
    ),

    array(
        'name' => '支付管理',
        'icon' => 'menu-icon fa fa-money',
        'subs' => array(
            array(
                'name' => '支付查询',
                'acts' => array('pay', 'index'),
                'url'  => '/pay/index',
            ),
        ),
    ),

    array(
        'name' => '用户管理',
        'icon' => 'menu-icon fa fa-group',
        'subs' => array(
            array(
                'name' => '添加用户',
                'acts' => array('user', 'item'),
                'url'  => '/user/item',
            ),
            array(
                'name' => '用户查询',
                'acts' => array('user', 'index'),
                'url'  => '/user',
            ),

            array(
                'name' => '添加用户组',
                'acts' => array('user-group', 'item'),
                'url'  => '/user-group/item',
            ),

            array(
                'name' => '用户组管理',
                'acts' => array('user-group', 'index'),
                'url'  => '/user-group',
            ),
        ),
    ),

    array(
        'name' => '第三方平台',
        'icon' => 'menu-icon fa fa-external-link',
        'subs'  => array(
            array(
                'name' => '微信公众平台',
                'acts' => array('wechat-thirdparty', 'wechat'),
                'url'  => '/wechat-thirdparty/wechat',
            ),
            array(
                'name' => '微信商家平台',
                'acts' => array('wechat-thirdparty', 'wechatPay'),
                'url'  => '/wechat-thirdparty/wechatPay',
            ),
            array(
                'name' => '微信测试平台',
                'acts' => array('wechat-thirdparty', 'wechatTest'),
                'url'  => '/wechat-thirdparty/wechatTest',
            ),
        ),
    ),
    
        'profile' => array(
        'name' => '个人中心',
        'icon' => 'user',
        'sub'  => array(
            'index'    => array(
                'name' => '个人信息',
                'icon' => '',
            ),
            'password' => array(
                'name' => '密码修改',
                'icon' => '',
            ),
            'logout'   => array(
                'name' => '退出登录',
                'url'  => 'javascript:confirmLogout();',
                'icon' => '',
            ),
        ),
    ),
    
        array(
        'name' => '支付管理',
        'icon' => 'credit-card',
        'subs' => array(
            array(
                'name' => '支付记录',
                'acts' => array('pay', 'index'),
                'url'  => '/pay/index',
            ),
        ),
    ),
);

