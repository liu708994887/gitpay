<?php
/**
 * 被扫支付demo
 * ====================================================
 * 
*/
	include_once("../WxPayMicropayHelper/WxPayMicropayHelper.php");
    header("Content-Type:text/html;charset=utf-8");

	//自定义订单号，此处仅作举例
	$timeStamp = time();
	$out_trade_no = WxPayConf_micropay::APPID."$timeStamp";

	//获取用户一维码
	if (isset($_POST["auth_code"]))
	{
	    $auth_code = $_POST["auth_code"];
		
		//使用被扫支付接口
		$micropayCall = new MicropayCall();
		
		//设置被扫支付接口参数
		//设置必填参数
		//appid已填,商户无需重复填写
		//mch_id已填,商户无需重复填写
		//noncestr已填,商户无需重复填写
		//spbill_create_ip已填,商户无需重复填写
		//sign已填,商户无需重复填写
		$micropayCall->setParameter("body","刘老板贡献一分钱给你");//商品描述
		$micropayCall->setParameter("out_trade_no","$out_trade_no");//商户订单号 
		$micropayCall->setParameter("total_fee","1");//总金额
		$micropayCall->setParameter("auth_code","$auth_code");//交易类型
		//非必填参数，商户可根据实际情况选填
		//$micropayCall->setParameter("sub_mch_id","XXXX");//子商户号  
		//$micropayCall->setParameter("device_info","XXXX");//设备号 
		//$micropayCall->setParameter("attach","XXXX");//附加数据 
		//$micropayCall->setParameter("time_start","XXXX");//交易起始时间
		//$micropayCall->setParameter("time_expire","XXXX");//交易结束时间 
		//$micropayCall->setParameter("goods_tag","XXXX");//商品标记 
		//$micropayCall->setParameter("openid","XXXX");//用户标识
		//$micropayCall->setParameter("product_id","XXXX");//商品ID
	
		//提交订单
		$micropayCallResult = $micropayCall->getResult();
        var_dump($micropayCallResult);

		//商户根据实际情况设置相应的处理流程,此处仅作举例
		if ($micropayCallResult["return_code"] == "FAIL") {
			echo "通信出错：".$micropayCallResult['return_msg']."<br>";
		}
		elseif($micropayCallResult["result_code"] == "FAIL"){
			echo "出错"."<br>";
			echo "错误代码：".$micropayCallResult['err_code']."<br>";
			echo "错误代码描述：".$micropayCallResult['err_code_des']."<br>";
		}
		else{
			echo "用户标识：".$micropayCallResult['openid']."<br>";
			echo "是否关注公众账号：".$micropayCallResult['is_subscribe']."<br>";
			echo "交易类型：".$micropayCallResult['trade_type']."<br>";
			echo "付款银行：".$micropayCallResult['bank_type']."<br>";
			echo "总金额：".$micropayCallResult['total_fee']."<br>";
			echo "现金券金额：".$micropayCallResult['coupon_fee']."<br>";
			echo "货币种类：".$micropayCallResult['fee_type']."<br>";
			echo "微信支付订单号：".$micropayCallResult['transaction_id']."<br>";
			echo "商户订单号：".$micropayCallResult['out_trade_no']."<br>";
			echo "商家数据包：".$micropayCallResult['attach']."<br>";
			echo "支付完成时间：".$micropayCallResult['time_end']."<br>";
		}

	}

?>

<!DOCTYPE HTML>
<html>
<head>
	<meta charset="UTF-8">
	<title>微信安全支付</title>
</head>
<body>
	</br></br>
	<div align="center">
		<form  action="./micropay_call.php" method="post">
			<p>请输入微信刷卡界面的一维码:</p>
			<p><input type="text" name="auth_code" value="<?php echo $auth_code; ?>"></p>
		    <button type="submit">提交</button>
		</form>
	</div>
		
	<div align="center">
		<p>订单号：<?php echo $out_trade_no; ?></p>
	</div>
	
	<div align="center">
	<table>
		<td>
		<form  action="./order_query.php" method="post">
			<input name="out_trade_no" type='hidden' value="<?php echo $out_trade_no; ?>">
		    <button type="submit" >查询订单状态</button>
		</form>
		</td>
		
		<td>
		<form  action="./refund.php" method="post">
			<input name="out_trade_no" type='hidden' value="<?php echo $out_trade_no; ?>">
			<input name="refund_fee" type='hidden' value="1">
		    <button type="submit" >申请退款</button>
		</form>
		</td>
		
		<td>
		<form  action="./reverse.php" method="post">
			<input name="out_trade_no" type='hidden' value="<?php echo $out_trade_no; ?>">
		    <button type="submit" >冲正</button>
		</form>
		</td>
	</table>
	</div>
		
	<br>
	<div align="center">
		<a href="../index.php">返回首页</a>
	</div>

</body>
</html>
