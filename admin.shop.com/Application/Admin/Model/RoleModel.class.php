<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Admin\Model;

/**
 * Description of RoleModel
 *
 * @author qingf
 */
class RoleModel extends \Think\Model {

    protected $_validate = array(
        array('name', 'require', '角色名称不能为空', self::MUST_VALIDATE, '', self::MODEL_BOTH),
    );

    /**
     * 添加角色
     * 1.保存角色基本信息
     * 2.保存角色和权限的关联关系
     */
    public function addRole() {
        unset($this->data['id']);
        $this->startTrans();
        //新增角色
        if (($role_id = $this->add()) === false) {
            $this->error = '添加角色失败';
            $this->rollback();
            return false;
        }
        //保存角色权限关联关系
        if (($role_id = $this->_addRolePermission($role_id)) === false) {
            $this->error = '保存权限失败';
            $this->rollback();
            return false;
        }
        $this->commit();
        return true;
    }

    /**
     * 
     * @param type $role_id
     */
    private function _addRolePermission($role_id) {
        //1.获取权限数据
        $permission_ids = I('post.permission_id');
        //允许角色不具有任何权限,如果没有,就直接返回成功即可
        if (!$permission_ids) {
            return true;
        }


        $data = array();
        foreach ($permission_ids as $permission_id) {
            $data[] = array(
                'role_id'       => $role_id,
                'permission_id' => $permission_id,
            );
        }
        return M('RolePermission')->addAll($data);
    }

    /**
     * 获取角色列表.
     * @return type
     */
    public function getList() {
        $cond = array(
            'status' => array('gt', 0),
        );
        return $this->where($cond)->order('sort')->select();
    }

    /**
     * 获取角色的详细信息,包括权限
     * @param integer $id
     * @return array
     */
    public function getRole($id) {
        $row = $this->find($id);
        if ($row) {
            $cond                 = array('role_id' => $id);
            $row['permission_id'] = json_encode(M('RolePermission')->where($cond)->select());
        }
        return $row;
    }

    public function updateRole() {
        $request_data = $this->data;
        $this->startTrans();
        //保存基本信息
        if ($this->save() === false) {
            $this->error = '编辑角色失败';
            $this->rollback();
            return false;
        }

        //保存角色权限对应关系
        $cond = array(
            'role_id' => $request_data['id'],
        );
        if(M('RolePermission')->where($cond)->delete()===false){
            $this->error = '删除原权限关系失败';
            $this->rollback();
            return false;
        }
        if($this->_addRolePermission($request_data['id'])===false){
            $this->error = '更新权限关系失败';
            $this->rollback();
            return false;
        }
        $this->commit();
        return true;
    }

}
