<?php
namespace Lge;

if (!defined('LGE')) {
	exit('Include Permission Denied!');
}

class Controller_Database extends Controller_Base
{
    
    /**
     * 默认入口函数.
     *
     * @return void
     */
    public function index()
    {
        /**
         * 这里采用的是SQLite数据库操作来演示该框架的数据库操作，其他数据库如MySQL、MSSQL也是一样的操作方式.
         */
        // 初始化数据库操作对象
        $db = Instance::database();
        // 设置出错时不立即停止执行
        $db->setHalt(false);
        // 设置调试模式(内部会记录详细的SQL执行信息)
        $db->setDebug(true);

        /**
         * 删除数据表
         */
        $sql = "DROP TABLE IF EXISTS `company`";
        if ($db->query($sql)) {
            echo "drop table 'company' successfully!\n";
        } else {
            echo "drop table 'company' failed!\n";
        }
        $sql = "DROP TABLE IF EXISTS `user`";
        if ($db->query($sql)) {
            echo "drop table 'user' successfully!\n";
        } else {
            echo "drop table 'user' failed!\n";
        }

        echo "\n";

        /**
         * 创建数据表
         */
        $sql = <<<MM
        CREATE TABLE company (
           id      INT PRIMARY KEY NOT NULL,
           uid     INT    NOT NULL,
           number  INT    NOT NULL,
           salary  REAL
        );
MM;
        if ($db->query($sql)) {
            echo "create table 'company' successfully!\n";
        } else {
            echo "create table 'company' failed!\n";
        }

        echo "==============================\n";

        $sql = <<<MM
        CREATE TABLE `user` (
           uid      INT PRIMARY KEY NOT NULL,
           name     CHAR(50) NOT NULL,
           age      INT      NOT NULL,
           address  CHAR(50)
        );
MM;
        if ($db->query($sql)) {
            echo "create table 'user' successfully!\n";
        } else {
            echo "create table 'user' failed!\n";
        }

        echo "==============================\n";

        // 单条插入数据
        $data = array(
            'uid'     => 1,
            'name'    => 'John',
            'age'     => 29,
            'address' => '',
        );
        $db->insert('user', $data);
        // 重复写入数据，会返回失败
        if ($db->insert('user', $data)) {
            echo "repeat insert successfully!\n";
        } else {
            echo "repeat insert failed!\n";
        }
        // 重复写入数据，如果存在已有数据，则忽略不写入
        if ($db->insert('user', $data, 'ignore')) {
            echo "repeat insert with ignore successfully!\n";
        } else {
            echo "repeat insert failed!\n";
        }
        // 重复写入数据，如果存在已有数据，则更新已有数据
        $data['name'] = 'Johnson';
        if ($db->insert('user', $data, 'replace')) {
            echo "repeat insert with replace successfully!\n";
        } else {
            echo "repeat insert failed!\n";
        }

        echo "==============================\n";

        // 批量写入数据
        $data = array(
            'uid'     => 2,
            'name'    => 'John_2',
            'age'     => 29,
            'address' => '',
        );
        $db->insert('user', $data);
        $list = array(
            array(
                'id'      => 1,
                'uid'     => 1,
                'salary'  => 5000,
                'number'  => '1001',
            ),
            array(
                'id'      => 2,
                'uid'     => 2,
                'salary'  => 6000,
                'number'  => '1002',
            ),
        );
        if ($db->batchInsert('company', $list)) {
            echo "batch insert successfully!\n";
        } else {
            echo "batch insert failed!\n";
        }

        echo "==============================\n";

        $result = $db->update('company', array('salary' => 8000), array('uid=?', 1));
        if ($result) {
            echo "update successfully!\n";
        } else {
            echo "update failed!\n";
        }

        echo "==============================\n";

        /**
         * 查询数据，注意查询条件参数
         */
        echo "getOne:\n";
        print_r($db->getOne('SELECT * FROM `user` WHERE `name`=?', array('Johnson')));

        echo "getAll:\n";
        print_r($db->getAll('SELECT * FROM `company` WHERE `salary`>:minSalary', array(':minSalary' => 1000)));

        echo "count:\n";
        var_dump($db->count('user', 1, 'name'));

        echo "select1:\n";
        print_r($db->select(array('user'), array('*'), array('name=?', array('Johnson'))));

        echo "select2:\n";
        print_r($db->select('user', array('uid', 'name'), array('name=?', 'Johnson')));

        echo "select3:\n";
        print_r($db->select('user', 'uid, name', array('name' => 'Johnson')));

        echo "select4:\n";
        print_r($db->select('user', '*', 'age>18'));

        echo "select5:\n";
        print_r($db->select(
            'company LEFT JOIN user ON(company.uid=user.uid)',
            'company.*,user.name,user.age',
            array('user.age>? and company.salary>?', '18', 3000)
        ));

        echo "select6:\n";
        print_r($db->select('user'));

        echo "==============================\n";

        /*
        // 删除数据表
        $sql = "DROP TABLE IF EXISTS `company`";
        if ($db->query($sql)) {
            echo "drop table 'company' successfully!\n";
        } else {
            echo "drop table 'company' failed!\n";
        }
        $sql = "DROP TABLE IF EXISTS `user`";
        if ($db->query($sql)) {
            echo "drop table 'user' successfully!\n";
        } else {
            echo "drop table 'user' failed!\n";
        }

        echo "\n";
        */
        // 获取年龄为18岁的所有女性
        Instance::table('lge_user')->getAll('*', array(
            'age'    => 18,
            'gender' => 0,
        ));

        $fields    = 'uid,nickname';
        $condition = array("gender=? and age>? and uid in(1,2,3) and name like '%john%'", $_GET['gender'], $_GET['age']);
        $groupby   = null;
        $orderby   = 'uid desc';
        $start     = 0;
        $limit     = 100;
        $arrayKey  = 'uid';
        Instance::table('lge_user')->getAll($fields, $condition, $groupby, $orderby, $start, $limit, $arrayKey);

        // 查询指定某一时间段高于某个价格的用户信息及用户地址
        $tables    = array(
            'user u',
            'left join order o on(o.uid=u.uid)',
            'left join order_address oa on(oa.order_id=o.id)',
        );
        $fields    = 'u.uid,u.nickname,oa.order_id,oa.address';
        $condition = array("o.order_price>? and o.create_time between ? and ?", $_GET['price'], $_GET['start_time'], $_GET['end_time']);
        $groupby   = null;
        $orderby   = 'oa.order_id desc';
        $start     = 0;
        $limit     = 100;
        $arrayKey  = 'oa.order_id';
        Instance::table($tables)->getAll($fields, $condition, $groupby, $orderby, $start, $limit, $arrayKey);

        /**
         * 表前缀示例
         */
        // 1、单表查询
        Instance::table('_user')->getOne('*', 'uid=1');

        // 2、联表查询
        $tables    = array(
            '_user u',
            'left join _user_address ua on(ua.uid=u.uid)',
        );
        $fields    = 'u.uid,u.nickname,ua.address';
        $condition = array('uid' => $_GET['uid']);
        Instance::table($tables)->getAll($fields, $condition);

        $db   = Instance::database();
        $sqls = $db->getQueriedSqls();
        print_r($sqls);
    }
}