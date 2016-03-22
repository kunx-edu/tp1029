<?php

namespace Home\Controller;

class OrderController extends \Think\Controller {

    public function add() {
        //调用模型，创建出订单基本信息、订单详情、发票
        $model = D('OrderInfo');
        if ($model->create() === false) {
            $this->error($model->getError());
        }
        if ($model->addOrder() === false) {
            $this->error($model->getError());
        }
        $this->success('创建成功',U('Index/flow3'));
        
    }

}
