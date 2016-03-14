<?php

namespace Admin\Controller;

class GoodsController extends \Think\Controller {

    private $_model = null;

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
        $keyword = I('get.keyword', '');
        $cond = array();
        if($keyword){
            $cond['name'] = array('like', '%' . $keyword . '%');
        }
        
        $goods_category_id = I('get.goods_category_id');
        if($goods_category_id){
            $cond['goods_category_id'] = $goods_category_id;
        }
        
        $brand_id = I('get.brand_id');
        if($brand_id){
            $cond['brand_id'] = $brand_id;
        }
        
        $goods_status = I('get.goods_status');
        if($goods_status){
            //goods_status & 2
            $cond[] = 'goods_status & ' . $goods_status;
        }
        
        $is_on_sale = I('get.is_on_sale');
        if($is_on_sale){
            $cond['is_on_sale'] = $is_on_sale;
        }
        $page    = I('get.p', 1);
        $rows    = $this->_model->getPageResult($cond, $page);
        $this->assign($rows);
        
        //1.读取品牌列表
        $this->assign('brand_list', D('Brand')->getList());
        //2.读取供应商列表
        $this->assign('supplier_list', D('Supplier')->getList());
        //3.获取商品分类列表
        $goods_category_list = index2assoc(D('GoodsCategory')->getList('id,name'),'id');
        $this->assign('goods_category_list', $goods_category_list);
        $this->assign('goods_status_list', $this->_model->goods_statuses);
        $this->assign('is_on_sale_list', $this->_model->is_on_sales);
        
        
        $this->display();
    }

    /**
     * 添加新商品
     */
    public function add() {
        if (IS_POST) {
            if ($this->_model->create() === false) {
                $this->error($this->_model->getError());
            }
            if ($this->_model->addGoods() === false) {
                $this->error($this->_model->getError());
            }
            $this->success('添加成功', U('index'));
        } else {
            $this->_before_view();
            $this->display();
        }
    }

    /**
     * 编辑页面
     * @param type $id
     */
    public function edit($id) {
        if (IS_POST) {
            if ($this->_model->create() === false) {
                $this->error($this->_model->getError());
            }
            if ($this->_model->updateGoods() === false) {
                $this->error($this->_model->getError());
            }
            $this->success('修改成功', U('index'));
        } else {
            $this->_before_view();
            $this->assign('row', $this->_model->getGoodsInfo($id));
            $this->display('add');
        }
    }

    /**
     * 获取add视图所需要的数据
     */
    private function _before_view() {
        //1.读取品牌列表
        $this->assign('brand_list', D('Brand')->getList());
        //2.读取供应商列表
        $this->assign('supplier_list', D('Supplier')->getList());
        //3.获取商品分类列表
        $this->assign('goods_category_list', json_encode(D('GoodsCategory')->getList()));
    }

    /**
     * 删除商品
     * @param integer $id.
     */
    public function delete($id) {
        if ($this->_model->deleteGoods($id) === false) {
            $this->error($this->_model->getError());
        } else {
            $this->success('删除成功', U('index'));
        }
    }

}
