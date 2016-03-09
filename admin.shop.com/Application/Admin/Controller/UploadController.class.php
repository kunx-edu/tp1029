<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Admin\Controller;

/**
 * Description of UploadController
 *
 * @author qingf
 */
class UploadController extends \Think\Controller {

    public function index() {
        //上传文件
        $config = C('UPLOAD_SETTING');
        $upload = new \Think\Upload($config);
        $file   = empty($_FILES['logo']['tmp_name']) ? array() : $_FILES['logo'];
        //如果文件不为空才执行上传操作
        $logo   = $msg    = '';

        if ($file) {
            if ($file_info = $upload->uploadOne($file)) {
                $logo = $file_info['savepath'] . $file_info['savename'];
                $status = 1;//成功了
            } else {
                $msg = $upload->getError();
                $status = false;//失败了
            }
        }
        $data = array(
            'file_url' => $logo,//文件的地址
            'msg'      => $msg,//错误消息
            'status'    => $status,//状态
        );
//        echo json_encode($data);
        $this->ajaxReturn($data);
    }

}
