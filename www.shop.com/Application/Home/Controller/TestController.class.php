<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Home\Controller;

/**
 * Description of TestConroller
 *
 * @author qingf
 */
class TestController extends \Think\Controller {

    public function sendSMS($telphone) {
        $param = array(
            'code'    => \Org\Util\String::randNumber(1000, 9999),
            'product' => '仙人跳',
        );
        if (sendSMS($telphone, $param)) {
            $return = array(
                'status' => 1,
            );
        } else {
            $return = array(
                'status' => 0,
            );
        }
        $this->ajaxReturn($return);
        exit;
    }

    public function sendMail() {
        //发送邮件
        $subject = '欢迎注册ayiyayo商城';
        $param   = array(
            'email'    => 'kunx@kunx.org',
            'rand_str' => md5(\Org\Util\String::randString(17)),
        );
        $url     = U('active', $param, true, true);
        echo $url;
        exit;
        $rst     = sendMail('kunx@kunx.org', '欢迎注册仙人跳', 'welcome join us');
        var_dump($rst);
        exit;
    }

}
