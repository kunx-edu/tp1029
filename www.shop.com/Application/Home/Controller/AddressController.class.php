<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Home\Controller;

/**
 * Description of AddressController
 *
 * @author qingf
 */
class AddressController extends \Think\Controller{
    private $_model = null;

    protected function _initialize() {
        $meta_titles      = array(
            'index' => '我的地址',
        );
        $meta_title       = isset($meta_titles[ACTION_NAME]) ? $meta_titles[ACTION_NAME] : '我的地址';
        $this->assign('meta_title', $meta_title);
        //将不常变动的数据缓存下来
        if (!$goods_categories = S('goods_categories')) {
            //取出所有的商品分类
            $model            = D('GoodsCategory');
            $goods_categories = $model->getAllCategory();
            S('goods_categories', $goods_categories);
        }
        $this->assign('goods_categories', $goods_categories);

        //获取页脚使用的帮助文章列表
        if (!$article_list = S('article_list')) {
            $article_list = D('Article')->getHelpArticleList();
            S('article_list', $article_list);
        }
        $this->assign('article_list', $article_list);

        $this->assign('userinfo', login());
        $this->_model = D('Locations');
        $this->assign('show_cat_list',false);
    }
    
    public function index(){
        //获取省级菜单
        $this->assign('provinces', $this->_model->getTopLocations());
        $this->assign('rows',D('Address')->getList());
        $this->display();
    }
    
    /**
     * 通过父级id获取下级列表
     */
    public function getLoctionsByParentId($parent_id){
        $this->ajaxReturn($this->_model->getLoctionsByParentId($parent_id));
    }
    
    /**
     * 新增收货地址
     */
    public function add(){
        if(IS_POST){
            $model = D('Address');
            if($model->create() ===false){
                $this->error($model->getError());
            }
            if($model->addAddress() ===false){
                $this->error($model->getError());
            }
            $this->success('添加成功');
        }else{
            $this->error('请求非法');
        }
    }
}
