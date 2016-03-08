<?php

namespace Admin\Model;

class ArticleModel extends \Think\Model {

    protected $_validate = array(
        array('name', 'require', '文章名称不能为空'),
        array('article_category_id', 'require', '文章分类不能为空'),
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
            'id'     => $id,
        );
        return $this->save($data);
    }

    
    public function addArticle(){
        if(($id = $this->add()) === false){
            $this->error = '添加文章失败';
            return false;
        }
        $data = array(
            'article_id'=>$id,
            'content'=>I('post.content'),
        );
        if(M('ArticleContent')->add($data)===false){
            $this->error = '保存文章详情失败';
            return false;
        }
        return true;
    }
    
    /**
     * 修改文章,更新文章详情.
     * @return boolean
     */
    public function updateArticle(){
        $request_data = $this->data;
        if(($id = $this->save()) === false){
            $this->error = '保存文章失败';
            return false;
        }
        $data = array(
            'article_id'=>$request_data['id'],
            'content'=>I('post.content'),
        );
        if(M('ArticleContent')->save($data)===false){
            $this->error = '保存文章详情失败';
            return false;
        }
        return true;
    }
}
