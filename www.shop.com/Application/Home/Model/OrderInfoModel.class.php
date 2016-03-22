<?php

namespace Home\Model;

class OrderInfoModel extends \Think\Model {

    public function addOrder() {
        $this->startTrans();
        $userinfo          = login();
        $shopping_car_info = D('ShoppingCar')->getShoppingCarInfo();

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

}