<?php
namespace Lge;

if(!defined('LGE')){
	exit('Include Permission Denied!');
}

/**
 * 验证码管理.
 *
 */
class Controller_Valimage extends AceAdmin_BaseControllerAuth
{
    /**
     * 显示验证码
     */
    public function index()
    {
        header('Content-Type: image/jpg');
        importLib('Image.ValiImage');
        $valiImage = new ValiImage();
        $this->_session['valicode'] = $valiImage->createImage();
    }

}
