<?php
namespace Admin\Model;

class GoodsCategoryModel extends \Think\Model{
    /**
     * 获取分类列表
     * @return type
     */
    public function getList(){
        $cond = array(
            'status'=>array('gt',-1),
        );
        return $this->where($cond)->order('lft asc')->select();
    }
    
    /**
     * 添加分类,并计算层级\左右节点
     * @return boolean
     */
    public function addCategory() {
        //添加分类的时候判断指定的父级分类下有没有同名分类
        $cond = array(
            'parent_id'=>$this->data['parent_id'],
            'name'=>$this->data['name'],
        );
        //查看是否有记录
        if($this->where($cond)->count()){
            $this->error = '已经存在同名分类';
            return false;
        }
        unset($this->data['id']);
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
        //编辑分类的时候判断指定的父级分类下有没有同名分类
        $cond = array(
            'parent_id'=>$request_data['parent_id'],
            'name'=>$request_data['name'],
            'id'=>array('neq',$request_data['id']),
        );
        //查看是否有记录
        if($this->where($cond)->count()){
            $this->error = '已经存在同名分类';
            return false;
        }
        
        
        
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
    
    /**
     * 逻辑删除分类,同时会删除所有的后代分类
     * @param integer $id
     * @return interger|false
     */
    public function deleteCategory($id){
        //以下使用nestedsets删除节点,会执行物理删除,并且会重新计算各节点.
//        //创建具体的sql执行的对象
//            $db = D('DbMysql','Logic');
//            $table_name = $this->trueTableName;//获取数据表的名字
//            //创建用于生成sql结构的对象
//            $nested_sets = new \Admin\Service\NestedSetsService($db, $table_name, 'lft', 'rght', 'parent_id', 'id', 'level');
//            $rst = $nested_sets->delete($id);
//            var_dump($rst);
//            exit;
        
        
        //逻辑删除
        /**
         * 应当将所有的后代节点以及其自身找出来并且更改状态为-1
         * 获取当前分类的左右节点
         * 查询出>=lft <=rght的节点
         */
        $row = $this->field('lft,rght')->find($id);
        //update goods_category set status=-1 where lft>=17 and rght<=25
        $data = array(
            'status'=>-1,
            'name'=>array('exp','concat(`name`,"_del")'),
        );
        //拼接条件,所有的后代分类左节点都>当前的左节点,所有的右节点都<当前的右节点
        $cond = array(
            'lft'=>array('egt',$row['lft']),
            'rght'=>array('elt',$row['rght']),
        );
        return $this->where($cond)->save($data);
    }
}
