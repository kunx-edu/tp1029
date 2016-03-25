<?php

namespace Home\Controller;

class PaymentController extends \Think\Controller {

    /**
     * 使用支付宝支付
     */
    public function alipay($id) {
        //设定页面编码
        header('Content-Type: text/html;charset=utf-8');

        //合作身份者id，以2088开头的16位纯数字
        $alipay_config['partner'] = '2088002155956432';

        //收款支付宝账号，一般情况下收款账号就是签约账号
        $alipay_config['seller_email'] = 'guoguanzhao520@163.com';

        //安全检验码，以数字和字母组成的32位字符
        $alipay_config['key'] = 'a0csaesgzhpmiiguif2j6elkyhlvf4t9';


        //↑↑↑↑↑↑↑↑↑↑请在这里配置您的基本信息↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑
        //签名方式 不需修改
        $alipay_config['sign_type'] = strtoupper('MD5');

        //字符编码格式 目前支持 gbk 或 utf-8
        $alipay_config['input_charset'] = strtolower('utf-8');

        //ca证书路径地址，用于curl中ssl校验
        $alipay_config['cacert'] = VENDOR_PATH. 'Alipay/cacert.pem';

        //访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
        $alipay_config['transport'] = 'http';


        

        /** ************************请求参数************************* */

        //支付类型
        $payment_type      = "1";
        //必填，不能修改
        //服务器异步通知页面路径
        $notify_url        = "http://kunx.org/create_partner_trade_by_buyer-PHP-UTF-8/notify_url.php";
        //需http://格式的完整路径，不能加?id=123这类自定义参数
        //页面跳转同步通知页面路径
        //"http://www.shop.com/index.php/create_partner_trade_by_buyer-PHP-UTF-8/return_url.php"
        $return_url        = U('Order/return','','',true);
        //需http://格式的完整路径，不能加?id=123这类自定义参数，不能写成http://localhost/
        //获取订单相关的信息
        $order_info = M('OrderInfo')->where(array('id'=>$id,'status'=>1))->find();
        //商户订单号
        $out_trade_no      = $order_info['sn'];
        //商户网站订单系统中唯一订单号，必填
        //订单名称
        $subject           = '仙人跳购物';
        //必填
        //付款金额
        $price             = $order_info['price'];
        //必填
        //商品数量
        $quantity          = "1";
        //必填，建议默认为1，不改变值，把一次交易看成是一次下订单而非购买一件商品
        //物流费用
        $logistics_fee     = $order_info['delivery_price'];
        //必填，即运费
        //物流类型
        $logistics_type    = "EXPRESS";
        //必填，三个值可选：EXPRESS（快递）、POST（平邮）、EMS（EMS）
        //物流支付方式
        $logistics_payment = "BUYER_PAY";
        //必填，两个值可选：SELLER_PAY（卖家承担运费）、BUYER_PAY（买家承担运费）
        //订单描述
        $body              = '仙人跳，让你全家鸡飞狗跳';
        //商品展示地址
        $show_url          = U('Index/index','','',true);
        //需以http://开头的完整路径，如：http://www.商户网站.com/myorder.html
        //收货人姓名
        $receive_name      = $order_info['name'];
        //如：张三
        //收货人地址
        $receive_address   = $order_info['province_name'].$order_info['city_name'].$order_info['area_name'].$order_info['detail_address'];
        //如：XX省XXX市XXX区XXX路XXX小区XXX栋XXX单元XXX号
        //收货人邮编
        $receive_zip       = '610000';
        //如：123456
        //收货人电话号码
        $receive_phone     = $order_info['tel'];
        //如：0571-88158090
        //收货人手机号码
        $receive_mobile    = $order_info['tel'];
        //如：13312341234


        /** ********************************************************* */

        //构造要请求的参数数组，无需改动
        $parameter = array(
            "service"           => "create_partner_trade_by_buyer",
            "partner"           => trim($alipay_config['partner']),
            "seller_email"      => trim($alipay_config['seller_email']),
            "payment_type"      => $payment_type,
            "notify_url"        => $notify_url,
            "return_url"        => $return_url,
            "out_trade_no"      => $out_trade_no,
            "subject"           => $subject,
            "price"             => $price,
            "quantity"          => $quantity,
            "logistics_fee"     => $logistics_fee,
            "logistics_type"    => $logistics_type,
            "logistics_payment" => $logistics_payment,
            "body"              => $body,
            "show_url"          => $show_url,
            "receive_name"      => $receive_name,
            "receive_address"   => $receive_address,
            "receive_zip"       => $receive_zip,
            "receive_phone"     => $receive_phone,
            "receive_mobile"    => $receive_mobile,
            "_input_charset"    => trim(strtolower($alipay_config['input_charset']))
        );

        //建立请求
        vendor('Alipay.alipay_submit#class');
        $alipaySubmit = new \AlipaySubmit($alipay_config);
        $html_text    = $alipaySubmit->buildRequestForm($parameter, "get", "确认");
        echo $html_text;
    }
    
    public function alipay2($id){
        $cond = array(
            'status'=>1,
            'id'=>$id,
        );
        if(M('OrderInfo')->where($cond)->setField('status',2)){
            $this->success('付款成功，等待商家发货');
        }else{
            $this->error(M('OrderInfo')->getError());
        }
    }

}
