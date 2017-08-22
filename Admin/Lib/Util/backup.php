<?php
error_reporting(0);
ob_start();
include("./lead_db.php");

if($_POST['hiddenField']){
	if($_POST['weizhi']=="localpc"&&$_POST['fenjuan']=='yes'){
		$msgs[]="只有选择备份到服务器，才能使用分卷备份功能";
		show_msg($msgs); 
		pageend();
	}

	if($_POST['fenjuan']=="yes"&&!$_POST['filesize']){
		$msgs[]="您选择了分卷备份功能，但未填写分卷文件大小";
		show_msg($msgs); 
		pageend();
	}

	if($_POST['weizhi']=="server"&&!writeable("../../backup")){
		$msgs[]="备份文件存放目录'./backup'不可写，请修改目录属性";
		show_msg($msgs); 
		pageend();
	}

	/*----------备份全部表-------------*/
	if($_POST['bfzl']=="quanbubiao"){//备份全部表

		if(!$_POST['fenjuan']){//不分卷
			if(!$tables=$d->query("show table status from $mysqldb")){//读数据库结构
				$msgs[]="读数据库结构错误"; 
				show_msg($msgs); 
				pageend();
			}

			$sql="";
			while($d->nextrecord($tables)){
				$table=$d->f("Name");
				$sql.=make_header($table);
				$d->query("select * from $table");
				$num_fields=$d->nf();
				while($d->nextrecord()){
					$sql.=make_record($table,$num_fields);
				}
			}
	
			$filename=date("Ymd",time())."_all.sql";//文件名
			if($_POST['weizhi']=="localpc") down_file($sql,$filename);//保存到本地
			elseif($_POST['weizhi']=="server"){//保存到服务器
				if(write_file($sql,$filename)) 
					$msgs[]="全部数据表数据备份完成,生成备份文件'./backup/$filename'";//成功
				else $msgs[]="备份全部数据表失败";//失败
				show_msg($msgs);
				pageend();
			}
		}else{//--如果分卷------------------------*/
			if(!$_POST['filesize']){
				$msgs[]="请填写备份文件分卷大小"; 
				show_msg($msgs);
				pageend();
			}

			if(!$tables=$d->query("show table status from $mysqldb")){
				$msgs[]="读数据库结构错误"; 
				show_msg($msgs); 
				pageend();
			}

			$sql=""; 
			$p=1;
			$filename=date("Ymd",time())."_all";
			while($d->nextrecord($tables)){
				$table=$d->f("Name");
				$sql.=make_header($table);
				$d->query("select * from $table");
				$num_fields=$d->nf();
				while($d->nextrecord()){
					$sql.=make_record($table,$num_fields);
					if(strlen($sql)>=$_POST['filesize']*1000){
						$filename.=("_v".$p.".sql");
						if(write_file($sql,$filename))
							$msgs[]="全部数据表 [卷-".$p."] 数据备份完成,生成备份文件'./backup/$filename'";
						else $msgs[]="备份表 [".$_POST['tablename']."] 失败";
						$p++;
						$filename=date("Ymd",time())."_all";
						$sql="";
					}
				}
			}

			if($sql!=""){
				$filename.=("_v".$p.".sql");		
				if(write_file($sql,$filename))
					$msgs[]="[卷-".$p."] 数据备份完成,生成备份文件'./backup/$filename'<br /><strong>全部数据备份完成！！</strong>";
			}
			show_msg($msgs);
		/*---------------------分卷结束*/
		}
	/*--------备份全部表结束*/
	}elseif($_POST['bfzl']=="danbiao"){/*----备份单表-----*/
		if(!$_POST['tablename']){
			$msgs[]="请选择要备份的数据表"; 
			show_msg($msgs); 
			pageend();
		}

		if(!$_POST['fenjuan']){/*--不分卷--*/
			$sql=make_header($_POST['tablename']);
			$d->query("select * from ".$_POST['tablename']);
			$num_fields=$d->nf();
			while($d->nextrecord()){
				$sql.=make_record($_POST['tablename'],$num_fields);
			}
			$filename=date("Ymd",time())."_".$_POST['tablename'].".sql";
			if($_POST['weizhi']=="localpc") down_file($sql,$filename);
			elseif($_POST['weizhi']=="server"){
				if(write_file($sql,$filename))
					$msgs[]="表 [".$_POST['tablename']."] 数据备份完成,生成备份文件'./backup/$filename'";
				else
					$msgs[]="备份表 [".$_POST['tablename']."] 失败";
				show_msg($msgs);
				pageend();
			}
			/*----------------不要卷结束*/
		}else{/*-------分卷-------------------------------*/
			if(!$_POST['filesize']){
				$msgs[]="请填写备份文件分卷大小"; 
				show_msg($msgs);
				pageend();
			}

			$sql=make_header($_POST['tablename']); 
			$p=1; 
			$filename=date("Ymd",time())."_".$_POST['tablename'];
			$d->query("select * from ".$_POST['tablename']);
			$num_fields=$d->nf();
			while ($d->nextrecord()){	
				$sql.=make_record($_POST['tablename'],$num_fields);
				if(strlen($sql)>=$_POST['filesize']*1000){
					$filename.=("_v".$p.".sql");
					if(write_file($sql,$filename))
						$msgs[]="表 -".$_POST['tablename']." [卷-".$p."] 数据备份完成,生成备份文件'./backup/$filename'";
					else 
						$msgs[]="备份表 -".$_POST['tablename']."- 失败";
					$p++;
					$filename=date("Ymd",time())."_".$_POST['tablename'];
					$sql="";
				}
			}

			if($sql!=""){$filename.=("_v".$p.".sql");		
			if(write_file($sql,$filename))
				$msgs[]="表 -".$_POST['tablename']." [卷-".$p."] 数据备份完成,生成备份文件'./backup/$filename'<br /><strong>全部数据备份完成！！</strong>";}
			show_msg($msgs);
			/*----------分卷结束*/
		}
	/*----------备份单表结束*/
	}

}
/*-------------主程序结束------------------------------------------*/


//== 文件生成函数 ====================================================
function write_file($sql,$filename)
{
	$re=true;
	if(!@$fp=fopen("../../backup/".$filename,"w+")) {$re=false; echo "打开文件失败";}
	if(!@fwrite($fp,$sql)) {$re=false; echo "写文件失败";}
	if(!@fclose($fp)) {$re=false; echo "关闭文件失败";}
	return $re;
}

//== 文件下载函数 ====================================================
function down_file($sql,$filename)
{
	ob_end_clean();
	header("Content-Encoding: none");
	header("Content-Type: ".(strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') ? 'application/octetstream' : 'application/octet-stream'));
			
	header("Content-Disposition: ".(strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') ? 'inline; ' : 'attachment; ')."filename=".$filename);
			
	header("Content-Length: ".strlen($sql));
	header("Pragma: no-cache");
			
	header("Expires: 0");
	echo $sql;
	$e=ob_get_contents();
	ob_end_clean();
}

//== 目录权限判断 ====================================================
function writeable($dir){	
	if(!is_dir($dir)){
		@mkdir($dir, 0777);
	}	
	if(is_dir($dir)){	
		if($fp = @fopen("$dir/test.test", 'w')){
			@fclose($fp);
			@unlink("$dir/test.test");
			$writeable = 1;	
		}else{
			$writeable = 0;
		}	
	}
	return $writeable;
}


//== 目录权限判断 ====================================================
function make_header($table){
	global $d;
	$sql="DROP TABLE IF EXISTS ".$table."\n";
	$d->query("show create table ".$table);
	$d->nextrecord();
	$tmp=preg_replace("/\n/","",$d->f("Create Table"));
	$sql.=$tmp."\n";
	return $sql;
}

//== SQL结构生成函数 ====================================================
function make_record($table,$num_fields){
	global $d;
	$comma="";
	$sql .= "INSERT INTO ".$table." VALUES(";
	for($i = 0; $i < $num_fields; $i++){
		$sql .= ($comma."'".mysql_escape_string($d->record[$i])."'"); 
		$comma = ",";
	}
	$sql .= ")\n";

	//替换相关内容
	return $sql;
}

//== 提示函数 ====================================================================
function show_msg($msgs)
{
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<style type="text/css">
<!--
.bd_in340 {
	width:310px;
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
.bd_in352 {
	width:330px;
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
.STYLE1 {
	font-family: "宋体";
	font-size: 12px;
	color: #FF3300;
	font-weight: bold;
}
-->
</style>
</head>
<body style="background-color:transparent">
<?php
$title="提示：";
echo "<table width='100%' border='0'  cellpadding='0' cellspacing='2' class='yy'>";
echo "<tr><td height='3'></td></tr>";
echo "<tr><td class='txt'>".$title."</td></tr>";
echo "<tr><td><ul>";
while (list($k,$v)=each($msgs))
{
	echo "<li class='txt'>".$v."</li>";
}
echo "<li class='txt'><a href=\"/Admin/index.php/Index/backup_database/\">返回</a></li></ul></td></tr></table>";
?>
</tr>
</table>
</body>
</html>
<?php
}
function pageend()
{
	exit();
}
?>