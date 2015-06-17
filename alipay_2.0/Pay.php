<?php
class Pay {

    private $config;
    
    public function __construct(Config $config) {
        $this->config = $config; 
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

    //查询接口
    final public function query($order_no) {
        if (empty($order_no)) {
            throw new Exception('查询订单号不得为空'); 
        }  
        $biz_content = array('out_trade_no' => $order_no);
        $request = new AlipayTradeQueryRequest();
        $request->setBizContent($biz_content);
        return $this->aopclient_request_execute($request);
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
