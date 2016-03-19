<?php

namespace Home\Model;

class GoodsCategoryModel extends \Think\Model{
    public function getAllCategory(){
        $cond = array(
            'status'=>array('gt',0),
            'level'=>array('elt',3),
        );
        return $this->where($cond)->select();
        
    }
}
