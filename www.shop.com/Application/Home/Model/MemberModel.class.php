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
    
    /**
     * 通过邮箱地址激活账户
     * @param type $email
     * @return type
     */
    public function activeMember($email){
        $cond = array(
            'email'=>$email,
        );
        return $this->where($cond)->setField('status',1);
    }
    
    /**
     * 用户登录
     */
    public function login(){
        $username = I('post.username');
        $password = I('post.password');
        $captcha = I('post.checkcode');
        $verify = new \Think\Verify;
        if($verify->check($captcha) === false){
            $this->error = '验证码不正确';
            return false;
        }
        $cond = array(
            'username'=>$username,
            'status'=>1,
        );
        $userinfo = $this->where($cond)->find();
        if(empty($userinfo)){
            $this->error = '用户不存在';
            return false;
        }
        
        $password = my_mcrypt($password, $userinfo['salt']);
        if($password !== $userinfo['password']){
            $this->error = '密码错误';
            return false;
        }
        //记录最后登录时间和ip
        $data = array(
            'last_login_time'=>NOW_TIME,
            'last_login_ip'=>  get_client_ip(1),
            'id'=>$userinfo['id'],
        );
        $this->save($data);
        return $userinfo;
    }
    
    /**
     * 自动登录
     * 无需将cookie放入数据库，因为只要是cookie中有信息，会自动登录，购物车数据会自动添加到数据库中
     * @return boolean
     */
    public function autoLogin(){
        //检查是否有session,如果有,就不用自动登陆了
        $userinfo = login();
        if ($userinfo) {
            return true;
        }
        $token = token();
        if (!$token) {
            return false;
        }
        

        //判断token是否合法
        if (!M('MemberToken')->where($token)->count()) {
            return false;
        }
        //获取用户信息,保存到session中
        $userinfo = M('Member')->find($token['member_id']);
        login($userinfo); //保存到session中
        

        //更新token
        $data = array(
            'member_id' => $userinfo['id'],
            'token'    => createToken(),
        );
        token($data);
        //记录token到数据表
        $cond = array(
            'member_id' => $userinfo['id'],
        );
        M('MemberToken')->where($cond)->save($data);
    }
}
