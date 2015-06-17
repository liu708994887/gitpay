<?php
//签名方式 不需修改
$config['sign_type']    = strtoupper('MD5');
//字符编码格式 目前支持 gbk 或 utf-8
$config['input_charset']= strtolower('utf-8');
//ca证书路径地址，用于curl中ssl校验
//请保证cacert.pem文件在当前文件夹目录中
$config['cacert']    = getcwd().'/cacert.pem';
//访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
$config['transport']    = 'http';
//访问网关
$config['gate_way_url'] = 'https://mapi.alipay.com/gateway.do?';
