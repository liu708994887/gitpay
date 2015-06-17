====
简介
============================================
接口名称：微信公众号支付接口
版本：V3.3
开发语言：PHP

========
配置说明
===========================================

1.【基本信息设置】
商户向微信提交企业以及银行账户资料，商户功能审核通过后，可以获得帐户基本信息，找到本例程的配置文件「WxPay.config.php」，配置好如下信息：
	appId：微信公众号身份的唯一标识。审核通过后，在微信发送的邮件中查看。
	Mchid：受理商ID，身份标识
	Key:商户支付密钥Key。审核通过后，在微信发送的邮件中查看。

2.【证书路径设置】
找到本例程的配置文件「WxPay.Micropay.config.php」，配置证书路径。

3.【必须开启curl服务】
使用Crul需要修改服务器中php.ini文件的设置，找到php_curl.dll去掉前面的";"即可。

4.【设置curl超时时间】
本例程通过curl使用HTTP POST方法，此处可修改其超时时间，默认为30秒。找到本例程的配置文件「WxPay.Micropay.config.php」，配置curl超时时间。

============
代码文件结构
===========================================
wxpay_php
|-- README.txt---------------------使用说明文本
|-- WxPayHelper--------------------微信支付类库及常用文件
|   |-- SDKRuntimeException.php----异常处理类
|   |-- WxPay.Micropay.config.php--商户配置文件
|   `-- WxPayMicropayHelper.php----微信支付类库
|-- demo---------------------------例程
|   |-- micropay_call.php----------被扫支付例程
|   |-- micropay_call_index.php----被扫支付例程
|   |-- order_query.php------------订单查询例程
|   |-- refund.php-----------------退款例程
|   |-- download_bill.php----------对账单例程
|   |-- reverse.php----------------冲正例程
|   `-- refund_query.php-----------退款查询例程
`-- index.php


==============
微信支付帮助sdk
====================================================
1.每一个接口对应一个类。
2.常用工具（产生随机字符串、生成签名、以post方式提交xml、证书的使用等）封装成CommonUtil类。
3.结构明细
【常用工具】--Common_util_micropay
		trimString()，设置参数时需要用到的字符处理函数
		createNoncestr()，产生随机字符串，不长于32位
		formatBizQueryParaMap(),格式化参数，签名过程需要用到
		getSign(),生成签名
		arrayToXml(),array转xml
		xmlToArray(),xml转 array
		postXmlCurl(),以post方式提交xml到对应的接口url
		postXmlSSLCurl(),使用证书，以post方式提交xml到对应的接口url
【请求型接口】--Wxpay_client_micropay
		被扫支付接口----MicropayCall
		订单查询接口----OrderQuery_micropay
		退款申请接口----Refund_micropay
		退款查询接口----RefundQuery_micropay
		冲正接口--------Reverse_micropay
		对账单接口------DownloadBill_micropay