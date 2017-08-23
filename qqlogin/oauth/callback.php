<?php
    header("Content-type:text/html; charset=UTF-8;");
	error_reporting(0); 
	if(!file_exists('../common/config.php')){
		header("Location:../install/index.php");
		exit;
	}
    include_once("../common/function.php");
	if($aConfig["debug"]==1){
?>
<html>
<head>
<link href="../style/default.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="../js/jquery.js"></script>
</head>
<body>
<h1>QQ互联集成PHP SDK - OAuth2.0 登录返回第二三步</h1>
<div class="list-div">
<table>
<?php
	}
    //if(!isset($_GET["state"])||empty($_GET["state"])||!isset($_GET["code"])||empty($_GET["code"])){
    //    echo "QQ第一步获取参数失败。<br />";
    //}else{
	//	if($_GET["state"]!=$_SESSION["state"]){
	//		echo "网站获取用于第三方应用防止CSRF攻击失败。<br />";
	//		exit;
	//	}
		$sUrl = "https://graph.qq.com/oauth2.0/token";
		$aGetParam = array(
			"grant_type"    =>    "authorization_code",
			"client_id"        =>    $aConfig["appid"],
			"client_secret"    =>    $aConfig["appkey"],
			"code"            =>    $_GET["code"],
			"state"            =>    $_GET["state"],
			"redirect_uri"    =>    $_SESSION["URI"]
		);
		unset($_SESSION["state"]);
		unset($_SESSION["URI"]);
		$sContent = get($sUrl,$aGetParam);

		if($sContent!==FALSE){
			$aTemp = explode("&", $sContent);
			$aParam = array();
			foreach($aTemp as $val){
				$aTemp2 = explode("=", $val);
				$aParam[$aTemp2[0]] = $aTemp2[1];
			}
			$_SESSION["access_token"] = $aParam["access_token"];
			$sUrl = "https://graph.qq.com/oauth2.0/me";
			$aGetParam = array(
				"access_token"    => $aParam["access_token"]
			);
			$sContent = get($sUrl, $aGetParam);
			if($sContent!==FALSE){
				$aTemp = array();
				preg_match('/callback\(\s+(.*?)\s+\)/i', $sContent,$aTemp);
				$aResult = json_decode($aTemp[1],true);
				$_SESSION["openid"] = $aResult["openid"];
				if(intval($aConfig["debug"])==1){
					echo "<tr><td class='narrow-label'></td><td><input type='button' onclick='window.location.href=\"/index.php/ThirdParty/qqlogin/\";' class='button' value='点此返回首页' /></td></tr>";
				}else{
					echo "<script>window.location.href='/index.php/ThirdParty/qqlogin/';</script>";
					exit;
				}
			}
		}
    //}
	if($aConfig["debug"]==1){
?>
</table>
</div>
</body>
</html>
<?php
	}
?>