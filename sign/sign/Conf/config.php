<?php
return array(
	//'配置项'=>'配置值'
	'TMPL_PARSE_STRING' => array(
		'__PUBLIC__' => __ROOT__.'/'.APP_NAME.'/Public'),

	//数据库配置
    'DB_TYPE'               => 'mysql',     // 数据库类型
    'DB_HOST'               => 'localhost', // 服务器地址
    'DB_NAME'               => 'sign',          // 数据库名
    'DB_USER'               => 'root',      // 用户名
    'DB_PWD'                => 'tieweishi.vps',          // 密码
    'DB_PORT'               => '3306',        // 端口
    'DB_PREFIX'             => 'hx_',    // 数据库表前缀
    'DB_CHARSET'            => 'utf8',      // 数据库编码默认采用utf8
);
?>