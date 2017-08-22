<?php 
		  
$config=array (
		  'DB_TYPE' => 'mysql',
		  'DB_HOST' => '127.0.0.1',
		  'DB_NAME' => 'zb',
		  'DB_USER' => 'root',
		  'DB_PWD' => 'root',
		  'DB_PORT' => '3306',
		  'DB_PREFIX' => 'ss_',
		  'SHOW_ERROR_MSG' => true,
		  'HTML_CACHE_ON' => '0',   
		  'HTML_CACHE_RULES' => 
		  array (
			'*' => 
			array (
			  0 => '\{$_SERVER.REQUEST_URI|md5}',
			  1 => 300,
			),
		  ),
		  'HTML_CACHE_TIME' => '605',
		  'HTML_READ_TYPE' => '0',
		  'HTML_FILE_SUFFIX' => '.html',
		  'TMPL_ACTION_ERROR' => 'Public:error',
		  'TMPL_ACTION_SUCCESS' => 'Public:success',
		  'DEFAULT_THEME' => 'Newtpl',
		  'URL_MODEL'=>'2'

		);

define('UC_DBHOST', 'localhost');
return $config;

?>

