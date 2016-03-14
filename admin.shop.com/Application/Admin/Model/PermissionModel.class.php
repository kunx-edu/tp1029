<?php


namespace Admin\Model;

class PermissionModel extends \Think\Model{
    protected $_validate = array(
        array('name','require','权限名称不能为空',self::EXISTS_VALIDATE,'',self::MODEL_BOTH),
        array('parent_id','require','父级权限不能为空',self::EXISTS_VALIDATE,'',self::MODEL_BOTH),
    );
    
    /**
     * 添加权限,使用nestedsets
     * 由于是新增权限,所以直接调用nestedsets的insert即可
     */
    public function addPermission(){
        unset($this->data['id']);
        $db_mysql = D('DbMysql','Logic');
        $table_name = $this->trueTableName;
        $nested_sets = new \Admin\Service\NestedSetsService($db_mysql, $table_name, 'lft', 'rght', 'parent_id', 'id', 'level');
        if($nested_sets->insert($this->data['parent_id'], $this->data, 'bottom') === false){
            $this->error = '添加权限失败';
            return false;
        }
        return true;
    }
    
    /**
     * 获取权限列表
     * @return type
     */
    public function getList(){
        $cond = array('status'=>array('gt',0));
        return $this->where($cond)->select();
    }
}
