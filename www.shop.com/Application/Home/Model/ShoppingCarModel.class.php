<?php

namespace Home\Model;

class ShoppingCarModel extends \Think\Model {

    /**
     * 将cookie中的购物车数据入库
     * @return type
     */
    public function cookie2db() {
        //从cookie中取出数据
        $cookie_car = shoppingcar();
        $flag       = true;
        //遍历数据
        foreach ($cookie_car as $goods_id => $amount) {
            //执行数据库操作
            $flag = $flag && $this->addCar($goods_id, $amount);
        }
        //清空cookie中的购物车数据，避免下次登录再次添加
        shoppingcar(array());
        return $flag;
        /**
         * 取出cookie中商品id对应的记录，根据db中的数量和cookie中的数量叠加，形成新的二维数组
         * 删除原来的goods_id对应的记录
         * 添加上面的二维数组
         */
    }

    /**
     * 将cookie中的数据添加到数据库
     * 如果有这个商品，就加数量
     * 如果没有，就加记录
     * @param integer $goods_id
     * @param integer $amount
     * @return boolean|integer
     */
    private function addCar($goods_id, $amount) {
        //判断有没有这个数据
        $userinfo = login();
        $cond     = array(
            'member_id' => $userinfo['id'],
            'goods_id'  => $goods_id,
        );
        if ($this->where($cond)->getField('amount')) {
            return $this->where($cond)->setInc('amount', $amount);
        } else {
            $data = array_merge($cond, array('amount' => $amount));
            return $this->add($data);
        }
    }

    /**
     * 修改购物车商品数量
     * @param type $goods_id
     * @param type $amount
     */
    public function changeAmount($goods_id, $amount) {
        $userinfo = login();
        if ($userinfo) {
            $cond = array(
                'goods_id'  => $goods_id,
                'member_id' => $userinfo['id'],
            );
            if ($amount) {
                $this->where($cond)->setField('amount', $amount);
            } else {
                $this->where($cond)->delete();
            }
        } else {
            $cookie_car = shoppingcar();
            if ($amount) {
                $cookie_car[$goods_id] = $amount;
            } else {
                unset($cookie_car[$goods_id]);
            }
            shoppingcar($cookie_car);
        }
    }
    
    /**
     * 获取购物车信息
     * @return type
     */
    public function getShoppingCarInfo() {
        $userinfo = login();
        if ($userinfo) {
            $model    = M('ShoppingCar');
            $cond     = array(
                'member_id' => $userinfo['id'],
            );
            $car_list = $model->where($cond)->getField('goods_id,amount');
            
            $cond = array(
                'id'=>$userinfo['id'],
            );
            $score = M('Member')->where($cond)->getField('score');
        } else {
            $car_list    = shoppingcar();
            $score = 0;
        }
        
        $goods_ids   = array_keys($car_list);
        $total_price = 0;
        if (empty($goods_ids)) {
            $goods_list = array();
        } else {
            //获取商品的基本信息
            $model      = D('Goods');
            $goods_list = $model->getShoppingCarInfo($goods_ids);
            //取出会员价格
            $cond = array(
                'bottom'=>array('elt',$score),
                'top'=>array('egt',$score),
                'status'=>1,
            );
            $member_level = M('MemberLevel')->where($cond)->field('id,discount')->find();
//            $member_level =['id'=>3,'discount'=>80];
            $price = 0;
            foreach($goods_list as $key=>$value){
                $cond = array(
                    'member_level_id'=>$member_level['id'],
                    'goods_id'=>$value['id'],
                );
                //会员价格表
                $price = M('MemberGoodsPrice')->where($cond)->getField('price');
                if(empty($price)){
                    $price = $member_level['discount'] * $value['shop_price']/100;
                }
                
                $goods_list[$key]['shop_price'] = $price;
            }
            
            
            //展示
            foreach ($goods_list as $key => $value) {
                $value['shop_price'] = my_num_format($value['shop_price']);
                $value['amount']     = $car_list[$value['id']];
                $value['sub_total']  = my_num_format($car_list[$value['id']] * $value['shop_price']);
                $total_price         = $total_price + $value['sub_total'];
                $goods_list[$key]    = $value;
            }
        }
        $total_price = my_num_format($total_price);
        return array('goods_list'=>$goods_list,'total_price'=>$total_price);
    }

    /**
     * 清空购物车
     */
    public function cleanShoppingCar(){
        $userinfo = login();
        $cond = array(
            'member_id'=>$userinfo['id'],
        );
        $this->where($cond)->delete();
    }
}
