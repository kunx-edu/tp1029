<?php

namespace Home\Model;

class MemberModel extends \Think\Model{
    protected $_validate = array(
        array('username','require','用户名必填'),
        array('username','','用户名已存在',self::EXISTS_VALIDATE,'unique'),
        array('password','require','密码必填'),
        array('password','6,20','密码必须是6-20个字符',self::EXISTS_VALIDATE,'length'),
        array('repassword','password','两次密码不一致',self::EXISTS_VALIDATE,'confirm'),
        array('email','email','邮箱不合法'),
        array('email','','邮箱已存在',self::EXISTS_VALIDATE,'unique'),
        array('tel','require','手机号码必填'),
        array('tel','','手机号码已存在',self::EXISTS_VALIDATE,'unique'),
        array('captcha','checkTelCode','手机验证码不正确',self::EXISTS_VALIDATE,'callback'),
        array('checkcode','checkCode','验证码不正确',self::EXISTS_VALIDATE,'callback'),
    );
    
    protected $_auto = array(
        array('salt','\Org\Util\String::randString',self::MODEL_INSERT,'function',array(4)),
        array('add_time',NOW_TIME,self::MODEL_INSERT),
    );


    /**
     * 验证短信验证码是否匹配
     * @param type $tel_code
     * @return boolean
     * TODO:有可能用户的验证码并没有在session中找到,所以就没有tephone和code两个元素,会导致一个notice
     */
    protected function checkTelCode($tel_code) {
        $telphone = I('post.tel');
        $session_data = tel_code();
        tel_code(array());//销毁数据,避免下次还可以使用这个验证码
        if($session_data['telphone']==$telphone && $session_data['code'] == $tel_code){
            return true;
        }else{
            return false;
        }
    }
    /**
     * 验证图片验证码
     * @param type $code
     * @return type
     */
    protected function checkCode($code){
        $verify = new \Think\Verify;
        return $verify->check($code);
    }
    
    /**
     * 添加一条用户数据
     * @return type
     */
    public function addMember(){
        //保存加盐加密的密码
        $this->data['password'] = my_mcrypt($this->data['password'], $this->data['salt']);
        //执行插入
        //返回结果
        return $this->add();
    }
}
