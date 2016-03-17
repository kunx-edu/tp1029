<?php
namespace Common\Behaviors;
class CheckPermissionBehavior extends \Think\Behavior{
    //执行权限的检测
    public function run(&$params) {
        /**
         * 1.获取是哪个用户
         * 2.获取用户从角色处获取的权限
         * 3.获取用户额外的权限
         * 4.对上面的权限进行合并
         * 5.获取当前路径
         * 6.将忽略路径加到上面的路径列表中
         * 7.判断当前路径是否被允许访问
         * 7.1如果允许,return true
         * 7.2如果不允许,就跳转到后台登陆页面
         */
        
        $userinfo = login();
        if(!$userinfo){
            D('Admin')->autoLogin();
        }
        $paths = paths();
        $pids = pids();
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
