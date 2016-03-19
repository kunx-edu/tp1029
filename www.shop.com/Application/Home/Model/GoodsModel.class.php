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
        return $row;
    }
}
