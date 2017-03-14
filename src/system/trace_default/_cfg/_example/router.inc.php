<?php
/**
 * 路由规则配置文件，请注意优先级：配置项在前面的优先于后面的进入匹配判断。
 *
 * @author john
 */
return array(
    /**
     * URI解析规则，用于将前端URI转换为内部可识别的GET变量(主要用于SEO或者请求转发到特定的控制器中)。
     */
    'uri' => array(
        // 示例：http://xxx.xxx.xxx/user/list/?type=1&page=2 => http://xxx.xxx.xxx/?__c=user&__a=list&type=1&page=2
        '/\/(\w+)\/(\w+)[\/]*/'  => '/?__c=$1&__a=$2',
        '/\/(\w+)[\/]*/'         => '/?__c=$1&__a=index',
    ),

    /**
     * 连接转换规则，用于将页面特定规则的连接转换为伪静态连接形式(主要用于SEO)。
     */
    'url' => array(
        // 示例：http://xxx.xxx.xxx/?__c=user&__a=list&type=1&page=2 => http://xxx.xxx.xxx/user/list/?type=1&page=2
        '/\/*(\w+\.php){0,1}\?\_\_s=(\w+)\&\_\_c=(\w+)\&\_\_a=(\w+)[\&]*/' => '/$3/$4/?__s=$2&',
        '/\/*(\w+\.php){0,1}\?\_\_c=(\w+)\&\_\_a=(\w+)[\&]*/'              => '/$2/$3/?',
        '/\/*(\w+\.php){0,1}\?\_\_c=(\w+)[\&]*/'                           => '/$2/?',
    ),
);