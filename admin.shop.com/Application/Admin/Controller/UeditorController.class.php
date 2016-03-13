<?php

namespace Admin\Controller;

class UeditorController extends \Think\Controller {

    /**
     * 使用项目的七牛云进行上传图片.
     */
    public function index() {
        $ueditor_base_url = ROOT_PATH . 'Public/ext/ueditor/';
        date_default_timezone_set("Asia/chongqing");
        error_reporting(E_ERROR);
        header("Content-Type: text/html; charset=utf-8");

        $CONFIG = json_decode(preg_replace("/\/\*[\s\S]+?\*\//", "", file_get_contents($ueditor_base_url . "php/config.json")), true);
        $action = I('get.action');

        switch ($action) {
            case 'config':
                $result = json_encode($CONFIG);
                break;

            /* 上传图片 */
            case 'uploadimage':
                $upload = new \Think\Upload(C('UPLOAD_SETTING'));
                $file_info = $upload->uploadOne($_FILES['upfile']);
                if($file_info) {
                    $file_info['state'] = 'SUCCESS';
                    $file_info['title'] = $file_info['savename'];
                    $file_info['original'] = $file_info['savename'];
                    $file_info['type'] = '.'.$file_info['ext'];
                }
                echo json_encode($file_info);
                break;

            default:
                $result = json_encode(array(
                    'state' => '请求地址出错'
                ));
                break;
        }

        /* 输出结果 */
        if (I('get.callback')) {
            if (preg_match("/^[\w_]+$/", I('get.callback'))) {
                echo htmlspecialchars(I('get.callback')) . '(' . $result . ')';
            } else {
                echo json_encode(array(
                    'state' => 'callback参数不合法'
                ));
            }
        } else {
            echo $result;
        }
        exit;
    }

}
