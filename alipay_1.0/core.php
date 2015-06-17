<?php
//error_reporting(E_ALL &~ E_NOTICE);
require_once 'function.inc.php';
require_once 'Pay.php';
require_once 'Config.php';
require_once 'Conf.php';

//定义交易过期时间 
define('TIMEOUT_TS', 3600);
define('TIMEOUT_ALIPAY', '1h');

mb_internal_encoding('UTF-8');
date_default_timezone_set('Asia/Shanghai');

$action = $_GETT['action'];
$pay = new Pay(new Config());                 

switch ($action) {
    case 'brpay'://条码支付下单
        //@tudo生成订单信息 
        $total_amount = (float)$_POST['total_amount'];
        $bar_code = trim($_POST['bar_code']);
        $order_info = getOrderInfo();
        try {
            $res = $pay->brpay($order_info, $total_amount, $bar_code);
        } catch (Exception $e) {
            //关闭订单，记录错误原因$e->getMessage()
            return jsonResult('failed', '下单或支付失败');        
            //return jsonResult('failed', $e->getMessage());        
        }
        $need_query = array('query' => 0);
        if ($res['response']['alipay']['result_code'] == 'ORDER_SUCCESS_PAY_SUCCESS') {
            //关闭订单，记录支付信息
            return jsonResult('success', '支付成功', $need_query); 
        } else if ($res['response']['alipay']['result_code'] == 'ORDER_SUCCESS_PAY_INPROCESS') {
            $need_query['query'] = 1; 
            $need_query['order_no'] = $res['response']['alipay']['out_trade_no'];
            $need_query['order_ts'] = $order_info['create_ts'];
            return jsonResult('success', '下单成功，等待用户付款', $need_query); 
        } else {
            //关闭订单，记录错误原因
            return jsonResult('failed', '下单失败，未知错误'); 
        }
        break;
    case 'query'://查询
        $order_no = trim($_GET['order_no']);
        $order_ts = intval($_GET['order_ts']);
        try {
            $res = $pay->query($order_no);
        } catch (Exception $e) {
            return jsonResult('failed', '查询失败');        
        }
        $need_query = array('query' => 0);
        if ($res['response']['alipay']['result_code'] == 'SUCCESS') {
            $trade_status = $res['response']['alipay']['trade_status']; 
            if ($trade_status == 'WAIT_BUYER_PAY') {
                //比较订单生成时间，判断交易是否超时，如果超时则撤销订单 
                if ($order_ts < time() - TIMEOUT_TS) {
                    //关闭订单，记录撤销原因
                    //$pay->cancel($order_no);
                    return jsonResult('success', '交易超时，已撤销', $need_query);        
                }
                $need_query['query'] = 1; 
                $need_query['order_no'] = $order_no;
                $need_query['order_ts'] = $order_ts;
                return jsonResult('success', '等待用户付款', $need_query);        
            } else if ($trade_status == 'TRADE_CLOSED') {
                return jsonResult('success', '交易关闭', $need_query);        
            } else if ($trade_status == 'TRADE_PENDING') {
                return jsonResult('success', '收款账号被冻结', $need_query);        
            } else if ($trade_status == 'TRADE_SUCCESS' || $trade_status == 'TRADE_FINISHED') {
                //@tudo检查支付金额是否正确,如果不正确需要记录支付信息入订单  
                return jsonResult('success', '交易成功', $need_query);        
            } else {
                return jsonResult('success', '交易异常', $need_query);        
            }
        }
        return jsonResult('failed', '查询失败');        
        break;
    case 'notify':
        $pay->notifyDeal();  
        echo 'success';
        break;
    case 'qrpay'://预下单
        $total_amount = (float)$_POST['total_amount'];
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
            $data = array(
                'out_trade_no' => $result['out_trade_no'],
                'qr_code' => $result['qr_code'], 
            );
            jsonResult('success', '预下单成功', $data);        
        }
        jsonResult('success', '预下单失败', $result);        
        break;
    default:
        echo 'wrong action!plese check';
        break;
}
