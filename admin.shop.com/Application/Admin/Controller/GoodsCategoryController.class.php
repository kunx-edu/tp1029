<?php


namespace Admin\Controller;

class GoodsCategoryController extends \Think\Controller{
    public function index(){
//       $this->display(); 
    }
    
    /**
     * 添加分类
     */
    public function add(){
        if(IS_POST){
            dump(I('post.'));
        }else{
            
            //取出所有的分类
            $model = D('GoodsCategory');
            $rows = $model->getList();
            foreach($rows as $key=>$value){
                $rows[$key]['pId'] = $value['parent_id'];
            }
            $rows = json_encode($rows);
            $this->assign('rows',$rows);//将所有分类传递给视图
            $this->display();
        }
    }
}
