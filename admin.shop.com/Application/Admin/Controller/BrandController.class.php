<?php

namespace Admin\Controller;

class BrandController extends \Think\Controller {

    private $_model = null;

    protected function _initialize() {
        $meta_titles  = array(
            'index'  => '品牌管理',
            'add'    => '添加品牌',
            'edit'   => '修改品牌',
            'delete' => '删除品牌',
        );
        $meta_title   = isset($meta_titles[ACTION_NAME]) ? $meta_titles[ACTION_NAME] : '品牌管理';
        $this->assign('meta_title', $meta_title);
        $this->_model = D('Brand'); //由于所有的操作都需要用到模型,我们在初始化方法中创建
    }

    /**
     * 展示供货商的列表
     */
    public function index() {
        $keyword = I('get.keyword', '');
        $cond    = array(
            'name' => array('like', '%' . $keyword . '%'),
        );
        $page    = I('get.p', 1);
        $this->assign($this->_model->getPageResult($cond, $page));
        $this->assign('keyword',$keyword);
        $this->display();
    }

    /**
     * 新增供货商
     */
    public function add() {
        //如果提交了,就保存到数据表中
        if (IS_POST) {
            //收集数据 验证数据是否合法
            if ($this->_model->create() === false) {
                $this->error($this->_model->getError());
            }
            //执行数据插入
            if ($this->_model->add() === false) {
                $this->error($this->_model->getError());
            }
            $this->success('添加成功', U('index'));
        } else {
            $this->display();
        }
    }

    /**
     * 完成供货商的修改操作
     * @param integer $id 供货商id.
     */
    public function edit($id) {
        //提交
        if (IS_POST) {
            //验证合法性
            if ($this->_model->create() === false) {
                $this->error($this->_model->getError());
            }
            //执行保存操作失败
            if ($this->_model->save() === false) {
                $this->error($this->_model->getError());
            }
            //成功跳转
            $this->success('修改成功', U('index'));
        } else {
            //展示
            //得到数据表中的数据
            $row = $this->_model->find($id);
            $this->assign('row', $row);
            $this->display('add');
        }
    }

    /**
     * 修改供货商状态,包括逻辑删除
     * @param type $id
     * @param type $status
     */
    public function changeStatus($id, $status = -1) {
        if ($this->_model->changeStatus($id, $status) === false) {
            $this->error($this->_model->getError());
        } else {
            if ($status == -1) {
                $msg = '删除成功';
            } else {
                $msg = '修改成功';
            }
            $this->success($msg, U('index'));
        }
    }

}
