<?php

namespace Common\Behaviors;

class CheckLoginBehavior extends \Think\Behavior {

    /**
     * 验证用户是否有登陆,如果没有,就判断是否有合适的token,如果有,就获取用户信息,保存到session中,并重新生成token
     * @param type $params
     */
    public function run(&$params) {
        $userinfo = login();
        if ($userinfo) {
            return true;
        }
        $token = cookie('token');
        if (!$token) {
            return false;
        }

        //判断token是否合法
        $token = token();
        if (!M('AdminToken')->where($token)->count()) {
            return false;
        }
        //获取用户信息,保存到session中
        $userinfo = M('Admin')->find($token['admin_id']);
        login($userinfo);//保存到session中
        
        //更新token
        $data     = array(
            'admin_id' => $userinfo['id'],
            'token'    => createToken(),
        );
        token($data);
        //记录token到数据表
        $cond = array(
            'admin_id' => $userinfo['id'],
        );
        M('AdminToken')->where($cond)->save($data);
    }

}
