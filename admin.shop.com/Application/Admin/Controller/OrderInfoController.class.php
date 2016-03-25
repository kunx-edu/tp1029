<?php

namespace Admin\Controller;

class OrderInfoController extends \Think\Controller {

    private $_model = null;

    protected function _initialize() {
        $meta_titles  = array(
            'index'        => '订单管理',
            'changeStatus' => '修改订单状态',
        );
        $meta_title   = isset($meta_titles[ACTION_NAME]) ? $meta_titles[ACTION_NAME] : '订单管理';
        $this->assign('meta_title', $meta_title);
        $this->_model = D('OrderInfo'); //由于所有的操作都需要用到模型,我们在初始化方法中创建
    }

    /**
     * 展示订单列表，支持分页和搜索功能
     */
    public function index() {
        //1.获取查询条件
        $cond       = array();
        $date_range = I('get.date_range');
        switch ($date_range) {
            case 1:
                $cond['inputtime'] = array('gt', strtotime('-1 day'));
                break;
            case 2:
                $cond['inputtime'] = array('gt', strtotime('-1 week'));
                break;
            case 3:
                $cond['inputtime'] = array('gt', strtotime('-1 month'));
                break;
            case 4:
                $cond['inputtime'] = array('gt', strtotime('-3 month'));
                break;
            case 5:
                $cond['inputtime'] = array('lt', strtotime('-3 month'));
                break;
        }
        $price_range = I('get.price_range');
        switch ($price_range) {
            case 1:
                $cond['price'] = array('lt', 500);
                break;
            case 2:
                $cond['price'] = array('lt', 3000);
                break;
            case 3:
                $cond['price'] = array('gt', 3000);
                break;
        }
        $status = I('get.status');
        if (strlen($status)) {
            $cond['status'] = $status;
        }
        $tel = I('get.tel');
        if ($tel) {
            $cond['tel'] = $tel;
        }

        //2.调用模型，获取当前页的列表和分页代码
        $date_array  = array(
            1 => '一天以内',
            2 => '一周以内',
            3 => '一个月以内',
            4 => '三个月以内',
            5 => '三个月以外',
        );
        $price_array = array(
            1 => '<500',
            2 => '<3000',
            3 => '>3000',
        );

        $statuses = $this->_model->statuses;
        $this->assign('date_array', $date_array);
        $this->assign('price_array', $price_array);
        $this->assign('statuses', $statuses);
        $this->assign($this->_model->getPageResult($cond));
        $this->display();
    }

    /**
     * 发货.
     * TODO:快递数据表的创建,两张表.
     * TODO:支付宝发货接口的集成.
     * @param type $id
     */
    public function send($id) {
        $cond = array(
            'status' => 2,
            'id'     => $id,
        );
        if (IS_POST) {
            //通知支付宝
            //更改状态
            if($this->_model->where($cond)->setField('status', 3)===false){
                $this->error($this->_model->getError());
            }
            $this->success('发货成功',U('index'));
        } else {
            $row = $this->_model->where($cond)->find();
            $this->assign('row', $row);
            $this->display();
        }
    }

}
