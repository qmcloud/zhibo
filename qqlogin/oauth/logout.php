<?php
    header("Content-type:text/html; charset=UTF-8;");
	if(!file_exists('../common/config.php')){
		header("Location:../install/index.php");
		exit;
	}
    include("../common/function.php");
    foreach($_SESSION as $key=>$v){
        unset($_SESSION[$key]);
    }
    header("Location:../index.php");