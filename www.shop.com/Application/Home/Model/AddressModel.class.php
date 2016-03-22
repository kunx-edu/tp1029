<?php

namespace Home\Model;

class AddressModel extends \Think\Model {

    /**
     * 添加新地址
     */
    public function addAddress() {
        $userinfo                 = login();
        $this->data['member_id']  = $userinfo['id'];
        //是否设置为默认
        $this->data['is_default'] = isset($this->data['is_default']) ? 1 : 0;
        if ($this->data['is_default']) {
            $this->_cancelDefault();
        }
        return $this->add();
    }

    /**
     * 获取用户的地址列表
     */
    public function getList() {
        $userinfo = login();
        return $this->where(array('member_id' => $userinfo['id']))->select();
    }

    public function updateAddress() {
        //判断是否默认，如果默认，将所有的其它地址设为0
        $this->data['is_default'] = isset($this->data['is_default']) ? 1 : 0;
        if ($this->data['is_default']) {
            $this->_cancelDefault();
        }
        //保存当前的
        return $this->save();
    }

    /**
     * 取消当前用户的收货地址中的默认地址
     */
    private function _cancelDefault() {
        $userinfo = login();
        $cond     = array(
            'member_id' => $userinfo['id'],
        );
        $this->where($cond)->setField('is_default', 0);
    }

}
