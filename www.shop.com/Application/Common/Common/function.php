<?php
/**
 * 发送短信.
 * @param type $telphone
 * @param type $param
 * @return boolean
 */
function sendSMS($telphone, $param) {
    vendor('Alidayu.Autoloader');
    $c            = new \TopClient;
    $c->appkey    = '23328756';
    $c->secretKey = 'e87ec1ad95d7c8621d74b2767a80825c';
    $req          = new \AlibabaAliqinFcSmsNumSendRequest;
    $req->setSmsType("normal");
    $req->setSmsFreeSignName("大鱼测试");
//    $param = array(
//        'code'=>\Org\Util\String::randNumber(1000, 9999),
//        'product'=>'仙人跳',
//    );
    $param        = json_encode($param);
    $req->setSmsParam($param);
    $req->setRecNum($telphone);
    $req->setSmsTemplateCode("SMS_6285006");
    $resp         = $c->execute($req);
    if (isset($resp->result->success) && $resp->result->success == 'true') {
        return true;
    } else {
        return false;
    }
}

/**
 * 将短信验证码存入session
 * @param type $data
 * @return array
 */
function tel_code($data=null){
    if(is_null($data)){
        $data = session('tel_code');
        if(!$data){
            $data=array();
        }
        return $data;
    } else{
        session('tel_code',$data);
    }
}


/**
 * 自定义加盐加密算法
 * @param string $string 原密码
 * @param string $salt 盐
 * @return string 加盐加密后的结果
 */
function my_mcrypt($string,$salt){
    return md5(md5($string).$salt);
}