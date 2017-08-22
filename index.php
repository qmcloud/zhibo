<?php 
/* 松松改良版 
 * 除了支持 Fms 服务器的推流 介入阿里直播等第三方直播接入
 * 支付宝 微信扫码 qq支付等
 * 优化即时聊天系统  礼物派送效果等
 * 优化TP 框架 加入redis 集群 系统性能优化
 * 界面优化 
 * 
 *  */
//定义项目名称和路径
define('APP_NAME','Liveshow',true);
define('APP_PATH', './',true);
define('SHOW_PAGE', '/index.php/Show/index/roomnum/');
// 加载框架入口文件

include "config.php";
require( "./Core/ThinkPHP.php");

?> 