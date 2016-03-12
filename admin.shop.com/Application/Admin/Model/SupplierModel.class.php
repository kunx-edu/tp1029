<?php

namespace Admin\Model;

class SupplierModel extends \Think\Model {

    protected $_validate = array(
        array('name', 'require', '供货商名称不能为空'),
        array('name', '', '供货商已存在', self::EXISTS_VALIDATE, 'unique'),
    );

    /**
     * 获取列表
     * 分页
     * 搜索
     * 排序sort
     * 过滤已删除
     */
    public function getPageResult(array $cond = array(), $page = 1) {
        $count     = $this->where($cond)->where('status<>-1')->count();
        $rows      = $this->where($cond)->where('status<>-1')->order('sort asc')->page($page, C('PAGE_SIZE'))->select();
        $page      = new \Think\Page($count, C('PAGE_SIZE'));
        $page->setConfig('theme', '%HEADER% %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%');
        $page_html = $page->show();

        return array('page_html' => $page_html, 'rows' => $rows);
    }

    /**
     * 修改或者删除供货商,如果删除的话,名字加上_del后缀
     * @param integer $id 要修改的供货商id
     * @param integer $status 修改成什么状态
     * @return boolean|integer 失败返回false,成功返回影响的行数
     */
    public function changeStatus($id, $status = -1) {
        $data = array(
            'status' => $status,
            'name'   => array('exp', 'concat(name,"_del")'),
            'id'     => $id,
        );
        //如果不是删除操作,就不修改名字
        if ($status != -1) {
            unset($data['name']);
        }

        return $this->save($data);
    }
    
    /**
     * 取出正常显示的供货商.
     * @return array
     */
    public function getList(){
        return $this->field('id,name')->where(array('status'=>array('gt',-1)))->select();
    }

}
