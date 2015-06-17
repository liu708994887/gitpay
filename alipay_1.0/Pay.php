<?php
class Pay {

    private $config;
    
    public function __construct(Config $config) {
        $this->config = $config; 
    } 

    //条码支付接口
    public function brpay($order_info, $total_amount, $bar_code) {
        $parameter = $this->assemblebrpayApiParams($order_info, $total_amount, $bar_code);
        $xml_res = $this->getHttpResponsePOST($parameter);
        if (! $xml_res) {
            throw new Exception('接口异常，请检查'); 
        }   
        $res_arr = $this->parseXML($xml_res);
        if ($res_arr['is_success'] == 'F') {
            throw new Exception('请求失败：' . var_export($res_arr, true)); 
        }
        $result_code = $res_arr['response']['alipay']['result_code'];
        if ($result_code == 'ORDER_FAIL' || $result_code == 'ORDER_SUCCESS_PAY_FAIL' || $result_code == 'UNKNOWN') {
            throw new Exception('下单或支付失败：' . var_export($res_arr, true)); 
        }

        return $result_code;
    }

    //拼装条码支付接口参数
    private function assemblebrpayApiParams($order_info, $total_amount, $bar_code) {
        $parameter = array();
        $parameter = array_merge($parameter, $this->getSystemParameter());
        $parameter['out_trade_no'] = $order_info['order_no'];
        $parameter['subject'] = $order_info['order_name'];
        $parameter['total_fee'] = $total_amount;
        $parameter['product_code'] = 'BARCODE_PAY_OFFLINE';
        $parameter['seller_email'] = $this->config->get('seller_email');
        //----业务相关，这里可以自定义
        $parameter['body'] = '支付宝条码支付';
        $parameter['goods_detail'] = json_encode(array(
            'goods_id' => 'apply-01',
            'goods_name' => 'iphone',
            'goods_category' => '7788230',
            'price' => '0.01',
            'quantity' => 1
        ));
        $parameter['it_b_pay'] = TIMEOUT_ALIPAY;//支付超时时间
        //----
        $parameter['dynamic_id_type'] = 'bar_code';
        $parameter['dynamic_id'] = $bar_code;
        //签名
        $parameter['sign'] = $this->generateSign($parameter);
        $parameter['sign_type'] = $this->config->get('sign_type');

        return $parameter; 
    }

    //查询接口
    public function query($order_no) {
        $parameter = $this->assemblequeryApiParams($order_no);
        $xml_res = $this->getHttpResponsePOST($parameter);
        if (! $xml_res) {
            throw new Exception('接口异常，请检查'); 
        }   
        $res_arr = $this->parseXML($xml_res);
        if ($res_arr['is_success'] == 'F') {
            throw new Exception('请求失败：' . var_export($res_arr, true)); 
        }
        $result_code = $res_arr['response']['alipay']['result_code'];
        if ($result_code == 'FAIL' || $result_code == 'PROCESS_EXCEPTION') {
            throw new Exception('查询失败：' . var_export($res_arr, true)); 
        }

        return $result_code;
    }

    //拼装查询接口参数 
    private function assemblequeryApiParams($order_no) {
        $parameter = array();
        $parameter = array_merge($parameter, $this->getSystemParameter());
        $parameter['out_trade_no'] = $order_no;
        //签名
        $parameter['sign'] = $this->generateSign($parameter);
        $parameter['sign_type'] = $this->config->get('sign_type');

        return $parameter; 
    }

    //获取系统参数 
    private function getSystemParameter() {
        $sys_para = array(); 
        $sys_para['service'] = 'alipay.acquire.query';
        $sys_para['partner'] = $this->config->get('partner');
        $sys_para['_input_charset'] = $this->config->get('input_charset');
        
        return $sys_para;
    }

    //条码支付签名
    private function generateSign(&$parameter) {
        //除去待签名参数数组中的空值和签名参数
        $parameter = paraFilter($parameter);
        //对待签名参数数组排序
        $parameter = argSort($parameter); 
        //把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
        $prestr = createLinkstring($parameter); 
        return md5($prestr . $this->config->get('key'));
    }

    //发送请求
    public function getHttpResponsePOST($para) {
        $url = $this->config->get('gate_way_url');
        $cacert = $this->config->get('cacert');
        $input_charset = $this->config->get('input_charset');
	    if (trim($input_charset) != '') {
	        $url = $url."_input_charset=".$input_charset;
	    }
	    $curl = curl_init($url);
	    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);//SSL证书认证
	    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);//严格认证
	    curl_setopt($curl, CURLOPT_CAINFO, $cacert);//证书地址
	    curl_setopt($curl, CURLOPT_HEADER, 0); // 过滤HTTP头
	    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);// 显示输出结果
	    curl_setopt($curl, CURLOPT_POST, true); // post传输数据
	    curl_setopt($curl, CURLOPT_POSTFIELDS, $para);// post传输数据
	    $responseText = curl_exec($curl);
	    //var_dump( curl_error($curl) );//如果执行curl过程中出现异常，可打开此开关，以便查看异常内容
	    curl_close($curl);

	    return $responseText;
    }

    //解析返回
    private function parseXML($xml_res) {
        $xml_obj = simplexml_load_string($xml_res);
        if (! $xml_obj) {
            throw new Exception('解析支付宝返回失败，请检查'); 
        }

        return json_decode(json_encode($xml_obj), true);
    }





    //预支付接口
    public function qrpay($order_info, $total_amount) {
        if ($total_amount < 0.01 || $total_amount > 100000000) {
            throw new Exception('金额有误，请检查');
        }
        $request = new AlipayTradePrecreateRequest($order_info, $total_amount);  
        $request->setBizContent($this->assembleApiParams($order_info, $total_amount));
        return $this->aopclient_request_execute($request);
    }

    //组装预支付api参数 
    private function assembleApiParams($order_info, $total_amount) {
        $biz_content = array();
        $biz_content['out_trade_no'] = $order_info['order_no'];
        $biz_content['total_amount'] = $total_amount;
        $biz_content['discountable_amount'] = '0.00';
        $biz_content['subject'] = $order_info['order_name'];
        $biz_content['body'] = $order_info['order_name'];
        $biz_content['goods_detail'] = array(
            'goods_id' => 'apply-01',
            'goods_name' => 'iphone',
            'goods_category' => '7788230',
            'price' => '0.01',
            'quantity' => 1
        );
        $biz_content['operator_id'] = 'op001';
        $biz_content['store_id'] = 'pudong001';
        $biz_content['terminal_id'] = 't_001';
        $biz_content['time_expire'] = date('Y-m-d H:i:s', time() + 60 * 60);
        return json_encode($biz_content);
    }  

    //撤销接口 
    public function cancel($order_no) {
        if (empty($order_no)) {
            throw new Exception('订单号不得为空');
        } 
        $request = new AlipayTradeCancelRequest();  
        $request->setBizContent($biz_content);
        $res = $this->aopclient_request_execute($request);
        if (is_object($res)) {
            $res = get_object_vars($res);  
            $result = $res['alipay_trade_query_response'];
        }
        if ($result['code'] == 10000) {
            return true; 
        } else {
            if ($result['retry_flag'] == 'Y') {
                return $this->cancel($order_no); 
            } else {
                throw new Exception($result['sub_msg']); 
            }
        }     
    }

    //退款接口
    public function refund($trade_no, $refund_amount) {
        if (empty($trade_no) || empty($refund_amount)) {
            throw new Exception('支付宝交易号和退款金额不得为空');
        }
        //@tudo校验退款金额是否超出订单金额 
        $biz_content = array();
        $biz_content['trade_no'] = $trade_no;
        $biz_content['refund_amount'] = $refund_amount;
        $biz_content['refund_reason'] = '支付宝退款';
        $biz_content['store_id'] = 'pudong001';
        $biz_content['terminal_id'] = 't_001';
        $request = new AlipayTradeRefundRequest();
        $request->setBizContent($biz_content);
        return $this->aopclient_request_execute($request);
    } 

    //执行接口请求
    public function aopclient_request_execute($request, $token = null) {
        $aop = new AopClient ();
        $aop->gatewayUrl = $this->config->get('gatewayUrl');
        $aop->appId = $this->config->get('app_id');
        $aop->rsaPrivateKeyFilePath = $this->config->get('merchant_private_key_file');
        $aop->apiVersion = "1.0";
        $result = $aop->execute ( $request, $token );
        //writeLog("response: ".var_export($result,true));
        return $result; 
    }       
}
