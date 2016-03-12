<?php

namespace Admin\Controller;

class GoodsController extends \Think\Controller {

    protected function _initialize() {
        $meta_titles  = array(
            'index'  => '商品管理',
            'add'    => '添加商品',
            'edit'   => '修改商品',
            'delete' => '删除商品',
        );
        $meta_title   = isset($meta_titles[ACTION_NAME]) ? $meta_titles[ACTION_NAME] : '商品管理';
        $this->assign('meta_title', $meta_title);
        $this->_model = D('Goods'); //由于所有的操作都需要用到模型,我们在初始化方法中创建
    }

    /**
     * 商品列表页,有搜索功能
     */
    public function index() {
        
    }

    /**
     * 添加新商品
     */
    public function add() {
        if (IS_POST) {
            var_dump(I('post.'));
        } else {
            //1.读取品牌列表
            $this->assign('brand_list',D('Brand')->getList());
            //2.读取供应商列表
            $this->assign('supplier_list',D('Supplier')->getList());
            //3.获取商品分类列表
            $this->assign('goods_category_list',json_encode(D('GoodsCategory')->getList()));
            $this->display();
        }
    }

}
