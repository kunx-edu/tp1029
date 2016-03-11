<?php

namespace Admin\Controller;

class GoodsCategoryController extends \Think\Controller {

    private $_model = null;

    protected function _initialize() {
        $meta_titles  = array(
            'index'  => '分类管理',
            'add'    => '添加分类',
            'edit'   => '修改分类',
            'delete' => '删除分类',
        );
        $meta_title   = isset($meta_titles[ACTION_NAME]) ? $meta_titles[ACTION_NAME] : '分类管理';
        $this->assign('meta_title', $meta_title);
        $this->_model = D('GoodsCategory'); //由于所有的操作都需要用到模型,我们在初始化方法中创建
    }

    public function index() {
        $rows = $this->_model->getList();
        $this->assign('rows', $rows); //将所有分类传递给视图
        $this->display();
    }

    /**
     * 添加分类
     */
    public function add() {
        if (IS_POST) {
            //验证数据
            if($this->_model->create() === false){
                $this->error($this->_model->getError());
            }
            //添加数据
            if($this->_model->addCategory() === false){
                $this->error($this->_model->getError());
            }
            //成功后跳转
            $this->success('添加分类成功',U('index'));
        } else {
            //取出所有的分类
            $rows = $this->_model->getList();
            foreach ($rows as $key => $value) {
                $rows[$key]['pId'] = $value['parent_id'];
            }
            $rows = json_encode($rows);
            $this->assign('rows', $rows); //将所有分类传递给视图
            $this->display();
        }
    }
    
    /**
     * 修改分类.
     * @param integer $id
     */
    public function edit($id){
        if(IS_POST){
            //验证数据是否合法
            if($this->_model->create() === false){
                $this->error($this->_model->getError());
            }
            
            if($this->_model->updateCategory() === false){
                $this->error($this->_model->getError());
            }
            $this->success('修改分类成功',U('index'));
        }else{
            //取出所有的分类
            $rows = $this->_model->getList();
            foreach ($rows as $key => $value) {
                $rows[$key]['pId'] = $value['parent_id'];
            }
            $rows = json_encode($rows);
            $this->assign('rows', $rows); //将所有分类传递给视图
            $this->assign('row', $this->_model->find($id)); //将所有分类传递给视图
            $this->display('add');
        }
    }
    
    public function delete($id){
        if($this->_model->deleteCategory($id)===false){
            $this->error('删除分类失败');
        }
        $this->success('删除分类成功',U('index'));
    }

}
