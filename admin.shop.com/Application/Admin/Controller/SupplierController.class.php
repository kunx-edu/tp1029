<?php
namespace Admin\Controller;
class SupplierController extends \Think\Controller{
    /**
     * 展示供货商的列表
     */
    public function index(){
        $model = D('Supplier');
        $this->assign('rows',$model->select());
        $this->display();
    }
}
