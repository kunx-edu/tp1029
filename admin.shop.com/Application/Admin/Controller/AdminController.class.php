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
class AdminController extends \Think\Controller {

    private $_model = null;

    protected function _initialize() {
        $meta_titles  = array(
            'index'  => '管理员管理',
            'add'    => '添加管理员',
            'edit'   => '修改管理员',
            'delete' => '删除管理员',
        );
        $meta_title   = isset($meta_titles[ACTION_NAME]) ? $meta_titles[ACTION_NAME] : '管理员管理';
        $this->assign('meta_title', $meta_title);
        $this->_model = D('Admin'); //由于所有的操作都需要用到模型,我们在初始化方法中创建
    }

    /**
     * 管理员列表
     */
    public function index() {
        $keyword = I('get.keyword');
        $cond    = array();
        if ($keyword) {
            $cond['username'] = array('like', '%' . $keyword . '%');
        }

        $this->assign($this->_model->getPageResult($cond, I('get.p')));
        $this->display();
    }

    /**
     * 添加管理员
     */
    public function add() {
        if (IS_POST) {
            if ($this->_model->create() === false) {
                $this->error($this->_model->getError());
            }
            if ($this->_model->addAdmin() === false) {
                $this->error($this->_model->getError());
            }

            $this->success('添加管理员成功', U('index'));
        } else {
            $this->_before_view();
            $this->display();
        }
    }

    /**
     * 修改管理员
     * @param type $id
     */
    public function edit($id) {
        if (IS_POST) {
            if ($this->_model->create() === false) {
                $this->error($this->_model->getError());
            }
            if ($this->_model->updateAdmin() === false) {
                $this->error($this->_model->getError());
            }

            $this->success('修改管理员成功', U('index'));
        } else {
            $this->_before_view();
            $this->assign('row', $this->_model->getAdminInfo($id));
            $this->display('add');
        }
    }

    public function delete($id) {
        if ($this->_model->deleteAdmin($id) === false) {
            $this->error($this->_model->getError());
        }

        $this->success('删除管理员成功', U('index'));
    }

    /**
     * 后台管理员登陆
     */
    public function login() {
        if (IS_POST) {
            if (($userinfo = $this->_model->login()) === false) {
                $this->error($this->_model->getError());
            }
            //将用户数据保存
            //将用户的信息保存到session中
            login($userinfo);
            //获取并保存用户的角色拥有的权限
            $paths            = array();
            $pids             = array();
            $role_permissions = D('Role')->getAdminRolePermission($userinfo['id']);
            foreach ($role_permissions as $role_permission) {
                $paths[] = $role_permission['path'];
                $pids[]  = $role_permission['id'];
            }
            //获取用户的额外权限
            $admin_permissions = $this->_model->getAdminPermission($userinfo['id']);
            foreach ($admin_permissions as $admin_permission) {
                $paths[] = $admin_permission['path'];
                $pids[]  = $admin_permission['id'];
            }
            //将用户所拥有的path列表放到session中
            paths($paths);
            pids($pids);
//            var_dump($paths);
//            session('paths',$paths);
//            session('pids',$pids);
//            $paths = array_merge($paths,$ignore);
            //取出菜单列表
            exit;
            //判断是否要自动登陆
            if (I('post.remember')) {
                //保存cookie和数据表中
                $data = array(
                    'admin_id' => $userinfo['id'],
                    'token'    => createToken(),
                );
                //存到数据表中
                M('AdminToken')->add($data);
                //存cookie
                token($data);
            } else {
                cookie('token', null);
            }

            $this->success('登录成功', U('Index/index'));
        } else {
            $this->display();
        }
    }

    /**
     * 获取角色和权限列表
     */
    private function _before_view() {
        //展示所有的角色列表
        $rows = D('Role')->getList();
        $this->assign('roles', $rows);
        //展示所有的权限列表
        $rows = D('Permission')->getList();
        $this->assign('permissions', json_encode($rows));
    }

    public function logout() {
        $userinfo = login();
        $admin_id = $userinfo['id'];
        //删除token数据表中当前用户的数据
        M('AdminToken')->where(array('admin_id' => $admin_id))->delete();
        //删除cookie中用户的数据
        cookie(null);
        //删除session
        session(null);
        $this->success('退出成功', U('login'));
    }

}
