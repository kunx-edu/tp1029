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
    $c->format = 'json';
    $c->appkey    = '23328756';
    $c->secretKey = 'e87ec1ad95d7c8621d74b2767a80825c';
    $req          = new \AlibabaAliqinFcSmsNumSendRequest;
    $req->setSmsType("normal");
    $req->setSmsFreeSignName("大鱼测试");
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
function tel_code($data = null) {
    if (is_null($data)) {
        $data = session('tel_code');
        if (!$data) {
            $data = array();
        }
        return $data;
    } else {
        session('tel_code', $data);
    }
}

/**
 * 自定义加盐加密算法
 * @param string $string 原密码
 * @param string $salt 盐
 * @return string 加盐加密后的结果
 */
function my_mcrypt($string, $salt) {
    return md5(md5($string) . $salt);
}

function sendMail($address, $content, $subject) {
    vendor('phpmailer.PHPMailerAutoload');
    $mail = new \PHPMailer;
    $mail->isSMTP();                                      // Set mailer to use SMTP
    $email_setting = C('EMAIL_SETTING');
    $mail->Host     = $email_setting['Host'];  // Specify main and backup SMTP servers
    $mail->SMTPAuth = true;                               // Enable SMTP authentication
    $mail->Username = $email_setting['Username'];                 // SMTP username
    $mail->Password = $email_setting['Password'];                           // SMTP password

    $mail->setFrom($email_setting['Username']);
    $mail->addAddress($address);     // Add a recipient

    $mail->isHTML(true);                                  // Set email format to HTML

    $mail->Subject = $subject;
    $mail->Body    = $content;
    $mail->CharSet = 'UTF-8';

    return $mail->send();
}

/**
 * 获取redis连接
 * @return \Redis
 */
function getRedis(){
    $redis = new \Redis;
    $redis->connect('127.0.0.1',6379);
    return $redis;
}


/**
 * 保存和获取用户的信息(session)
 */
function login($data = null){
    if(!is_null($data)){
        session('userinfo',$data);
    }else{
        $userinfo = session('userinfo');
        if(!$userinfo){
            $userinfo = array();
        }
        return $userinfo;
    }
}

/**
 * 生成令牌.
 * @param type $len
 * @return type
 */
function createToken($len = 32){
    $token = mcrypt_create_iv($len);
    $token = base64_encode($token);
    $token = substr($token,0,$len);
    return $token;
}



/**
 * 获取或者保存token信息
 * @param array|null $data
 * @return array|null
 */
function token($data=null){
    if(is_null($data)){
        $token = cookie('token');
        if(!$token){
            $token = array();
        }else{
            $token = unserialize($token);
        }
        return $token;
    }else{
        $data = serialize($data);
        cookie('token',$data,604800);//存7天
    }
}

/**
 * 获取或者保存token信息
 * @param array|null $data
 * @return array|null
 */
function shoppingcar($data=null){
    if(is_null($data)){
        $car_list = cookie('SHOPPING_CAR');
        if(!$car_list){
            $car_list = array();
        }
        return $car_list;
    }else{
        cookie('SHOPPING_CAR',$data,604800);//存7天
    }
}

/**
 * 使用财务表示形式
 * @param type $number
 */
function my_num_format($number){
    return number_format($number,2,'.','');
}


/**
 * 将关联数组转换成一个下拉列表
 * @param array $data 数据库中的关联数组
 * @param string $value_field 值字段
 * @param string $name_field 名字段
 * @param string $name 表单提交的控件name属性
 * @return string 具体下拉列表的html代码
 */
function arr2select(array $data, $value_field, $name_field, $name = '', $selected_value = '') {
    $html = "<select name='$name' class='$name'>";
    $html .= '<option value="">请选择...</option>';
    foreach ($data as $item) {
        if ($selected_value == $item[$value_field]) {
            $html .= "<option value='{$item[$value_field]}' selected='selected'>{$item[$name_field]}</option>";
        } else {
            $html .= "<option value='{$item[$value_field]}'>{$item[$name_field]}</option>";
        }
    }
    $html .= "</select>";
    return $html;
}