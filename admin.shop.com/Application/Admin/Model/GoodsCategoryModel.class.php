<?php
namespace Admin\Model;

class GoodsCategoryModel extends \Think\Model{
    public function getList(){
        return $this->select();
    }
}
