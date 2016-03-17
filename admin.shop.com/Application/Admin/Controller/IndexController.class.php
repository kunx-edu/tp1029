<?php
namespace Admin\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function index(){
        $this->display();
    }
    public function top(){
        //获取用户名
        $userinfo = login();
        $this->assign('username', $userinfo['username']);
        $this->display();
    }
    public function menu(){
        $menus = D('Menu')->getAdminMenu();
        $this->assign('menus', $menus);
        $this->display();
    }
    public function main(){
        $this->display();
    }
}