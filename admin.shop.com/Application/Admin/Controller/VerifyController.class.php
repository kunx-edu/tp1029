<?php

namespace Admin\Controller;

class VerifyController extends \Think\Controller {
    /**
     * 展示验证码
     */
    public function verify(){
        $config = array(
            'length'=>4,
        );
        $verify = new \Think\Verify($config);
        $verify->entry();
    }
}
