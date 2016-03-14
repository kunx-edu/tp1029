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
class AdminModel extends \Think\Model{
    protected $_validate = array(
        array('username','require','用户名不能为空',self::MUST_VALIDATE,'',self::MODEL_BOTH),
        array('username','','用户名已存在',self::MUST_VALIDATE,'unique',self::MODEL_BOTH),
        array('password','require','密码不能为空',self::MUST_VALIDATE,'',self::MODEL_BOTH),
        array('email','require','邮箱不能为空',self::MUST_VALIDATE,'',self::MODEL_BOTH),
        array('email','','邮箱已存在',self::MUST_VALIDATE,'unique',self::MODEL_BOTH),
    );
    protected $_auto = array(
        array('salt','\Org\Util\String::randString',self::MODEL_INSERT,'function'),
        array('add_time',NOW_TIME,self::MODEL_INSERT),
    );
    
    public function addAdmin(){
        $this->startTrans();
        $this->data['password'] = my_mcrypt($this->data['password'], $this->data['salt']);
        //保存管理员基本信息
        if (($admin_id = $this->add()) === false) {
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
    private function _addAdminRole($admin_id){
        $role_ids = I('post.role_id');
        if(!$role_ids){
            return true;
        }
        $data = array();
        foreach($role_ids as $role_id){
            $data[] = array(
                'admin_id'=>$admin_id,
                'role_id'=>$role_id,
            );
        }
        if($data){
            return M('AdminRole')->addAll($data);
        }
    }
    /**
     * 保存管理员-角色关系
     * @param integer $admin_id
     * @return boolean
     */
    private function _addAdminPermission($admin_id){
        $permission_ids = I('post.permission_id');
        if(!$permission_ids){
            return true;
        }
        $data = array();
        foreach($permission_ids as $permission_id){
            $data[] = array(
                'admin_id'=>$admin_id,
                'permission_id'=>$permission_id,
            );
        }
        if($data){
            return M('AdminPermission')->addAll($data);
        }
    }
    
    
}
