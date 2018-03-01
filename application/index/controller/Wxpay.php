<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/1
 * Time: 10:58
 */

namespace app\index\controller;


use think\Controller;
include(PAY_PATH.'wxpay/lib/WxPay.Api.php');


class Wxpay extends Controller
{
    public function orderpay()
    {
        /**
         * 流程：
         * 1、调用统一下单，取得code_url，生成二维码
         * 2、用户扫描二维码，进行支付
         * 3、支付完成之后，微信服务器会通知支付成功
         * 4、在支付成功通知中需要查单确认是否真正支付成功（见：notify.php）
         */
        $input = new \WxPayUnifiedOrder();//统一下单输入对象
        $input->SetBody("黄楚宏"); //设置商品描述
        $input->SetAttach("黄楚宏的测试");//设置商品附加数据
        $input->SetOut_trade_no(\WxPayConfig::MCHID . date("YmdHis"));//设置商品号
        $input->SetTotal_fee("1");//设置订单总金额，单位为分，只能为整数
        $input->SetTime_start(date("YmdHis"));//设置订单生成时间
        $input->SetTime_expire(date("YmdHis", time() + 600));//设置订单失效时间
        $input->SetGoods_tag("腾讯大佬");//设置商品标记
        $input->SetNotify_url("http://paysdk.weixin.qq.com/example/notify.php");//设置接收微信支付异步通知回调地址!!!!
        $input->SetTrade_type("NATIVE");//设置参数
        $input->SetProduct_id("123456789");//商品id
        $result = \WxPayApi::unifiedOrder($input);
        $url2 = $result["code_url"];

        $this->assign('url',urlencode($url2));
        return $this->fetch();
    }
}