<?php

namespace Home\Controller;

class MemberController extends \Think\Controller {

    private $_model = null;

    protected function _initialize() {
        $meta_titles  = array(
            'index' => '用户管理',
            'add'   => '添加用户',
            'edit'  => '修改用户',
        );
        $meta_title   = isset($meta_titles[ACTION_NAME]) ? $meta_titles[ACTION_NAME] : '用户管理';
        $this->assign('meta_title', $meta_title);
        $this->_model = D('Member'); //由于所有的操作都需要用到模型,我们在初始化方法中创建
//        $this->assign('userinfo',login());
    }

    /**
     * 前台用户注册
     */
    public function register() {
        if (IS_POST) {
            //对数据合法性进行验证
            if ($this->_model->create() === false) {
                $this->error($this->_model->getError());
            }
            if ($this->_model->addMember() === false) {
                $this->error($this->_model->getError());
            }

            if ($this->sendMail()===false) {
                $this->errro('邮件发送失败,请到重发邮件页面重新发送', U('Index/index'));
            }
            $this->success('注册成功', U('Index/index'));
        } else {
            $this->display();
        }
    }

    /**
     * 验证注册信息是否已经存在.
     * 用户名
     * 邮箱
     * 手机号码
     * 不能重复
     */
    public function checkByParam() {
        $param = I('get.');
        $flag  = true;
        if ($this->_model->where($param)->count()) {
            $flag = false;
        }
        echo json_encode($flag);
        exit;
    }

    /**
     * 发送验证码,用于注册验证手机号码
     * @param type $telphone
     */
    public function sendSMS($telphone) {
        $code  = \Org\Util\String::randNumber(1000, 9999);
        $param = array(
            'code'    => $code,
            'product' => '仙人跳',
        );

        if (sendSMS($telphone, $param)) {
            //将验证码存放到session中
            $data   = array(
                'code'     => $code,
                'telphone' => $telphone,
            );
            tel_code($data);
            $return = array(
                'status' => 1,
            );
        } else {
            $return = array(
                'status' => 0,
            );
        }
        $this->ajaxReturn($return);
        exit;
    }

    /**
     * 发送邮件,尝试三次,如果三次都失败,就不再尝试
     * @return boolean
     */
    private function sendMail() {
        for ($i = 0; $i < 3;  ++$i) {
            //发送邮件
            $address = I('post.email');
            $subject = '欢迎注册ayiyayo商城';
            $param   = array(
                'email'    => $address,
                'token' => md5(\Org\Util\String::randString(17)),
            );
            $url     = U('active', $param, true, true);
            $content = <<<EMIAL
欢迎注册,请点击以下链接激活账号：
<a href="$url" target="_blank">$url</a>
(如不能打开页面，请复制该地址到浏览器打开)'
EMIAL;
            if (sendMail($address, $content, $subject)){
                //记录数据到数据表
                M('EmailToken')->delete($address);
                M('EmailToken')->add($param);
                return true;
            }
        }
        return false;
    }
    
    public function active($email,$token){
        $model = M('EmailToken');
        //判断数据表中是否有对应记录
        $cond = array(
            'email'=>$email,
            'token'=>$token,
        );
        if(!$model->where($cond)->count()){
            $this->error('验证信息不匹配,或者已经激活成功',U('Index/index'));
        }
        //如果有就激活账户
        if($this->_model->activeMember($email) === false){
            $this->error('激活失败,请稍后再试',U('Index/index'));
        }
        //删除这条记录
        if($model->delete($email) === false){
            $this->error('激活失败,请稍后再试',U('Index/index'));
        }
        $this->success('激活成功',U('Index/index'));
    }
    
    /**
     * 用户登录
     */
    public function login(){
        if(IS_POST){
            if(($userinfo = $this->_model->login()) === false){
                $this->error($this->_model->getError());
            }
            //保存用户信息
            login($userinfo);
            //是否勾选了自动登录
            M('MemberToken')->where(array('member_id'=>$userinfo['id']))->delete();
            if(I('post.remember')){
                $token = array(
                    'member_id'=>$userinfo['id'],
                    'token'=>  createToken(),
                );
                token($token);
                M('MemberToken')->add($token);
            }else{
                token(array());
            }
            
            //将cookie中的购物车放入到数据库中
            /**
             * 从cookie中取出所有的商品
             * 遍历调用数据表中的数据
             */
            D('ShoppingCar')->cookie2db();
            $url = cookie('FORWARD');
            cookie('FORWARD',null);
            if(!$url){
                $url = U('Index/index');
            }
            $this->success('登录成功',$url);
        }else{
            $this->display();
        }
    }
    
    /**
     * 退出
     */
    public function logout(){
        login(array());
        token(array());
        $this->success('退出成功',U('Index/index'));
    }
    
    /**
     * 获取用户信息
     */
    public function userInfo(){
        $userinfo = login();
        $return = array(
            'username'=>$userinfo['username'],
            'member_id'=>$userinfo['id'],
        );
        $this->ajaxReturn($return);
    }

}
