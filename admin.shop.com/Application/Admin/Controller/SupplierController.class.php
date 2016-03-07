<?php
namespace Admin\Controller;
class SupplierController extends \Think\Controller{
    protected function _initialize(){
        $meta_titles = array(
            'index'=>'供货商管理',
            'add'=>'添加供货商',
            'edit'=>'修改供货商',
            'delete'=>'删除供货商',
        );
        $meta_title = isset($meta_titles[ACTION_NAME])?$meta_titles[ACTION_NAME]:'供货商管理';
        $this->assign('meta_title',$meta_title);
    }


    /**
     * 展示供货商的列表
     */
    public function index(){
        $model = D('Supplier');
        $this->assign('rows',$model->select());
        $this->display();
    }
    
    /**
     * 新增供货商
     */
    public function add(){
        $this->display();
    }
}
