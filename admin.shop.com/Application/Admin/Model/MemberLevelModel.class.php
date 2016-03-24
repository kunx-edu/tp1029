<?php
namespace Admin\Model;

class MemberLevelModel extends \Think\Model{
    protected $_validate = array(
        array('name','require','等级名称必填'),
        array('discount','require','折扣率必填'),
//        array('top','require','积分上限必填'),
        array('bottom','require','积分下限必填'),
    );
    
    /**
     * 添加会员等级
     */
    public function addMemberLevel(){
        unset($this->data['id']);
        return $this->add();
    }
    
    /**
     * 获取会员等级列表
     * @return type
     */
    public function getList(){
        return $this->where('status=1')->select();
    }
}
