<?php
namespace Lge;

if(!defined('LGE')){
	exit('Include Permission Denied!');
}

/**
 * 后台首页
 */
class Controller_Default extends AceAdmin_BaseControllerAuth
{
    /**
     * 首页展示
     */
    public function index()
    {
    	$this->assigns(array(
        	'mainTpl' => 'default/index'
        ));
        $this->display('index');
    }
}
