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
//        $start = microtime(true);
        //将不常变动的数据缓存下来
        if(!$goods_categories=S('goods_categories')){
            //取出所有的商品分类
            $model = D('GoodsCategory');
            $goods_categories = $model->getAllCategory();
            S('goods_categories',$goods_categories);
        }
        $this->assign('goods_categories',$goods_categories);
        
        //获取页脚使用的帮助文章列表
        if(!$article_list=S('article_list')){
            $article_list= D('Article')->getHelpArticleList();
            S('article_list',$article_list);
        }
        $this->assign('article_list', $article_list);
        $end = microtime(true);
//        echo $end-$start;
//        exit;
    }
    /**
     * 站点首页
     */
    public function index(){
//        $data = array('name'=>'kunx');
        //取出商品列表：热卖 新品 精品
        $model = D('Goods');
        
        $data = array(
            'new_list'=>$model->getGoodsByStatus(1),
            'hot_list'=>$model->getGoodsByStatus(2),
            'best_list'=>$model->getGoodsByStatus(4),
            
        );
        $this->assign($data);
        $this->display();
    }
    
    /**
     * 商品展示页面
     */
    public function goods($id){
        $model = D('Goods');
        $row = $model->getGoodsInfo($id);
        $this->assign('row',$row);
        $this->display();
    }
}