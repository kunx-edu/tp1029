<?php

namespace Home\Controller;

class MemberController extends \Think\Controller{
    private $_model = null;

    protected function _initialize() {
        $meta_titles  = array(
            'index'    => '用户管理',
            'add'      => '添加用户',
            'edit'     => '修改用户',
        );
        $meta_title   = isset($meta_titles[ACTION_NAME]) ? $meta_titles[ACTION_NAME] : '用户管理';
        $this->assign('meta_title', $meta_title);
        $this->_model = D('Member'); //由于所有的操作都需要用到模型,我们在初始化方法中创建
    }
    
    /**
     * 前台用户注册
     */
    public function register(){
        if(IS_POST){
            //对数据合法性进行验证
            if($this->_model->create() === false){
                $this->error($this->_model->getError());
            }
            if($this->_model->addMember()===false){
                $this->error($this->_model->getError());
            }
            $this->success('注册成功',U('Index/index'));
        }else{
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
    public function checkByParam(){
        $param = I('get.');
        $flag = true;
        if($this->_model->where($param)->count()){
            $flag=false;
        }
        echo json_encode($flag);
        exit;
    }
    
    /**
     * 发送验证码,用于注册验证手机号码
     * @param type $telphone
     */
    public function sendSMS($telphone) {
        $code = \Org\Util\String::randNumber(1000, 9999);
        $param = array(
            'code'    => $code,
            'product' => '仙人跳',
        );
        
        if (sendSMS($telphone, $param)) {
            //将验证码存放到session中
            $data = array(
                'code'=>$code,
                'telphone'=>$telphone,
            );
            tel_code($data);
            $return = array(
                'status' => 1,
            );
        } else {
//            session('tel_code',null);
            $return = array(
                'status' => 0,
            );
        }
        $this->ajaxReturn($return);
        exit;
    }
}
