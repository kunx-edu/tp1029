<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Admin\Model;

/**
 * Description of OrderInfoModel
 *
 * @author qingf
 */
class OrderInfoModel extends \Think\Model {

    public $statuses = array(
        0 => '已关闭',
        1 => '待支付',
        2 => '待发货',
        3 => '待确认',
        4 => '已完成',
    );
    
    public function getPageResult(array $cond=array()){
        //获取总行数
        $count = $this->where($cond)->count();
        //获取分页代码
        $page = new \Think\Page($count,C('PAGE_SIZE'));
        $page->setConfig('theme', C('PAGE_THEAM'));
        $page_html = $page->show();
        //获取当前页内容
        $rows = $this->where($cond)->select();
        //获取到所有的支付方式
        $pay_type_list = M('Payment')->where('status=1')->getField('id,name');
        foreach($rows as $key=>$value){
            $value['payment_name'] = $pay_type_list[$value['pay_type']];
            $rows[$key] = $value;
        }
        //返回数据
        return array('page_html'=>$page_html,'rows'=>$rows);
    }

}
