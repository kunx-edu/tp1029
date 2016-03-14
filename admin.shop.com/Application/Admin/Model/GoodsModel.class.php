<?php

namespace Admin\Model;

class GoodsModel extends \Think\Model {

    /**
     * 商品推荐状态
     * @var array 
     */
    public $goods_statuses = array(
        1=>'精品',
        2=>'新品',
        4=>'热销',
    );
    /**
     * 商品上架状态
     * @var array 
     */
    public $is_on_sales = array(
        1=>'上架',
        0=>'下架',
    );
    
    protected $_validate = array(
//当填写货号时应当保证货号不重复
        array('sn', '', '货号已存在', self::VALUE_VALIDATE, 'unique', self::MODEL_INSERT),
    );
    protected $_auto     = array(
        array('inputtime', NOW_TIME), //新建时间
        array('goods_status', 'array_sum', self::MODEL_BOTH, 'function'), //将表单中状态的值进行按位或
//        array('sn', 'setSn', self::MODEL_INSERT, 'callback'), //自动生成sn
    );

    /**
     * 生成sn
     * @param string $sn
     * @return string
     */
    protected function setSn($sn) {
        if (empty($sn)) {
            //生成sn  sn20160312 00000001
            $model = M('GoodsNumber');
            $date  = date('Ymd');
            $count = $model->getFieldByDate($date, 'count');
            //如果数据表中有了当天的记录,就修改,否则就添加
            if ($count) {
                $count++;
                $cond = array('date' => $date);
                $model->where($cond)->setInc('count', 1);
            } else {
                $count = 1;
                $data  = array(
                    'date'  => $date,
                    'count' => $count,
                );
                $model->add($data);
            }

            $sn = 'SN' . $date . str_pad($count, 8, '0', STR_PAD_LEFT);
        }
        return $sn;
    }

    /**
     * 完成商品的添加
     */
    public function addGoods() {
        unset($this->data['id']);
        //开启事务
        $this->startTrans();
        $this->data['sn'] = $this->setSn($this->data['sn']);
        /**
         * 1.商品的基本信息
         * 2.商品的详细描述
         */
        if (($id               = $this->add()) === false) {
            $this->error = '添加商品失败';
            $this->rollback();
            return false;
        }

        //保存商品详细信息
        if ($this->_addContent($id) === false) {
            $this->error = '添加商品详情失败';
            $this->rollback();
            return false;
        }

        //执行相册的保存
        if ($this->_addGallery($id) === false) {
            $this->error = '添加相册图片失败';
            $this->rollback();
            return false;
        }

        //保存货号,如果货号已经提交,就判断是否重复,否则自动生成
        $this->commit();
    }

    /**
     * 插入商品详情,返回成功还是失败
     * @param integer $goods_id 商品id
     * @return boolean false失败 true成功
     */
    private function _addContent($goods_id) {
        $model   = M('GoodsIntro');
        $content = I('post.content', '', false); //不使用过滤
        $data    = array(
            'goods_id' => $goods_id,
            'content'  => $content,
        );
        return $model->add($data) !== false;
    }

    /**
     * 获取分页商品信息
     * @param array $cond
     * @param type $page
     */
    public function getPageResult(array $cond = array(), $page = 1) {
        $count     = $this->where($cond)->where('status<>0')->count();
        $rows      = $this->where($cond)->where('status<>0')->order('sort asc')->page($page, C('PAGE_SIZE'))->select();
        $page      = new \Think\Page($count, C('PAGE_SIZE'));
        $page->setConfig('theme', '%HEADER% %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%');
        $page_html = $page->show();

        /**
         * 增加了新品 热销 精品的元素
         */
        foreach ($rows as $key => $value) {
            $value['is_best'] = ($value['goods_status'] & 1) ? 1 : 0;
            $value['is_new']  = ($value['goods_status'] & 2) ? 1 : 0;
            $value['is_hot']  = ($value['goods_status'] & 4) ? 1 : 0;
            $rows[$key]       = $value;
        }
        return array('page_html' => $page_html, 'rows' => $rows);
    }

    /**
     * 取出一个商品的详细信息
     * @param integer $goods_id 商品id
     */
    public function getGoodsInfo($goods_id) {
        $row            = $this->find($goods_id);
        $row['is_best'] = ($row['goods_status'] & 1) ? 1 : 0;
        $row['is_new']  = ($row['goods_status'] & 2) ? 1 : 0;
        $row['is_hot']  = ($row['goods_status'] & 4) ? 1 : 0;
        $row['content'] = M('GoodsIntro')->getFieldByGoodsId($goods_id, 'content');
        $row['paths']= M('GoodsGallery')->field('id,path')->where(array('goods_id'=>$goods_id))->select();
        return $row;
    }

    /**
     * 保存商品
     */
    public function updateGoods() {
        $request_data = $this->data;
        $this->startTrans();
        //1.保存基本信息
        if ($this->save() === false) {
            $this->error = '保存失败';
            $this->rollback();
            return false;
        }
        //2.保存详细描述
        if ($this->updateContent($request_data['id']) === false) {
            $this->error = '保存详细描述失败';
            $this->rollback();
            return false;
        }
        
        //执行相册的保存
        if ($this->_addGallery($request_data['id']) === false) {
            $this->error = '保存相册图片失败';
            $this->rollback();
            return false;
        }
        $this->commit();
        return true;
    }

    /**
     * 修改商品的详细描述
     * @param integer $goods_id
     * @return boolean
     */
    private function updateContent($goods_id) {
        $data = array(
            'goods_id' => $goods_id,
            'content'  => I('post.content', '', false),
        );
        return M('GoodsIntro')->save($data) !== false;
    }

    /**
     * 删除指定的商品
     * @param type $goods_id
     */
    public function deleteGoods($goods_id) {
        $data = array(
            'id'     => $goods_id,
            'status' => 0,
        );
        return $this->save($data);
    }

    /**
     * 插入相册
     * @param integer $goods_id 商品的id
     * @return boolean
     */
    private function _addGallery($goods_id) {
        //收集相册图片
        $paths = I('post.path');
        $data  = array();
        foreach ($paths as $path) {
            $data[] = array(
                'goods_id' => $goods_id,
                'path'     => $path,
            );
        }
        if ($data) {
            //保存相册图片
            $model = M('GoodsGallery');
            return $model->addAll($data);
        }
        return true;
    }

}
