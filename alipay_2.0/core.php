<?php
require_once 'AopSdk.php';
require_once 'function.inc.php';
require_once 'Pay.php';
require_once 'QrCode.php';
require_once 'Config.php';
require_once 'Conf.php';

mb_internal_encoding('UTF-8');
date_default_timezone_set('Asia/Shanghai');

$action = $_GET['action'];

switch ($action) {
    case 'qrpay'://预下单
        $total_amount = (float)$_POST['total_amount'];
        $pay = new Pay(new Config());                 
        try {
            $res = $pay->qrpay(getOrderInfo(), $total_amount);
            if (is_object($res)) {
                $res = get_object_vars($res);  
                $result = $res['alipay_trade_precreate_response'];
            }
        } catch (Exception $e) {
            jsonResult('success', '预下单失败', $e->getMessage());        
        }
        if ($result['code'] == 10000) {
            //@tudo创建订单
            $data = array(
                'out_trade_no' => $result['out_trade_no'],
                'qr_code' => $result['qr_code'], 
            );
            jsonResult('success', '预下单成功', $data);        
        }
        jsonResult('success', '预下单失败', $result);        
        break;
    case 'query'://查询
        $order_no = $_GET['order_no'];
        $pay = new Pay(new Config());                 
        try {
            $res = $pay->query($order_no);
            if (is_object($res)) {
                $res = get_object_vars($res);  
                $result = $res['alipay_trade_query_response'];
            }
        } catch (Exception $e) {
            jsonResult('failed', '查询失败', $e->getMessage());        
        }
        if ($result['code'] == 10000) {
            $trade_status = $result['trade_status']; 
            if ($trade_status == 'WAIT_BUYER_PAY') {
                jsonResult('success', '等待用户付款', $trade_status);        
            } else if ($trade_status == 'TRADE_CLOSED') {
                //@tudo关闭订单,撤销支付宝订单,记录订单支付失败原因和撤销支付宝订单情况
                
                jsonResult('success', '交易超时', $trade_status);        
            } else if ($trade_status == 'TRADE_SUCCESS') {
                //@tudo检查支付金额是否正确,如果不正确走退款api,正确则记录支付信息入订单  
                jsonResult('success', '交易成功', $trade_status);        
            } else if ($trade_status == 'TRADE_FINISHED') {
                jsonResult('success', '交易已结束', $trade_status);        
            }
            //发生异常,关闭订单,撤销支付宝订单,记录订单支付失败原因和撤销支付宝订单情况
            jsonResult('success', '交易异常', 'TRADE_EXCEPTION');        
        }
        jsonResult('failed', $result['sub_msg']);        
        break;
    default:
        echo 'wrong action!plese check';
        break;
}
