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
            if ($amount) {
                $cond = array(
                    'goods_id'  => $goods_id,
                    'member_id' => $userinfo['id'],
                );
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

}
