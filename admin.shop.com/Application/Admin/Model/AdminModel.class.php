<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Admin\Model;

/**
 * Description of AdminModel
 *
 * @author qingf
 */
class AdminModel extends \Think\Model {

    protected $_validate = array(
        array('username', 'require', '用户名不能为空', self::EXISTS_VALIDATE, '', self::MODEL_BOTH),
        array('username', '', '用户名已存在', self::EXISTS_VALIDATE, 'unique', self::MODEL_INSERT),
        array('password', 'require', '密码不能为空', self::EXISTS_VALIDATE, '', self::MODEL_BOTH),
        array('email', 'require', '邮箱不能为空', self::EXISTS_VALIDATE, '', self::MODEL_INSERT),
        array('email', '', '邮箱已存在', self::EXISTS_VALIDATE, 'unique', self::MODEL_INSERT),
        array('password', 'require', '密码不能为空', self::EXISTS_VALIDATE, '', 5),
        array('repassword', 'password', '密码必须一致', self::EXISTS_VALIDATE, 'confirm', 5),
    );
    protected $_auto     = array(
        array('salt', '\Org\Util\String::randString', self::MODEL_INSERT, 'function'),
        array('add_time', NOW_TIME, self::MODEL_INSERT),
    );

    /**
     * 添加管理员
     * @return boolean
     */
    public function addAdmin() {
        $this->startTrans();
        $this->data['password'] = my_mcrypt($this->data['password'], $this->data['salt']);
        //保存管理员基本信息
        if (($admin_id               = $this->add()) === false) {
            $this->error = '添加管理员失败';
            $this->rollback();
            return false;
        }

        //保存管理员-角色
        if ($this->_addAdminRole($admin_id) === false) {
            $this->error = '保存管理员角色关联失败';
            $this->rollback();
            return false;
        }

        //保存管理员-权限
        if ($this->_addAdminPermission($admin_id) === false) {
            $this->error = '保存管理员特殊权限关联失败';
            $this->rollback();
            return false;
        }


        $this->commit();
        return true;
    }

    /**
     * 保存管理员-角色关系
     * @param integer $admin_id
     * @return boolean
     */
    private function _addAdminRole($admin_id) {
        $role_ids = I('post.role_id');
        if (!$role_ids) {
            return true;
        }
        $data = array();
        foreach ($role_ids as $role_id) {
            $data[] = array(
                'admin_id' => $admin_id,
                'role_id'  => $role_id,
            );
        }
        if ($data) {
            return M('AdminRole')->addAll($data);
        }
    }

    /**
     * 保存管理员-角色关系
     * @param integer $admin_id
     * @return boolean
     */
    private function _addAdminPermission($admin_id) {
        $permission_ids = I('post.permission_id');
        if (!$permission_ids) {
            return true;
        }
        $data = array();
        foreach ($permission_ids as $permission_id) {
            $data[] = array(
                'admin_id'      => $admin_id,
                'permission_id' => $permission_id,
            );
        }
        if ($data) {
            return M('AdminPermission')->addAll($data);
        }
    }

    /**
     * 获取分页数据
     * @param array $cond
     * @param \Think\Page $page
     * @return array
     */
    public function getPageResult(array $cond = array(), $page = 1) {
        $count     = $this->where($cond)->count();
        $rows      = $this->where($cond)->page($page, C('PAGE_SIZE'))->select();
        $page      = new \Think\Page($count, C('PAGE_SIZE'));
        $page->setConfig('theme', '%HEADER% %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%');
        $page_html = $page->show();

        return array('page_html' => $page_html, 'rows' => $rows);
    }

    /**
     * 编辑管理员
     * @param integer $admin_id
     */
    public function updateAdmin() {
        $request_data = $this->data;
        $this->startTrans();
        //保存管理员-角色
        if ($this->_deleteRole($request_data['id']) === false) {
            $this->error = '删除原有管理员角色关联失败';
            $this->rollback();
            return false;
        }
        if ($this->_addAdminRole($request_data['id']) === false) {
            $this->error = '保存管理员角色关联失败';
            $this->rollback();
            return false;
        }

        //保存管理员-权限
        if ($this->_deletePermission($request_data['id']) === false) {
            $this->error = '删除原有管理员特殊权限关联失败';
            $this->rollback();
            return false;
        }
        if ($this->_addAdminPermission($request_data['id']) === false) {
            $this->error = '保存管理员特殊权限关联失败';
            $this->rollback();
            return false;
        }

        $this->commit();
        return true;
    }

    /**
     * 获取用户详细信息,包括角色和特殊权限
     * @param type $admin_id
     * @return type
     */
    public function getAdminInfo($admin_id) {
        $row = $this->find($admin_id);
        if ($row) {
            $cond                 = array(
                'admin_id' => $admin_id
            );
            $row['roles']         = M('AdminRole')->where($cond)->getField('role_id', true);
            $row['permission_id'] = json_encode(M('AdminPermission')->where($cond)->getField('permission_id', true));
        }
        return $row;
    }

    /**
     * 删除已有的管理员-角色关系
     * @param integer $admin_id
     * @return type
     */
    private function _deleteRole($admin_id) {
        $cond = array(
            'admin_id' => $admin_id
        );
        return M('AdminRole')->where($cond)->delete();
    }

    /**
     * 删除已有的管理员-权限关系
     * @param integer $admin_id
     * @return type
     */
    private function _deletePermission($admin_id) {
        $cond = array(
            'admin_id' => $admin_id
        );
        return M('AdminPermission')->where($cond)->delete();
    }

    /**
     * 删除管理员.
     * @param integer $admin_id
     * @return boolean
     */
    public function deleteAdmin($admin_id) {
        $this->startTrans();
        //删除基本信息
        if ($this->delete($admin_id) === false) {
            $this->error = '删除管理员失败';
            $this->rollback();
            return false;
        }

        //删除管理员-角色信息
        if ($this->_deleteRole($admin_id) === false) {
            $this->error = '删除原有管理员角色关联失败';
            $this->rollback();
            return false;
        }

        //删除管理员-权限信息
        if ($this->_deletePermission($admin_id) === false) {
            $this->error = '删除原有管理员特殊权限关联失败';
            $this->rollback();
            return false;
        }

        $this->commit();
        return true;
    }

    public function login() {

        //验证验证码
        $captcha = I('post.captcha');
        $verify  = new \Think\Verify;
        if ($verify->check($captcha) === false) {
            $this->error = '验证码不正确';
            return false;
        }
        //验证用户名和密码是否为空
        $username = I('post.username');
        $password = I('post.password');
        if (empty($username) || empty($password)) {
            $this->error = '用户名或密码不能为空';
            return false;
        }
        //验证是否有此用户
        if (!$userinfo = $this->getByUsername($username)) {
            $this->error = '用户不存在';
            return false;
        }
        //验证密码是否匹配
        $salt     = $userinfo['salt'];
        $password = my_mcrypt($password, $salt);
        if ($password != $userinfo['password']) {
            $this->error = '密码不正确';
            return false;
        }
        //记录用户最后登录时间和ip
        $data = array(
            'id'              => $userinfo['id'],
            'last_login_time' => NOW_TIME,
            'last_login_ip'   => get_client_ip(1),
        );
        $this->save($data);
        //将用户数据返回给控制器
        return $userinfo;
    }

    /**
     * 获取用户的额外权限
     * @param type $admin_id
     * @return type
     */
    public function getAdminPermission($admin_id) {
        $cond = array(
            'admin_id' => $admin_id,
            'path'     => array('neq', ''),
        );
        return $this->field('DISTINCT id,path')->table('__ADMIN_PERMISSION__ as ap')->join('LEFT JOIN __PERMISSION__ as p ON ap.permission_id=p.id')->where($cond)->select();
    }

    /**
     * 自动登陆
     * @return boolean
     */
    public function autoLogin() {
        //检查是否有session,如果有,就不用自动登陆了
        $userinfo = login();
        if ($userinfo) {
            return true;
        }
        $token = token();
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
        login($userinfo); //保存到session中
        
        $this->setPermissionsToSession();
        

        //更新token
        $data = array(
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
    
    
    public function setPermissionsToSession(){
        $userinfo = login();
        //获取并保存用户的角色拥有的权限
        $paths            = array();
        $pids             = array();
        $role_permissions = D('Role')->getAdminRolePermission($userinfo['id']);
        foreach ($role_permissions as $role_permission) {
            $paths[] = $role_permission['path'];
            $pids[]  = $role_permission['id'];
        }
        //获取用户的额外权限
        $admin_permissions = $this->getAdminPermission($userinfo['id']);
        foreach ($admin_permissions as $admin_permission) {
            $paths[] = $admin_permission['path'];
            $pids[]  = $admin_permission['id'];
        }
        //将用户所拥有的path列表放到session中
        paths($paths);
        pids($pids);
    }
    
    /**
     * 重置密码
     * @return boolean|integer
     */
    public function resetPwd(){
        $this->data['salt'] = \Org\Util\String::randString();
        $this->data['password'] = my_mcrypt($this->data['password'], $this->data['salt']);
        return $this->save();
    }
    
    /**
     * 重置密码
     * @return boolean|integer
     */
    public function updatePwd(){
        $this->data['salt'] = \Org\Util\String::randString();
        $this->data['password'] = my_mcrypt($this->data['password'], $this->data['salt']);
        return $this->save();
    }

}
