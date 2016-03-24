<?php

namespace Admin\Controller;

class MemberLevelController extends \Think\Controller {

    private $_model = null;

    protected function _initialize() {
        $meta_titles = array(
            'index'  => '会员等级管理',
            'add'    => '添加会员等级',
            'edit'   => '修改会员等级',
            'delete' => '删除会员等级',
        );
        $meta_title  = isset($meta_titles[ACTION_NAME]) ? $meta_titles[ACTION_NAME] : '会员等级管理';
        $this->assign('meta_title', $meta_title);
        $this->_model = D('MemberLevel'); //由于所有的操作都需要用到模型,我们在初始化方法中创建
    }

    public function index() {
        //获取所有的等级数据
        $this->assign('rows', $this->_model->getList());
        $this->display();
    }

    public function add() {
        if (IS_POST) {
            if($this->_model->create() === false){
                $this->error($this->_model->getError());
            }
            if($this->_model->addMemberLevel() === false){
                $this->error($this->_model->getError());
            }
            $this->success('添加会员等级成功',U('index'),1);
            
        } else {

            $this->display();
        }
    }

    public function edit() {

        $this->display('add');
    }

}
