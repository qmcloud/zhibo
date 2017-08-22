<?php
function getEmceelevel($earnbean) {
	$level = D("Emceelevel")->where("earnbean_up>=".$earnbean." and earnbean_low<=".$earnbean)->field('levelid,levelname')->order('levelid asc')->select();
	return $level;
}
//判断是否是手机端登录
function ismobile() {

     // 如果有HTTP_X_WAP_PROFILE则一定是移动设备
     if (isset ($_SERVER['HTTP_X_WAP_PROFILE']))
         return true;     
     //此条摘自TPM智能切换模板引擎，适合TPM开发
     if(isset ($_SERVER['HTTP_CLIENT']) &&'PhoneClient'==$_SERVER['HTTP_CLIENT'])
         return true;
     //如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
     if (isset ($_SERVER['HTTP_VIA']))
         //找不到为flase,否则为true
         return stristr($_SERVER['HTTP_VIA'], 'wap') ? true : false;
     //判断手机发送的客户端标志,兼容性有待提高
     if (isset ($_SERVER['HTTP_USER_AGENT'])) {
         $clientkeywords = array(
             'nokia','sony','ericsson','mot','samsung','htc','sgh','lg','sharp','sie-','philips','panasonic','alcatel','lenovo','iphone','ipod','blackberry','meizu','android','netfront','symbian','ucweb','windowsce','palm','operamini','operamobi','openwave','nexusone','cldc','midp','wap','mobile'
         );
         //从HTTP_USER_AGENT中查找手机浏览器的关键字
         if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))) {
             return true;
         }
     }
     //协议法，因为有可能不准确，放到最后判断
     if (isset ($_SERVER['HTTP_ACCEPT'])) {
         // 如果只支持wml并且不支持html那一定是移动设备
         // 如果支持wml和html但是wml在html之前则是移动设备
         if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))) {
             return true;
        }
     }
     return false;
}
function fDate($l1,$l2=0){
    if (strlen($l1)==0) { return ; }
    switch ($l2) {
        case '0':
        	$I1 = date('Y-m-d H:i:s',$l1);
            break;
        case '1':
            $I1 = date('Y-n-j G:i:s',$l1);
            break;
        case '2':
        	$I1 = date('Y-m-d',$l1);
            break;
        case '3':
            $I1 = date('Y-n-j',$l1);
            break;
		case '4':
			$I1 = date('Y年m月d日',$l1);
            break;
		case '5':
			$I1 = date('m月 Y',$l1);
            break;
		case '6':
			$I1 = date('Y-m',$l1);
            break;
        default:
            $I1 = date($l2,$l1);
            break;
    }
    return $I1;
}

function getChannelStr($channelId)
{
	$dao = D("Votechannel");
	$channel = $dao->getById($channelId);
	if($channel){
		return $channel['channelstr'];
	}
}


?>