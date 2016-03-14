<?php

namespace Admin\Model;

class PermissionModel extends \Think\Model {

    protected $_validate = array(
        array('name', 'require', '权限名称不能为空', self::EXISTS_VALIDATE, '', self::MODEL_BOTH),
        array('parent_id', 'require', '父级权限不能为空', self::EXISTS_VALIDATE, '', self::MODEL_BOTH),
    );

    /**
     * 添加权限,使用nestedsets
     * 由于是新增权限,所以直接调用nestedsets的insert即可
     */
    public function addPermission() {
        unset($this->data['id']);
        $db_mysql    = D('DbMysql', 'Logic');
        $table_name  = $this->trueTableName;
        $nested_sets = new \Admin\Service\NestedSetsService($db_mysql, $table_name, 'lft', 'rght', 'parent_id', 'id', 'level');
        if ($nested_sets->insert($this->data['parent_id'], $this->data, 'bottom') === false) {
            $this->error = '添加权限失败';
            return false;
        }
        return true;
    }

    /**
     * 获取权限列表
     * @return type
     */
    public function getList() {
        $cond = array('status' => array('gt', 0));
        return $this->where($cond)->select();
    }

    /**
     * 修改权限.
     * @return boolean
     */
    public function updatePermission() {
        //获取原来的父级节点
        $request_data = $this->data;
        //编辑分类的时候判断指定的父级分类下有没有同名分类
        $cond         = array(
            'parent_id' => $request_data['parent_id'],
            'name'      => $request_data['name'],
            'id'        => array('neq', $request_data['id']),
        );
        //查看是否有记录
        if ($this->where($cond)->count()) {
            $this->error = '已经存在同名权限';
            return false;
        }



        $old_parent_id = $this->getFieldById($request_data['id'], 'parent_id');
        //由于如果没有改变父级分类,moveUnder将会返回false,所以我们先判断是否改变了父级分类
        if ($old_parent_id !== $request_data['parent_id']) {
            //创建具体的sql执行的对象
            $db          = D('DbMysql', 'Logic');
            $table_name  = $this->trueTableName; //获取数据表的名字
            //创建用于生成sql结构的对象
            $nested_sets = new \Admin\Service\NestedSetsService($db, $table_name, 'lft', 'rght', 'parent_id', 'id', 'level');
            if ($nested_sets->moveUnder($request_data['id'], $request_data['parent_id'], 'bottom') === false) {
                $this->error = '修改父级权限失败';
                return false;
            }
        }

        if ($this->save() === false) {
            $this->error = '修改权限失败';
            return false;
        }

        return true;
    }

}
