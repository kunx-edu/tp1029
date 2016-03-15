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
        $paths = array();
        $pids = array();
        if($userinfo){
            $admin_id = $userinfo['id'];
            $sql = "SELECT   DISTINCT p.id,path FROM  admin_role AS ar LEFT JOIN role_permission AS rp     ON ar.`role_id` = rp.`role_id`   LEFT JOIN permission AS p ON rp.`permission_id`=p.`id` WHERE admin_id = $admin_id AND path<>''";
            $role_permissions = M()->query($sql);
            foreach($role_permissions as $role_permission){
                $paths[] = $role_permission['path'];
                $pids[] = $role_permission['id'];
            }

            //获取额外权限
            $sql = "SELECT DISTINCT p.id,path FROM  admin_permission AS ap   LEFT JOIN permission AS p     ON ap.`permission_id` = p.`id` WHERE admin_id = $admin_id  AND path <> ''";
            $admin_permissions = M()->query($sql);
            foreach ($admin_permissions as $admin_permission){
                $paths[] =$admin_permission['path'];
                $pids[] = $admin_permission['id'];
            }
        }
        $ignore = C('ACCESS_ACTIONS');
        $paths = array_merge($paths,$ignore);
        
        
        
        //获取菜单列表
        $pid_str = implode(',', $pids);
        $menus = M()->query("SELECT DISTINCT id,NAME,path,parent_id,LEVEL FROM menu AS m LEFT JOIN menu_permission AS mp ON m.id=mp.`menu_id` WHERE LEVEL IN(1,2) AND permission_id IN($pid_str)");
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
