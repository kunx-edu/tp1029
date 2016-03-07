<?php
namespace Admin\Controller;
class SupplierController extends \Think\Controller{
    private $_model = null;
    protected function _initialize(){
        $meta_titles = array(
            'index'=>'供货商管理',
            'add'=>'添加供货商',
            'edit'=>'修改供货商',
            'delete'=>'删除供货商',
        );
        $meta_title = isset($meta_titles[ACTION_NAME])?$meta_titles[ACTION_NAME]:'供货商管理';
        $this->assign('meta_title',$meta_title);
        $this->_model = D('Supplier');//由于所有的操作都需要用到模型,我们在初始化方法中创建
    }


    /**
     * 展示供货商的列表
     */
    public function index(){
//        $model = D('Supplier');
        $this->assign('rows',$this->_model->select());
        $this->display();
    }
    
    /**
     * 新增供货商
     */
    public function add(){
//        $model = D('Supplier');
        //如果提交了,就保存到数据表中
        if(IS_POST){
            //收集数据 验证数据是否合法
            if($this->_model->create() === false){
                $this->error($this->_model->getError());
            }
            //执行数据插入
            if($this->_model->add() === false){
                $this->error($this->_model->getError());
            }
            $this->success('添加成功',U('index'));
        }else{
            $this->display();
        }
    }
    
    /**
     * 完成供货商的修改操作
     * @param integer $id 供货商id.
     */
    public function edit($id){
        //提交
        if(IS_POST){
            
        }else{
        //展示
            //得到数据表中的数据
            $row = $this->_model->find($id);
            $this->assign('row', $row);
            $this->display('add');
        }
    }
}
