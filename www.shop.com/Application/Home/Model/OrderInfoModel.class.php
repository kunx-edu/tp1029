<?php

namespace Home\Model;

class OrderInfoModel extends \Think\Model {

    private static $statuses = array(
        0 => '已关闭',
        1 => '待支付',
        2 => '待发货',
        3 => '待确认',
        4 => '已完成',
    );

    public function addOrder() {
        $this->startTrans();
        $userinfo          = login();
        $shopping_car_info = D('ShoppingCar')->getShoppingCarInfo();

        //解决并发问题，检查库存有没有被锁定
        if($this->_checkAllGoodsIsLocked($shopping_car_info)===false){
//            $this->_removeGoodsLock($shopping_car_info);
            $this->rollback();
            return false;
        }
        
        //更新库存
        if ($this->_refreshStock($shopping_car_info) === false) {
            $this->_removeGoodsLock($shopping_car_info);
            $this->rollback();
            return false;
        }

        $address_id   = I('post.address_id');
        //判断是否选择了收货地址
        if(!$address_id){
            $this->error = '请选择收货地址';
            $this->_removeGoodsLock($shopping_car_info);
            $this->rollback();
            return false;
            
        }
        $address_info = M('Address')->find($address_id);
        //保存订单基本信息
        if (($order_id     = $this->_addOrderInfo($shopping_car_info, $userinfo, $address_info)) === false) {
            $this->error = '创建订单失败';
            $this->_removeGoodsLock($shopping_car_info);
            $this->rollback();
            return false;
        }

        //保存订单详情
        if ($this->_addOrderDetail($order_id, $shopping_car_info) === false) {
            $this->error = '保存订单详情失败';
            $this->_removeGoodsLock($shopping_car_info);
            $this->rollback();
            return false;
        }

        //开票
        if (($invoice = $this->_addInvoice($order_id, $shopping_car_info, $userinfo, $address_info)) === false) {
            $this->error = '保存发票失败';
            $this->_removeGoodsLock($shopping_car_info);
            $this->rollback();
            return false;
        }
        //回写到订单表中
        $data = array(
            'id'         => $order_id,
            'invoice_id' => $invoice,
        );
        if ($this->save($data) === false) {
            $this->error = '回写发票失败';
            $this->_removeGoodsLock($shopping_car_info);
            $this->rollback();
            return false;
        }

        //清空购物车
        if (D('ShoppingCar')->cleanShoppingCar() === false) {
            $this->error = '清空购物车失败';
            $this->_removeGoodsLock($shopping_car_info);
            $this->rollback();
            return false;
        }
        
        $this->_removeGoodsLock($shopping_car_info);
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
    public function getPageResult() {
        $userinfo     = login();
        $cond         = array(
            'status'    => array('gt', 0),
            'member_id' => $userinfo['id'],
        );
        //获取所有的支付类型
        $payment_list = M('Payment')->getField('id,name');
        $rows         = $this->where($cond)->order('inputtime desc')->page(I('get.p', 1), C('PAGE_SIZE'))->select();
        //获取到每个订单中商品的logo列表
        foreach ($rows as $key => $value) {
            $value['total_price']  = my_num_format($value['price'] + $value['delivery_price']);
            $value['payment_name'] = $payment_list[$value['pay_type']];
            $value['status_name']  = self::$statuses[$value['status']];
            //取出订单详情
            $order_info_items      = M('OrderInfoItem')->where('order_info_id=' . $value['id'])->getField('goods_id,logo');
            $value['goods_list']   = $order_info_items;
            $rows[$key]            = $value;
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
            $cond  = array(
                'id'    => $item['id'],
                'stock' => array('egt', $item['amount']),
            );
            //where id=111 and stock>=5
            if (!$stock = M('Goods')->where($cond)->count()) {
                $this->error = '库存不够';
                return false;
            }
        }
        //修改所购买商品的库存
        foreach ($shopping_car_info['goods_list'] as $item) {
            //查出所有的商品库存，如果发现库存不够，就返回false
            $cond = array(
                'id' => $item['id'],
            );
            //where id=111 and stock>=5
            if (M('Goods')->where($cond)->setDec('stock', $item['amount']) === false) {
                $this->error = '扣库存失败';
                return false;
            }
        }
        return true;
    }

    /**
     * 用于检查商品有没有被购买。
     * @param type $goods_id
     */
    private function _checkLock($goods_id) {
        $redis = getRedis();
        $key   = 'locked_stock';
        for ($i = 0; $i < 3;  ++$i) {
            if ($flag = $redis->sIsMember($key, $goods_id)) {
                sleep(1);
            } else {
                break;
            }
        }
        if($flag){
            return false;//意味着尝试了三次还没有解锁
        }else{
            return true;//已经解锁
        }
    }
    
    /**
     * 检查所有的商品id是否被锁定，如果没有就锁定之
     * @param type $shopping_car_info
     * @return boolean
     */
    private function _checkAllGoodsIsLocked($shopping_car_info){
        $redis = getRedis();
        $key   = 'locked_stock';
        $goods_ids = array();
        foreach ($shopping_car_info['goods_list'] as $item) {
            if($this->_checkLock($item['id'])===false){
                $this->error = '商品太过热门，请稍后再来';
                return false;
            }
            $goods_ids[] = $item['id'];
        }
        foreach($goods_ids as $goods_id){
            $redis->sAdd($key,$goods_id);
        }
    }
    
    /**
     * 从redis中解锁
     */
    private function _removeGoodsLock($shopping_car_info){
        $redis = getRedis();
        $key   = 'locked_stock';
        foreach ($shopping_car_info['goods_list'] as $item) {
            $redis->srem($key,$item['id']);
        }
    }
    /**
     * 1.遍历每个要购买的商品，判断是否被锁定，如果被锁定，直接返回false，不再执行后续的订单创建流程
     * 2.如果没有被锁定，将商品id存入到redis中，应当创建订单
     * 3.订单创建完成，将商品id从redis中移除
     */

    
    public function cleanTimeoutOrder(){
        //1.查询出来超时的订单
        $cond = array(
            'status'=>1,
            'inputtime'=>array('lt',NOW_TIME-900),//15分钟超时
        );
        $invoice = M('Invoice');
        $order_list = $this->where($cond)->getField('id,invoice_id');
        //4.废除发票
        $invoice_ids = array_values($order_list);
        $invoice->where(array('id'=>array('in',$invoice_ids)))->setField('status',0);
        
        //2.将订单的详情查出来
        $order_ids = array_keys($order_list);
        $order_info_items = M('OrderInfoItem')->where(array('order_info_id'=>array('in',$order_ids)))->getField('id,goods_id,amount');
        
        //3.更改商品的库存
        $model = M('Goods');
        foreach($order_info_items as $goods_info){
            $cond = array('id'=>$goods_info['goods_id']);
            $model->where($cond)->setInc('stock',$goods_info['amount']);
        }
        
    }
}
