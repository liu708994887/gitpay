<?php
/**
 * 冲正接口-demo
 * ====================================================
 * 
 * 
*/
	include_once("../WxPayMicropayHelper/WxPayMicropayHelper.php");

	//输入需冲正的订单号
	if (isset($_POST["out_trade_no"]))
	{
	    $out_trade_no = $_POST["out_trade_no"];
		
		//使用冲正接口
		$reverse = new Reverse_micropay();
		//设置必填参数
		//appid已填,商户无需重复填写
		//mch_id已填,商户无需重复填写
		//noncestr已填,商户无需重复填写
		//sign已填,商户无需重复填写
		$reverse->setParameter("out_trade_no","$out_trade_no");//商户订单号
		//非必填参数，商户可根据实际情况选填
		//$refund->setParameter("sub_mch_id","XXXX");//子商户号 
		//$refund->setParameter("transaction_id","XXXX");//微信订单号
		
		//调用结果
		$reverseResult = $reverse->getResult();
		
		//商户根据实际情况设置相应的处理流程,此处仅作举例
		if ($reverseResult["return_code"] == "FAIL") {
			echo "通信出错：".$reverseResult['return_msg']."<br>";
		}
		else{
			echo "业务结果：".$reverseResult['result_code']."<br>";
			echo "错误代码：".$reverseResult['err_code']."<br>";
			echo "错误代码描述：".$reverseResult['err_code_des']."<br>";
			echo "公众账号ID：".$reverseResult['appid']."<br>";
			echo "商户号：".$reverseResult['mch_id']."<br>";
			echo "子商户号：".$reverseResult['sub_mch_id']."<br>";
			echo "签名：".$reverseResult['sign']."<br>";
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
	</br></br></br></br>
	<div align="center">
		<form  action="./reverse.php" method="post">
			<p>冲正：</p>
			<p>单号: <input type="text" name="out_trade_no" value=<?php echo $out_trade_no; ?> ></p>
		    <button type="submit" >提交</button>
		</form>
		
		</br>
		<a href="../index.php">返回首页</a>

	</div>
</body>
</html>