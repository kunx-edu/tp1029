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
        $admin_id = $userinfo['id'];
        $sql = "SELECT   DISTINCT path FROM  admin_role AS ar LEFT JOIN role_permission AS rp     ON ar.`role_id` = rp.`role_id`   LEFT JOIN permission AS p ON rp.`permission_id`=p.`id` WHERE admin_id = $admin_id AND path<>''";
        $role_permissions = M()->query($sql);
        $paths = array();
        foreach($role_permissions as $role_permission){
            $paths[] = $role_permission['path'];
        }
        
        //获取额外权限
        $sql = "SELECT DISTINCT path FROM  admin_permission AS ap   LEFT JOIN permission AS p     ON ap.`permission_id` = p.`id` WHERE admin_id = $admin_id  AND path <> ''";
        $admin_permissions = M()->query($sql);
        foreach ($admin_permissions as $admin_permission){
            $paths[] =$admin_permission['path'];
        }
        $ignore = C('ACCESS_ACTIONS');
        $paths = array_merge($paths,$ignore);
        
        
        
        
        //获取菜单列表
        $menus = M()->query('SELECT * FROM menu WHERE `level` IN (1,2)');
        session('menus',$menus);
        //paths里面存放的是所有可访问的操作路径,有可能出现重复,但是对业务逻辑无影响
        $path = MODULE_NAME . '/' . CONTROLLER_NAME . '/' . ACTION_NAME;
        if(in_array($path, $paths)){
            return true;
        }else{
            redirect(U('Admin/login'),1,'权限不足');
        }
        
    }

}
