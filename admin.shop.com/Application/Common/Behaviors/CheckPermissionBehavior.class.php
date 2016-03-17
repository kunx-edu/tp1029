<?php
namespace Common\Behaviors;
class CheckPermissionBehavior extends \Think\Behavior{
    //执行权限的检测
    public function run(&$params) {
        
        /**
         * 自动登陆,获取用户的权限列表
         */
        D('Admin')->autoLogin();
        $paths = paths();
        $ignore = C('ACCESS_ACTIONS');
        $paths = array_merge($paths,$ignore);
        
        
        //paths里面存放的是所有可访问的操作路径,有可能出现重复,但是对业务逻辑无影响
        $path = MODULE_NAME . '/' . CONTROLLER_NAME . '/' . ACTION_NAME;
        if(in_array($path, $paths)){
            return true;
        }else{
            redirect(U('Admin/login'),1,'权限不足');
        }
        
    }

}
