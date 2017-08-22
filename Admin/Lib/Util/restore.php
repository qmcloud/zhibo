<?php
error_reporting(0);
session_start();
include("./lead_db.php");
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<style type="text/css">
<!--
.bd_in352 {
	width:340px;
	font-family: "宋体";
	font-size: 12px;
	font-style: normal;
	font-weight: normal;
	font-variant: normal;
	border:1px solid #0099CC;
	height: 20px;
	padding-top: 2px;
	padding-right: 2px;
	padding-bottom: 0;
	padding-left: 2px;
	line-height: 16px;
}
body {
	margin-left: 0px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
}
.input_Submit {
	BACKGROUND-COLOR: #f5f5f5;
	COLOR: #000000;
 FONT-FAMILY:font-family:Tahoma, Verdana;
	FONT-SIZE: 12px;
	BACKGROUND: url(style/Submit.gif) #ffffff;
	BORDER: 1px solid #666666;
	height: 22px;
	padding-top: 2px;
	padding-right: 5px;
	padding-left: 5px;
	width: 60px;
}
.yy {
	filter: DropShadow(Color=#ffffff, OffX=1, OffY=1, Positive=1);
}
.bd_txt {
	width:54px;
	font-family: "宋体";
	font-size: 12px;
	font-weight: normal;
	color: #003366;
	text-align: right;
	padding-right: 3px;
}
.txt {
	font-family: "宋体";
	font-size: 12px;
	font-weight: normal;
	color: #003366;
	padding-right: 3px;
}
-->
</style>
</head>
<body style="background-color:transparent">
<?
/****************************主程序*/
if($_POST['hiddenField']){
	if($_POST['restorefrom']=="server"){/***************服务器恢复*/
		if(!$_POST['serverfile']){
			$msgs[]="您选择从服务器文件恢复备份，但没有指定备份文件";
			show_msg($msgs); 
			pageend();	
		}

		if(!eregi("_v[0-9]+",$_POST['serverfile'])){
			$filename="../../backup/".$_POST['serverfile'];
			if(import($filename)) $msgs[]="备份文件 [".$_POST['serverfile']."] 成功导入数据库";
			else $msgs[]="备份文件 [".$_POST['serverfile']."] 导入失败";show_msg($msgs); pageend();	
		}else{
			$filename="../../backup/".$_POST['serverfile'];
			if(import($filename)) $msgs[]="备份文件 [".$_POST['serverfile']."] 成功导入数据库";
			else {$msgs[]="备份文件 [".$_POST['serverfile']."] 导入失败";show_msg($msgs);pageend();}

			$voltmp=explode("_v",$_POST['serverfile']);
			$volname=$voltmp[0];
			$volnum=explode(".sq",$voltmp[1]);
			$volnum=intval($volnum[0])+1;
			$tmpfile=$volname."_v".$volnum.".sql";
			if(file_exists("../../backup/".$tmpfile)){
				$msgs[]="程序将在3秒钟后自动开始导入此分卷备份的下一部份：文件 [".$tmpfile."] ，请勿手动中止程序的运行，以免数据库结构受损";
				$_SESSION['data_file']=$tmpfile;
				show_msg($msgs);
				sleep(3);
				echo "<script language='javascript'>"; 
				echo "location='restore.php';"; 
				echo "</script>"; 
			}else{
				$msgs[]="<strong>全部数据导入成功！！</strong>";
				show_msg($msgs);
			}
		}
		/**************服务器恢复结束*/
	}
	if($_POST['restorefrom']=="localpc"){/*****从本地文件恢复*********/
		switch ($_FILES['myfile']['error']){
			case 1:
			case 2:
				$msgs[]="您上传的文件大于服务器限定值，上传未成功";
				break;
			case 3:
				$msgs[]="未能从本地完整上传备份文件";
				break;
			case 4:
				$msgs[]="从本地上传备份文件失败";
				break;
			case 0:
				break;
		}
		if($msgs){show_msg($msgs);pageend();}
		//$fname=date("Ymd",time())."_".sjs(5).".sql";
		$fname=date("Ymd",time())."_up.sql";
		if (is_uploaded_file($_FILES['myfile']['tmp_name'])){
			copy($_FILES['myfile']['tmp_name'], "../../backup/".$fname);
		}

		if (file_exists("../../backup/".$fname)){
			$msgs[]="本地备份文件上传成功";
			if(import("../../backup/".$fname)){$msgs[]="本地备份文件成功导入数据库"; unlink("../../backup/".$fname);}
			else $msgs[]="本地备份文件导入数据库失败";
		}
		else($msgs[]="从本地上传备份文件失败");
		show_msg($msgs);

	/****本地恢复结束*****/
	}
/****************************主程序结束*/
}

/*************************剩余分卷备份恢复**********************************/
if(!$_POST['hiddenField']&&$_SESSION['data_file']){
	$filename="../../backup/".$_SESSION['data_file'];
	if(import($filename)) $msgs[]="备份文件 [".$_SESSION['data_file']."] 成功导入数据库";
	else {$msgs[]="备份文件".$_SESSION['data_file']."导入失败";show_msg($msgs);pageend();}
	$voltmp=explode("_v",$_SESSION['data_file']);
	$volname=$voltmp[0];
	$volnum=explode(".sq",$voltmp[1]);
	$volnum=intval($volnum[0])+1;
	$tmpfile=$volname."_v".$volnum.".sql";
	if(file_exists("../../backup/".$tmpfile)){
		$msgs[]="程序将在3秒钟后自动开始导入此分卷备份的下一部份：文件 [".$tmpfile."] ，请勿手动中止程序的运行，以免数据库结构受损";
		$_SESSION['data_file']=$tmpfile;
		show_msg($msgs);
		sleep(3);
		echo "<script language='javascript'>"; 
		echo "location='restore.php';"; 
		echo "</script>"; 
	}
	else
	{
		$msgs[]="<strong>全部数据导入成功！！</strong>";
		unset($_SESSION['data_file']);
		show_msg($msgs);
	}
}
/**********************剩余分卷备份恢复结束*******************************/

//== 导入数据 ====================================================
function import($fname){
	global $d;
	$sqls=file($fname);
	foreach($sqls as $sql){
		str_replace("\r","",$sql);
		str_replace("\n","",$sql);
		if(!$d->query(trim($sql))) return false;
	}
	return true;
}


function show_msg($msgs)
{
	$title="提示：";
	echo "<table width='100%' border='0'  cellpadding='0' cellspacing='2' class='yy'>";
	echo "<tr><td height='3'></td></tr>";
	echo "<tr><td class='txt'>".$title."</td></tr>";
	echo "<tr><td><ul>";
	while (list($k,$v)=each($msgs))
	{
		echo "<li class='txt'>".$v."</li>";
	}
	echo "<li class='txt'><a href=\"/Admin/index.php/Index/restore_database/\">返回</a></li></ul></td></tr></table>";
}

function pageend()
{
	exit();
}
?>
</body>
</html>