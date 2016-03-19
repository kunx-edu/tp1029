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


function getRedis(){
    $redis = new \Redis;
    $redis->connect('127.0.0.1',6379);
    return $redis;
}