<?php

namespace Home\Controller;

class OrderController extends \Think\Controller {
    private $_model = null;

    protected function _initialize() {
        $meta_titles      = array(
            'add' => '创建订单',
            'cleanTimeoutOrder' => '清空超时订单',
        );
        $meta_title       = isset($meta_titles[ACTION_NAME]) ? $meta_titles[ACTION_NAME] : '创建订单';
        $this->assign('meta_title', $meta_title);
        $this->_model = D('OrderInfo');
    }

    public function add() {
        //调用模型，创建出订单基本信息、订单详情、发票
        if ($this->_model->create() === false) {
            $this->error($model->getError());
        }
        if ($this->_model->addOrder() === false) {
            $this->error($model->getError());
        }
        $this->success('创建成功',U('Index/flow3'));
        
    }
    
    /**
     * 清空超时订单。
     */
    public function cleanTimeoutOrder(){
        $this->_model->cleanTimeoutOrder();
    }

}
