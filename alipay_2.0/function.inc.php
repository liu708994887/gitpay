<?php

//获取订单信息
function getOrderInfo() {
    $order_info = array();
    list($usec, $sec) = explode(' ', microtime());  
    $usec = ceil($usec * 1000);
    $order_info['order_no'] = intval(date('YmdHis') . $usec);
    $order_info['order_name'] = '订单' . $order_info['order_no'];
    return $order_info;
}

//打印结果
//status => success | fail
function jsonResult($status, $message, $data = null) {
    echo json_encode(array(
        'status' => $status,  
        'message' => $message,
        'data' => $data
    ));
    exit;
}

//记录日志
function writeLog($text) {
	// $text=iconv("GBK", "UTF-8//IGNORE", $text);
	$text = characet ( $text );
	file_put_contents ( dirname ( __FILE__ ) . "/log/log.txt", date ( "Y-m-d H:i:s" ) . "  " . $text . "\r\n", FILE_APPEND );
}

//转换编码
function characet($data) {
	if (! empty ( $data )) {
		$fileType = mb_detect_encoding ( $data, array (
				'UTF-8',
				'GBK',
				'GB2312',
				'LATIN1',
				'BIG5' 
		) );
		if ($fileType != 'UTF-8') {
			$data = mb_convert_encoding ( $data, 'UTF-8', $fileType );
		}
	}
	return $data;
}
