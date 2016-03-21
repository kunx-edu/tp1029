<?php
namespace Home\Model;

class AddressModel extends \Think\Model{
    /**
     * 添加新地址
     */
    public function addAddress(){
        $userinfo = login();
        $this->data['member_id'] = $userinfo['id'];
        //是否设置为默认
        $this->data['is_default'] = isset($this->data['is_default'])?1:0;
        if($this->data['is_default']){
            //将其它的设置为非默认
            $this->where(array('member_id'=>$userinfo['id']))->setField('is_default',0);
        }
        return $this->add();
    }
    /**
     * 获取用户的地址列表
     */
    public function getList(){
        $userinfo = login();
        return $this->where(array('member_id'=>$userinfo['id']))->select();
    }
}
