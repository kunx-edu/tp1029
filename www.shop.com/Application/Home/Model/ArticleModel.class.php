<?php

namespace Home\Model;

class ArticleModel extends \Think\Model {

    /**
     * 获取首页帮助文章列表
     */
    public function getHelpArticleList() {
//        echo 'no cache<br />';
        $cond               = array(
            'status' => array('gt', 0),
            'id'     => array('elt', 5),
        );
        $article_categories = M('ArticleCategory')->where($cond)->order('sort')->select();
        $return             = array();
        /**
         * [
         *      '购物指南'=>[
         *          [],[],[]
         *          ]
         * ]
         */
        foreach ($article_categories as $article_category) {
            $return[$article_category['name']] = $this->getArticleList($article_category['id']);
        }
        return $return;
    }

    private function getArticleList($article_category) {
        $cond = array(
            'status'              => array('gt', 0),
            'article_category_id' => $article_category,
        );
        return $this->field('id,name,article_category_id')->where($cond)->order('sort')->limit(6)->select();
    }

}
