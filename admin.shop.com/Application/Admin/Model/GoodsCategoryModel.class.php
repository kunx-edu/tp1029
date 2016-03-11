<?php
namespace Admin\Model;

class GoodsCategoryModel extends \Think\Model{
    public function getList(){
        return $this->order('lft asc')->select();
    }
    
    /**
     * 添加分类,并计算层级\左右节点
     * @return boolean
     */
    public function addCategory() {
        //创建具体的sql执行的对象
        $db = D('DbMysql','Logic');
        $table_name = $this->trueTableName;//获取数据表的名字
        //创建用于生成sql结构的对象
        $nested_sets = new \Admin\Service\NestedSetsService($db, $table_name, 'lft', 'rght', 'parent_id', 'id', 'level');
        //插入一条新的数据
        if($nested_sets->insert($this->data['parent_id'],$this->data)===false){
            $this->error = '新建分类失败';
            return false;
        }
        return true;
    }
    
    /**
     * 修改分类,如果修改了父级分类的话,重新计算左右节点和层级.
     * 要注意不要使用find获取原始的父级分类,具体见基础模型find方法.
     * @return boolean
     */
    public function updateCategory(){
        $request_data = $this->data;
        $old_parent_id = $this->getFieldById($request_data['id'],'parent_id');
        //由于如果没有改变父级分类,moveUnder将会返回false,所以我们先判断是否改变了父级分类
        if($old_parent_id !== $request_data['parent_id']){
            //创建具体的sql执行的对象
            $db = D('DbMysql','Logic');
            $table_name = $this->trueTableName;//获取数据表的名字
            //创建用于生成sql结构的对象
            $nested_sets = new \Admin\Service\NestedSetsService($db, $table_name, 'lft', 'rght', 'parent_id', 'id', 'level');
            if($nested_sets->moveUnder($request_data['id'], $request_data['parent_id'], 'bottom')===false){
                $this->error = '修改父级分类失败';
                return false;
            }
            
        }
        if($this->save()===false){
            $this->error = '修改分类失败';
            return false;
        }
        
        return true;
    }
}
