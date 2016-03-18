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

}
