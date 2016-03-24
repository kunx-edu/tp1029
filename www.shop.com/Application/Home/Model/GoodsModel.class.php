<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Home\Model;

/**
 * Description of GoodsModel
 *
 * @author qingf
 */
class GoodsModel extends \Think\Model{
    /**
     * 获取指定状态的商品列表
     * @param type $status
     * @param type $limit
     */
    public function getGoodsByStatus($status,$limit=5){
        $cond = array(
            'status'=>array('gt',0),
            'goods_status&'.$status,
        );
        //where status>0 and goods_status&1
        return $this->where($cond)->limit($limit)->order('sort')->select();
    }
    
    
    public function getGoodsInfo($goods_id){
        $cond = array(
            'status'=>1,
        );
        $row= $this->where($cond)->find($goods_id);
        $row['brand_name'] = M('Brand')->getFieldById($row['brand_id'],'name');
        $row['content'] = M('GoodsIntro')->getFieldByGoodsId($goods_id,'content');
        $row['gallery'] = M('GoodsGallery')->where(array('goods_id'=>$goods_id))->getField('path',true);
        $row['logo'] = $row['gallery'][0];
        $member_prices= M('MemberGoodsPrice')->where(array('goods_id'=>$goods_id))->getField('member_level_id,price');
        $member_levels= M('MemberLevel')->where('status=1')->getField('id,name,discount');
        $tmp_prices = array();
        foreach($member_levels as $member_level){
            if(isset($member_prices[$member_level['id']])){
                $tmp_price = my_num_format($member_prices[$member_level['id']]);
            }else{
                $tmp_price=  my_num_format($row['shop_price']*$member_level['discount']/100);
            }
            $tmp_prices[$member_level['id']] = array(
                'price'=>$tmp_price,
                'name'=>$member_level['name'],
            );
        }
        $row['member_prices'] = $tmp_prices;
        /**
         * [
         *  1：[1200,钻石会员]
         * ]
         */
        return $row;
    }
    
    /**
     * 获取购物车中商品的基本信息
     * id name logo shop_price
     */
    public function getShoppingCarInfo(array $goods_ids){
        $cond = array(
            'id'=>array('in',$goods_ids),
            'status'=>1,
            'is_on_sale'=>1,
        );
        return $this->field('id,name,logo,shop_price')->where($cond)->select();
        echo $this->getLastSql();
    }
}
