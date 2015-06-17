<?php 
/**
 * Native（原生）支付-模式二-demo
 * ====================================================
 * 商户生成订单，先调用统一支付接口获取到code_url，
 * 此URL直接生成二维码，用户扫码后调起支付。
 * 
*/
    include_once("WxPayPubHelper/WxPayPubHelper.php");
    $out_trade_no = 'liu' . time(); 
    $nonce_str = 'liu'. time(); 

    //使用统一支付接口
    $unifiedOrder = new UnifiedOrder_pub();
    
    //设置统一支付接口参数
    //设置必填参数
    //appid已填,商户无需重复填写
    //mch_id已填,商户无需重复填写
    //noncestr已填,商户无需重复填写
    //spbill_create_ip已填,商户无需重复填写
    //sign已填,商户无需重复填写
    $unifiedOrder->setParameter("body","贡献一分钱");//商品描述
    //自定义订单号，此处仅作举例
    $timeStamp = time();
    $unifiedOrder->setParameter("out_trade_no", $out_trade_no);//商户订单号 
    $unifiedOrder->setParameter("total_fee","1");//总金额
    $unifiedOrder->setParameter("notify_url",WxPayConf_pub::NOTIFY_URL);//通知地址 
    $unifiedOrder->setParameter("trade_type","NATIVE");//交易类型
    $unifiedOrder->setParameter("nonce_str",$nonce_str);//交易类型
    $unifiedOrder->setParameter("spbill_create_ip",long2ip("120.25.233.191"));//交易类型
    //非必填参数，商户可根据实际情况选填
    //$unifiedOrder->setParameter("sub_mch_id","XXXX");//子商户号  
    //$unifiedOrder->setParameter("device_info","XXXX");//设备号 
    //$unifiedOrder->setParameter("attach","XXXX");//附加数据 
    //$unifiedOrder->setParameter("time_start","XXXX");//交易起始时间
    //$unifiedOrder->setParameter("time_expire","XXXX");//交易结束时间 
    //$unifiedOrder->setParameter("goods_tag","XXXX");//商品标记 
    //$unifiedOrder->setParameter("openid","XXXX");//用户标识
    //$unifiedOrder->setParameter("product_id","XXXX");//商品ID
    
    //获取统一支付接口结果
    $unifiedOrderResult = $unifiedOrder->getResult();
    var_dump($unifiedOrderResult);
    
    //商户根据实际情况设置相应的处理流程
    if ($unifiedOrderResult["return_code"] == "FAIL") 
    {
        //商户自行增加处理流程
        echo "通信出错：".$unifiedOrderResult['return_msg']."<br>";
    }
    elseif($unifiedOrderResult["result_code"] == "FAIL")
    {
        //商户自行增加处理流程
        echo "错误代码：".$unifiedOrderResult['err_code']."<br>";
        echo "错误代码描述：".$unifiedOrderResult['err_code_des']."<br>";
    }
    elseif($unifiedOrderResult["code_url"] != NULL)
    {
        //从统一支付接口获取到code_url
        $code_url = $unifiedOrderResult["code_url"];
        //商户自行增加处理流程
        //生成二维码
    }

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>支付页面</title>
<link rel="stylesheet" href="css/index.css" type="text/css"/>
<script type="text/javascript" src="js/jquery-1.10.1.min.js"></script>
<script type="text/javascript" src="js/scrollpic2.js"></script>
<style>
    .show {
       display: show;  
    }
    .hidden {
       display: none;
    }
</style>

</head>

<body>
  <div class="pay">
    <div class="pay_top"></div>
    <div class="pay_center">
      <div class="pay_left">
        <div class="pay_method"> <strong>支付方式：</strong>
          <label>
            <input type="radio" name="pay_type" value="zhifubao">
            <img src="images/zf_03.jpg" width="114" height="40" /> </label>
          <label>
            <input type="radio" name='pay_type' value='weixin'>
            <img src="images/zf_05.jpg" width="90" height="40" /> </label>
        </div>
        <!--pay_method over-->
        <div class="pay_amount">
          <p>请输入金额：</p>
          <input type="text" value="" />
          <a href="#" ><img src="images/zf_10.jpg" width="98" height="36"/></a> </div>
        <!--pay_amount over-->
        <table width="90%" border="0">
          <tr>
            <td><a href="#"><img src="images/zf_14.jpg" width="103" height="43"/></a></td>
            <td>&nbsp;</td>
            <td><a href="#"><img src="images/zf_16.jpg" width="102" height="43"/></a></td>
            <td>&nbsp;</td>
            <td><a href="#"><img src="images/zf_18.jpg" width="102" height="43"/></a></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td><a href="#"><img src="images/zf_23.jpg" width="103" height="43"/></a></td>
            <td>&nbsp;</td>
            <td><a href="#"><img src="images/zf_24.jpg" width="102" height="43"/></a></td>
            <td>&nbsp;</td>
            <td><a href="#"><img src="images/zf_25.jpg" width="102" height="43"/></a></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td><a href="#"><img src="images/zf_32.jpg" width="103" height="43"/></a></td>
            <td>&nbsp;</td>
            <td><a href="#"><img src="images/zf_33.jpg" width="102" height="43"/></a></td>
            <td>&nbsp;</td>
            <td><a href="#"><img src="images/zf_34.jpg" width="102" height="43"/></a></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td><a href="#"><img src="images/zf_39.jpg" width="103" height="43"/></a></td>
            <td>&nbsp;</td>
            <td><a href="#"><img src="images/zf_40.jpg" width="102" height="43"/></a></td>
            <td>&nbsp;</td>
            <td><a href="#"><img src="images/zf_41.jpg" width="102" height="43"/></a></td>
          </tr>
        </table>
      </div>
      <!--pay_left over-->
      <div class="pay_right show" id="zhifubao_pay_right">
        <div class="pay_choice">
          <ul>
            <li><a href="javascript:void(0)" title="付款码" class="hover"  id="m_zl001"  onclick="set('m_zl0',1,3)"><img src="images/m_06.png" width="43" height="55"/></a></li>
            <li><a href="javascript:void(0)" id="m_zl002"  onclick="set('m_zl0',2,3)"><img src="images/m_08.png" width="43" height="55"/></a></li>
            <li><a href="javascript:void(0)" id="m_zl003"  onclick="set('m_zl0',3,3)"><img src="images/m_03.png" width="43" height="55"/></a></li>
          </ul>
        </div>
        <!--pay_choice over-->
        <div class="pay_choicef"  id="conm_zl001" style="display:block;">
            <p>扫描枪类型：条形码</p>
            <p>条形码数据：</p>
            <P>
              <input type="text" value=""/>
            </P>
            <a href="#"><img src="images/zf_30.jpg" width="97" height="36"/></a>
             <strong>请扫描支付宝付款码</strong> </div>
        <!-- pay_choicef over-->   
        <div class="pay_choicef"  id="conm_zl002" style="display:none;">
            <p>请扫码二维码:</p>
            <p>订单号：<?php echo $out_trade_no; ?></p>
            <strong>请扫描支付宝付款码</strong>
        </div>
        <!-- pay_choicef over--> 
                 <div class="pay_choicef"  id="conm_zl003" style="display:none;">
            <p>扫描枪类型：条形码</p>
            <p>条形码数据：</p>
            <P>
              <input type="text" value=""/>
            </P>
            <a href="#"><img src="images/zf_30.jpg" width="97" height="36"/></a>
             <strong>请扫描支付宝付款码</strong> </div>
         <!-- pay_choicef over-->  
      </div>

     <div class="pay_right hidden" id="weixin_pay_right">
        <div class="pay_choice">
          <ul>
            <li><a href="javascript:void(0)" title="付款码" class="hover"  id="m_zl01"  onclick="set('m_zl0',1,3)"><img src="images/m_06.png" width="43" height="55"/></a></li>
            <li><a href="javascript:void(0)" id="m_zl02"  onclick="set('m_zl0',2,3)"><img src="images/m_08.png" width="43" height="55"/></a></li>
            <li><a href="javascript:void(0)" id="m_zl03"  onclick="set('m_zl0',3,3)"><img src="images/m_03.png" width="43" height="55"/></a></li>
          </ul>
        </div>
        <!--pay_choice over-->
        <div class="pay_choicef"  id="conm_zl01" style="display:block;">
            <p>扫描枪类型：条形码</p>
            <p>条形码数据：</p>
            <P>
              <input type="text" value=""/>
            </P>
            <a href="#"><img src="images/zf_30.jpg" width="97" height="36"/></a>
             <strong>请扫描支付宝付款码</strong> </div>
        <!-- pay_choicef over-->   
        <div class="pay_choicef"  id="conm_zl02" style="display:none;">
            <p>请扫码二维码:</p>
            <p>订单号：<?php echo $out_trade_no; ?></p>
            <div align="center" id="qrcode"></div>
            <strong>请扫描支付宝付款码</strong>
        </div>
        <!-- pay_choicef over--> 
                 <div class="pay_choicef"  id="conm_zl03" style="display:none;">
            <p>扫描枪类型：条形码</p>
            <p>条形码数据：</p>
            <P>
              <input type="text" value=""/>
            </P>
            <a href="#"><img src="images/zf_30.jpg" width="97" height="36"/></a>
             <strong>请扫描支付宝付款码</strong> </div>
         <!-- pay_choicef over-->  
      </div>

    </div>
  </div>
</body>
</html>

<script src="./js/qrcode.js"></script>
<script>
$(function() {

    $("input[name='pay_type']").change(function() {
    
        var pay_type = $(":radio[name='pay_type']:checked").val();
        if (pay_type == 'weixin') {
            $("#weixin_pay_right, #zhifubao_pay_right").removeClass('show');
            $("#weixin_pay_right").addClass('show');
        } else {
            $("#weixin_pay_right, #zhifubao_pay_right").removeClass('show');
            $("#zhifubao_pay_right").addClass('show');
        }
    });

    if(<?php echo $unifiedOrderResult["code_url"] != NULL; ?>)
    {
        set('m_zl0',2,3);
        var url = "<?php echo $code_url;?>";
        //参数1表示图像大小，取值范围1-10；参数2表示质量，取值范围'L','M','Q','H'
        var qr = qrcode(10, 'M');
        qr.addData(url);
        qr.make();

        var code=document.createElement('DIV');
        code.innerHTML = qr.createImgTag();
        var element=document.getElementById("qrcode");
        element.appendChild(code);
    }
});
</script>
