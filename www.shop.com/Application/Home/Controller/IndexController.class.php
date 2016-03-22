<?php

namespace Home\Controller;

use Think\Controller;

class IndexController extends Controller {

    private $_model = null;

    protected function _initialize() {
        $meta_titles      = array(
            'index' => '京西商城',
            'goods' => '商品详情',
        );
        $meta_title       = isset($meta_titles[ACTION_NAME]) ? $meta_titles[ACTION_NAME] : '京西商城';
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
        $this->_model = D('Goods');
        if(ACTION_NAME == 'index'){
            $this->assign('show_cat_list',true);
        }else{
            $this->assign('show_cat_list',false);
        }
    }

    /**
     * 站点首页
     */
    public function index() {
        //取出商品列表：热卖 新品 精品
        $model = D('Goods');
        //打印session和cookie
        $data  = array(
            'new_list'  => $model->getGoodsByStatus(1),
            'hot_list'  => $model->getGoodsByStatus(2),
            'best_list' => $model->getGoodsByStatus(4),
        );
        $this->assign($data);

        $this->display();
    }

    /**
     * 商品展示页面
     */
    public function goods($id) {
        $model = D('Goods');
        $row   = $model->getGoodsInfo($id);
        $this->assign('row', $row);
        $this->display();
    }

    public function refreshGoodsClick($goods_id) {

        //将数据存放在redis中
        $redis   = getRedis();
        //goods_clicks
        $key     = 'goods_clicks';
        $memeber = $goods_id;
//        var_dump($memeber);
//        exit;
        //获取点击数
        $click   = $redis->zScore($key, $memeber);
        //增加点击数
        $click   = $redis->zIncrBy($key, 1, $memeber);
        $this->ajaxReturn($click);

        exit;


        $model = M('GoodsClick');
        //增加商品点击数
        //先获取这个商品的点击数
        $click = $model->getFieldByGoodsId($goods_id, 'click_times');
        if ($click) {
            ++$click;
            $model->where('goods_id=' . $goods_id)->setInc('click_times', 1);
        } else {
            $click = 1;
            $data  = array(
                'goods_id'    => $goods_id,
                'click_times' => 1,
            );
            $model->add($data);
        }
        $this->ajaxReturn($click);
        //设置商品的点击数
        //返回上皮你的点击数
    }

    /**
     * 将redis中的点击数存储到数据库中
     */
    public function syncGoodsClicks() {
        $redis     = getRedis();
        $key       = 'goods_clicks';
        $clicks    = $redis->zRange($key, 0, -1, true);
        //删除数据表中的数据，然后添加
        $goods_ids = array_keys($clicks);
        $model     = M('GoodsClick');
        $model->where(array('goods_id' => array('in', $goods_ids)))->delete();
        $data      = array();
        foreach ($clicks as $key => $value) {
            $data[] = array(
                'goods_id'    => $key,
                'click_times' => $value,
            );
        }
        $model->addAll($data);
        echo '<script type="text/javascript">window.close();</script>';
        exit;
    }

    /**
     * 添加到购物车
     * 如果没有登录，就保存到cookie中
     * 如果登录了，就保存到数据库中
     * @param type $goods_id
     * @param type $amount
     */
    public function addCar($goods_id, $amount) {
        $userinfo = login();
        if ($userinfo) {
            //取出当前商品在数据表中的数据
            $model          = D('ShoppingCar');
            $cond           = array(
                'goods_id'  => $goods_id,
                'member_id' => $userinfo['id'],
            );
            //查看商品是否已经在购物车中了
            $car_goods_info = $model->where($cond)->find();
            //如果在，加数量
            if ($car_goods_info) {
                $model->where($cond)->setInc('amount', $amount);
                //如果不在，加记录
            } else {
                $data = array(
                    'goods_id'  => $goods_id,
                    'member_id' => $userinfo['id'],
                    'amount'    => $amount,
                );
                $model->add($data);
            }
        } else {
            /**
             * [
             *  'goods_id'=>amount
             * ]
             */
            $car_list = shoppingcar();
            if (isset($car_list[$goods_id])) {
                $car_list[$goods_id] += $amount;
            } else {
                $car_list[$goods_id] = $amount;
            }
            shoppingcar($car_list);
        }
        $this->success('添加购物车成功', U('flow1'), 1);
    }

    /**
     * 购物车列表
     */
    public function flow1() {
        //获取到购物车列表商品id和购买数量
        $this->assign(D('ShoppingCar')->getShoppingCarInfo());
        $this->display();
    }

    /**
     * 获取购物车需要的商品信息
     * @param type $goods_ids
     * @return type
     */
//    private function getShoppingCarInfo($car_list) {
//        $goods_ids   = array_keys($car_list);
//        $total_price = 0;
//        if (empty($goods_ids)) {
//            $goods_list = array();
//        } else {
//            //获取商品的基本信息
//            $model      = D('Goods');
//            $goods_list = $model->getShoppingCarInfo($goods_ids);
//            //展示
//            foreach ($goods_list as $key => $value) {
//                $value['shop_price'] = my_num_format($value['shop_price']);
//                $value['amount']     = $car_list[$value['id']];
//                $value['sub_total']  = my_num_format($car_list[$value['id']] * $value['shop_price']);
//                $total_price         = $total_price + $value['sub_total'];
//                $goods_list[$key]    = $value;
//            }
//        }
//        $total_price = my_num_format($total_price);
//        return array('goods_list'=>$goods_list,'total_price'=>$total_price);
//    }
    
    /**
     * 填写订单信息
     */
    public function flow2(){
        //判断是否登录，没有登录就跳转到登录页面，并且登陆后跳转回来
        $userinfo = login();
        if(empty($userinfo)){
            cookie('FORWARD',__SELF__);
            $this->error('亲，请先登录哟',U('Member/login'));
        }
        //取出当前用户的所有地址
        $this->assign('addresses',D('Address')->getList());
        //获取所有的送货方式
        $this->assign('deliveries',M('Delivery')->where('status=1')->order('sort')->select());
        //获取所有的付款方式
        $this->assign('payments',M('Payment')->where('status=1')->order('sort')->select());
        //获取购物车数据
//        $model    = M('ShoppingCar');
//        $cond     = array(
//            'member_id' => $userinfo['id'],
//        );
        $this->assign(D('ShoppingCar')->getShoppingCarInfo());
        
        $this->display();
    }
    
    /**
     * 修改购物车商品数量
     * @param type $goods_id
     * @param type $amount
     */
    public function changeAmount($goods_id,$amount){
        $model = D('ShoppingCar');
        $model->changeAmount($goods_id,$amount);
    }

    
    public function flow3(){
        $this->display();
    }
}
