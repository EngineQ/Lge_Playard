<?php
namespace Lge;

if(!defined('LGE')){
    exit('Include Permission Denied!');
}
/**
 * 公司模型。
 *
 * @author John
 */
class Model_Demo_Company extends BaseModelTable
{
    public $table        = 'company';
    public $dbConfigName = 'sqlite_demo';

    /**
     * 获得对象的方法，请使用该方法获得对象.
     *
     * @return Model_Demo_Company
     */
    public static function instance ()
    {
        return self::_instanceInternal(__CLASS__);
    }
}