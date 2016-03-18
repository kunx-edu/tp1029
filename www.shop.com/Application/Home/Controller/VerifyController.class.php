<?php

namespace Home\Controller;

class VerifyController extends \Think\Controller{
    public function verify(){
        $config = array(
            'length'=>4,
        );
        $verify = new \Think\Verify($config);
        $verify->entry();
    }
}
