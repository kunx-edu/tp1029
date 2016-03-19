<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
    protected function _initialize() {
        $meta_titles  = array(
            'index' => '京西商城',
            'goods'   => '商品详情',
        );
        $meta_title   = isset($meta_titles[ACTION_NAME]) ? $meta_titles[ACTION_NAME] : '京西商城';
        $this->assign('meta_title', $meta_title);
//        $this->_model = D('Member'); //由于所有的操作都需要用到模型,我们在初始化方法中创建
//        
        //取出所有的商品分类
        $model = D('GoodsCategory');
        $this->assign('goods_categories',$model->getAllCategory());
        //获取页脚使用的帮助文章列表
        $article_list= D('Article')->getHelpArticleList();
        $this->assign('article_list', $article_list);
    }
    /**
     * 站点首页
     */
    public function index(){
        $data = array('name'=>'kunx');
        $this->display();
    }
    
    /**
     * 商品展示页面
     */
    public function goods(){
        $this->display();
    }
}