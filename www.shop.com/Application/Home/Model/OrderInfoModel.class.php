<?php

namespace Home\Model;

class OrderInfoModel extends \Think\Model {
    private static $statuses = array(
        0=>'已关闭',
        1=>'待支付',
        2=>'待发货',
        3=>'待确认',
        4=>'已完成',
    );

    public function addOrder() {
        $this->startTrans();
        $userinfo          = login();
        $shopping_car_info = D('ShoppingCar')->getShoppingCarInfo();

        
        //更新库存
        if($this->_refreshStock($shopping_car_info)===false){
            $this->rollback();
            return false;
        }
        
        $address_id   = I('post.address_id');
        $address_info = M('Address')->find($address_id);
        //保存订单基本信息
        if (($order_id     = $this->_addOrderInfo($shopping_car_info, $userinfo, $address_info)) === false) {
            $this->error = '创建订单失败';
            $this->rollback();
            return false;
        }

        //保存订单详情
        if ($this->_addOrderDetail($order_id, $shopping_car_info) === false) {
            $this->error = '保存订单详情失败';
            $this->rollback();
            return false;
        }

        //开票
        if (($invoice = $this->_addInvoice($order_id, $shopping_car_info, $userinfo, $address_info)) === false) {
            $this->error = '保存发票失败';
            $this->rollback();
            return false;
        }
        //回写到订单表中
        $data = array(
            'id'=>$order_id,
            'invoice_id'=>$invoice,
        );
        if($this->save($data)===false){
            $this->error = '回写发票失败';
            $this->rollback();
            return false;
        }
        
        //清空购物车
        if(D('ShoppingCar')->cleanShoppingCar() ===false){
            $this->error = '清空购物车失败';
            $this->rollback();
            return false;
        }

        $this->commit();
        return true;


    }

    /**
     * 保存订单信息
     */
    private function _addOrderInfo($shopping_car_info, $userinfo, $address_info) {
        //订单基本信息
        //获取用户地址信息
//        $address_id = I('post.address_id');
//        $address_info = M('Address')->find($address_id);
        $this->data['sn']             = date('Ymd') . mt_rand(1, 999999); //省
        $this->data['province_name']  = $address_info['province_name']; //省
        $this->data['city_name']      = $address_info['city_name']; //市
        $this->data['area_name']      = $address_info['area_name']; //区县
        $this->data['detail_address'] = $address_info['detail_address']; //详细地址
        $this->data['name']           = $address_info['name']; //收货人
        $this->data['tel']            = $address_info['tel']; //手机号码
        $this->data['member_id']      = $userinfo['id']; //会员
        $delivery_info                = M('Delivery')->field('name,price')->where('id=' . $this->data['delivery_id'])->find();
        $this->data['delivery_name']  = $delivery_info['name'];
        $this->data['delivery_price'] = $delivery_info['price'];


        //总金额
//        $shopping_car_info = D('ShoppingCar')->getShoppingCarInfo();
        $this->data['price']     = $shopping_car_info['total_price'];
        $this->data['inputtime'] = NOW_TIME;
        $this->data['status']    = 1;
        return $this->add();
    }

    /**
     * 添加订单详情数据
     * @param type $order_id
     * @param type $shopping_car_info
     * @return type
     */
    private function _addOrderDetail($order_id, $shopping_car_info) {
        $data = array();
        foreach ($shopping_car_info['goods_list'] as $item) {
            $data[] = array(
                'order_info_id' => $order_id,
                'goods_id'      => $item['id'],
                'amount'        => $item['amount'],
                'goods_name'    => $item['name'],
                'price'         => $item['shop_price'],
                'logo'          => $item['logo'],
                'total_price'   => $item['sub_total'],
            );
        }
        return M('OrderInfoItem')->addAll($data);
    }

    /**
     * 开发票
     * @param type $order_id
     * @param type $shopping_car_info
     * @param type $userinfo
     * @param type $address_info
     * @return type
     */
    private function _addInvoice($order_id, $shopping_car_info, $userinfo, $address_info) {
        $type = I('post.type');
        if ($type == 2) {
            $name = I('post.company_name'); //抬头
        } else {
            $name = $address_info['name'];
        }

        $content = I('post.content');
        switch ($content) {
            /**
             * 小米手机  1999  × 1
             * 小米微波炉 800  × 2
             * 总计：3599
             */
            case 1:
                $content = '';
                foreach ($shopping_car_info['goods_list'] as $item) {
                    $content .= $item['name'] . "\t" . $item['shop_price'] . "\t×\t" . $item['amount'] . "\r\n";
                }

                break;
            case 2:
                $content = "办公用品\r\n";
                break;
            case 3:
                $content = "体育休闲\r\n";
                break;
            case 4:
                $content = "耗材\r\n";
                break;
        }
        $content = $name . "\r\n" . $content . "总计：" . $shopping_car_info['total_price'];
        //准备数据
        $data    = array(
            'name'          => $name,
            'content'       => $content,
            'price'         => $shopping_car_info['total_price'],
            'inputtime'     => NOW_TIME,
            'order_info_id' => $order_id,
            'member_id'     => $userinfo['id'],
        );
        return M('Invoice')->add($data);
    }
    
    /**
     * 获取订单列表
     * @return array
     */
    public function getPageResult(){
        $userinfo = login();
        $cond = array(
            'status'=>array('gt',0),
            'member_id'=>$userinfo['id'],
        );
        //获取所有的支付类型
        $payment_list = M('Payment')->getField('id,name');
        $rows = $this->where($cond)->order('inputtime desc')->page(I('get.p',1),C('PAGE_SIZE'))->select();
        //获取到每个订单中商品的logo列表
        foreach($rows as $key=>$value){
            $value['total_price'] = my_num_format($value['price']+$value['delivery_price']);
            $value['payment_name'] = $payment_list[$value['pay_type']];
            $value['status_name'] = self::$statuses[$value['status']];
            //取出订单详情
            $order_info_items = M('OrderInfoItem')->where('order_info_id='.$value['id'])->getField('goods_id,logo');
            $value['goods_list'] = $order_info_items;
            $rows[$key] = $value;
            
        }
        return $rows;
    }

    
    /**
     * 更新库存数据
     * @param type $order_id
     * @param type $shopping_car_info
     * @return type
     */
    private function _refreshStock($shopping_car_info) {
        foreach ($shopping_car_info['goods_list'] as $item) {
            //查出所有的商品库存，如果发现库存不够，就返回false
            $cond = array(
                'id'=>$item['id'],
                'stock'=>array('egt',$item['amount']),
            );
            //where id=111 and stock>=5
            if(!$stock = M('Goods')->where($cond)->count()){
                $this->error = '库存不够';
                return false;
            }
        }
        //修改所购买商品的库存
        foreach ($shopping_car_info['goods_list'] as $item) {
            //查出所有的商品库存，如果发现库存不够，就返回false
            $cond = array(
                'id'=>$item['id'],
            );
            //where id=111 and stock>=5
            if(M('Goods')->where($cond)->setDec('stock',$item['amount'])===false){
                $this->error = '扣库存失败';
                return false;
            }
        }
        return true;
    }
}
