
微信支付简介
===============

本项目将thinkphp5和微信支付的相关接口结合

+将微信支付开发文档与mvc框架相结合
+用微信测试帐号模拟了模式二（统一下单API）

详细开发文档参考 [微信支付开发者文档](https://pay.weixin.qq.com/wiki/doc/api/native.php?chapter=6_3)

## 目录结构

初始的目录结构如下：

~~~

www  WEB部署目录（或者子目录）
├─application           应用目录
│  ├─index             模块目录
│  │  ├─controller    控制器
│  │     ├─Wxpay      核心文件
│     ├─view       
│        ├─wxpay         
│              ├─orderpay.html      视图文件
├─extend                扩展类库目录

~~~

在项目中遇到的两个问题：

一、错误信息：
>Fatal error: Uncaught exception ‘WxPayException‘ with message ‘curl出错，错误码:60‘ in D:\wwwroot\weixinpaytest\lib\WxPay.Api.php:564 Stack trace: #0 D:\wwwroot\weixinpaytest\lib\WxPay.Api.php(62):

参考了官方文档的[注意事项](https://pay.weixin.qq.com/wiki/doc/api/jsapi.php?chapter=11_2)

打开后找到官方给出的解决方案。

~~~
示例1（php）:
curl_setopt($curl, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1);

在去WxPay.Api.php 文件中找到如下代码（约357-358行）：

curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,TRUE);
curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,2);//严格校验

作如下修改

if(stripos($url,"https://")!==FALSE){
        curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        }    else    {
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,TRUE);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,2);//严格校验
} 

~~~
[参考链接](http://blog.csdn.net/qq_34755805/article/details/51221932)

二、错误信息：
>参数 spbill_create_ip 在  /lib/WxPay.Api.php  的53行
>微信扫码支付invalid spbill_create_ip错误

原因：打印出变量  $_SERVER['REMOTE_ADDR'] ，发现是  ::1 ，这明显是一个无效的ip地址

解决方案：
~~~
//获取浏览器ip地址
public static function real_ip()
{
    static $realip;


    if ($realip !== NULL) {
        return $realip;
    }


    if (isset($_SERVER)) {
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);


            foreach ($arr as $ip) {
                $ip = trim($ip);


                if ($ip != 'unknown') {
                    $realip = $ip;
                    break;
                }
            }
        }
        else if (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $realip = $_SERVER['HTTP_CLIENT_IP'];
        }
        else if (isset($_SERVER['REMOTE_ADDR'])) {
            $realip = $_SERVER['REMOTE_ADDR'];
        }
        else {
            $realip = '0.0.0.0';
        }
    }
    else if (getenv('HTTP_X_FORWARDED_FOR')) {
        $realip = getenv('HTTP_X_FORWARDED_FOR');
    }
    else if (getenv('HTTP_CLIENT_IP')) {
        $realip = getenv('HTTP_CLIENT_IP');
    }
    else {
        $realip = getenv('REMOTE_ADDR');
    }


    preg_match('/[\\d\\.]{7,15}/', $realip, $onlineip);
    $realip = (!empty($onlineip[0]) ? $onlineip[0] : '0.0.0.0');
    return $realip;
}
~~~
####将上面的函数添加到类WxPayApi后，修改如下
$inputObj->SetSpbill_create_ip(self::real_ip());//终端ip 

[参考链接](http://blog.csdn.net/Cand6oy/article/details/79122198)