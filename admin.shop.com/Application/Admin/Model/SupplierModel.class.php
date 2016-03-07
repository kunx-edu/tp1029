<?php

namespace Admin\Model;

class SupplierModel extends \Think\Model{
    protected $_validate = array(
        array('name','require','供货商名称不能为空'),
        array('name','','供货商已存在',self::EXISTS_VALIDATE,'unique'),
    );
}
