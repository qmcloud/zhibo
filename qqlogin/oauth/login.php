<?php
    header("Content-type:text/html; charset=UTF-8;");
	if(!file_exists('../common/config.php')){
		header("Location:../install/index.php");
		exit;
	}
include "../common/function.php";
if($aConfig["debug"]==1){
?>
<html>
<head>
<link href="../style/default.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="../js/jquery.js"></script>
</head>
<body>
<h1>QQ互联集成PHP SDK - OAuth2.0 登录第一步</h1>
<div class="list-div">
<table>
<?php
}
/*
 *
 * 参考：http://wiki.open.qq.com/wiki/%E3%80%90QQ%E7%99%BB%E5%BD%95%E3%80%91%E4%BD%BF%E7%94%A8Authorization_Code%E8%8E%B7%E5%8F%96Access_Token
 */
$sState = md5(date("YmdHis".getip()));
$_SESSION["state"] = $sState;
$aScope = array();
foreach($aConfig["api"] as $key=>$val){
    if($val==1){
        $aScope[] = $key;
    }
}
$sUri =  "http://".$_SERVER["HTTP_HOST"].str_replace("/oauth/login.php", "/oauth/callback.php", $_SERVER["REQUEST_URI"]);
$_SESSION["URI"] = $sUri;
$aParam = array(
    "response_type"    => "code",
    "client_id"        =>    $aConfig["appid"],
    "redirect_uri"    =>    $sUri,
    "scope"            =>    join(",", $aScope),
    //"state"            =>    $sState
);
$aGet = array();
foreach($aParam as $key=>$val){
    $aGet[] = $key."=".urlencode($val);
}
$sUrl = "https://graph.qq.com/oauth2.0/authorize?";
$sUrl .= join("&",$aGet);
if(intval($aConfig["debug"])==1){
    echo "<tr><td class='narrow-label'>网站地址:</td><td><pre>".$sUrl."</pre></td></tr>";
    echo "<tr><td class='narrow-label'>请求参数:</td><td><pre>".var_export($aParam,true)."</pre></td></tr>";
    echo "<tr><td class='narrow-label'></td><td><input type='button' onclick=\"location.href='".$sUrl."';\" class='button' value='点此进入QQ登录' /></td></tr>";
    echo "</table></div></html>";
}else{
    header("location:".$sUrl);
}