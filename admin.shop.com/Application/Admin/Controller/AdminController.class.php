<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Admin\Controller;

/**
 * Description of AdminController
 *
 * @author qingf
 */
class AdminController extends \Think\Controller{
    private $_model = null;

    protected function _initialize() {
        $meta_titles  = array(
            'index'  => '用户管理',
            'add'    => '添加用户',
            'edit'   => '修改用户',
            'delete' => '删除用户',
        );
        $meta_title   = isset($meta_titles[ACTION_NAME]) ? $meta_titles[ACTION_NAME] : '用户管理';
        $this->assign('meta_title', $meta_title);
        $this->_model = D('Admin'); //由于所有的操作都需要用到模型,我们在初始化方法中创建
    }
    public function index(){
        $this->display();
    }
    
    public function add(){
        if(IS_POST){
            if ($this->_model->create() === false) {
                $this->error($this->_model->getError());
            }
            if ($this->_model->addAdmin() === false) {
                $this->error($this->_model->getError());
            }

            $this->success('添加管理员成功', U('index'));
        }else{
            $this->_before_view();
            $this->display();
        }
    }
    
    public function edit($id){
        $this->display('add');
    }
    
    public function delete($id){
        
    }
    
    private function _before_view() {
        //展示所有的角色列表
        $rows = D('Role')->getList();
        $this->assign('roles', $rows);
        //展示所有的权限列表
        $rows = D('Permission')->getList();
        $this->assign('permissions', json_encode($rows));
    }
}
