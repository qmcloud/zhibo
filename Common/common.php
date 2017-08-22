<?php
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





function getSortname($sid){
	$sortinfo = D("Usersort")->find($sid);
	if($sortinfo){
		return $sortinfo['sortname'];
	}
}

function getSortOnline($sid){
	//$onlinecount = D('Member')->where('sid='.$sid)->sum('online');
	//if($onlinecount == ''){$onlinecount = 0;}
	//return $onlinecount;
	$virtualcount = D('Member')->where('isvirtual="y"')->count();
	$onlinecount = 0;
	$onlineemcee = D('Member')->where('sid='.$sid)->select();
	foreach($onlineemcee as $val){
		if($val['broadcasting'] == "y"){
			if($val['virtualguest'] > 0){
				$onlinecount = $onlinecount + $val['online'] + $val['virtualguest'] + $virtualcount;
			}
			else{
				$onlinecount = $onlinecount + $val['online'];
			}
		}
		else{
			$onlinecount = $onlinecount + $val['online'];
		}
	}
	if($onlinecount < 0){
		$onlinecount = 0;
	}
	return $onlinecount;
}

function getEmceelevel($earnbean) {
	$level = D("Emceelevel")->where("earnbean_up>=".$earnbean." and earnbean_low<=".$earnbean)->field('levelid,levelname')->order('levelid asc')->select();
	return $level;
}

function getRichlevel($spendcoin) {
	$level = D("Richlevel")->where("spendcoin_up>=".$spendcoin." and spendcoin_low<=".$spendcoin)->field('levelid,levelname')->order('levelid asc')->select();
	return $level;
}

function checkIt($number) {     
	$modes = array(
			'######', 'AAAAAA', 'AAABBB', 'AABBCC', 'ABCABC', 'ABBABB', 'AABAA', 'AAABB', 'AABBB', '#####', 'AAAAA', '####', 'AAAA', 'AABB', 'ABBA', 'AAAB', 'ABAB', 'AAA', '###', 'AAAAAAAB', 'AAAAAABC', 'AAAAABCD', 'AAABBBCD', 'AAABBBC', 'AABBBCDE', 'AABBBCD', 'AABBBC', 'AAABBCDE', 'AAABBCD', 'AAABBC', 'AAAABCDE', 'AAAABCD', 'AAAABC', 'AAAAB', 'AABBCDEF', 'AABBCDE', 'AABBCD', 'AABBC', 'AAABCDEF', 'AAABCDE', 'AAABCD', 'AAABC', 'AAAB', 'AABBCCDE', 'AABBCCD'); //前后排序有优先级,只要有一个匹配,后面的就不再检索了
	$result = ' ';     
	foreach ($modes as $mode) {         
		$len = strlen($mode);         
		$s = substr($number, -$len);         
		$temp = array();         
		$match = true;         
		for ($i=0; $i<$len; $i++) {             
			if ($mode[$i]=='#') {                 
				if (!isset($temp['step'])) {                     
					$temp['step'] = 0;                     
					$temp['current'] = intval($s[$i]);                 
				} 
				elseif ($temp['step'] == 0) {                     
					$temp['step'] = $temp['current'] - intval($s[$i]);                     
					if ($temp['step'] != -1 && $temp['step'] != 1) {                         
						$match = false;                         
						break;                    
					} 
					else {                         
						$temp['current'] = intval($s[$i]);                     
					}                 
				} 
				else {                     
					$step = $temp['current'] - intval($s[$i]);                     
					if ($step != $temp['step']) {                        
						$match = false;                         
						break;                     
					} 
					else {                        
						$temp['current'] = intval($s[$i]);                     
					}                 
				}             
			} 
			else {                 
				if (isset($temp[$mode[$i]])) {                     
					if ($s[$i] != $temp[$mode[$i]]) {                         
						$match = false;                         
						break;                     
					}                 
				} 
				else {                     
					$temp[$mode[$i]] = $s[$i];                 
				}             
			}         
		}         
		if ($match) {             
			$result = $mode;             
			break;        
		}     
	}     
	return $result; 
}

?>