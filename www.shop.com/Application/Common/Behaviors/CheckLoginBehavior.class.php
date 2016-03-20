<?php

namespace Common\Behaviors;
class CheckLoginBehavior extends \Think\Behavior{
    /**
     * 进行自动登录。
     * @param type $params
     */
    public function run(&$params) {
        $model = D('Member');
        $model->autoLogin();
    }

}
