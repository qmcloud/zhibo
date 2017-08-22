<?php
class catchAction extends BaseAction {
function unescape($str) 
{ 
    $ret = ''; 
    $len = strlen($str); 

    for ($i = 0; $i < $len; $i++) 
    { 
        if ($str[$i] == '%' && $str[$i+1] == 'u') 
        { 
            $val = hexdec(substr($str, $i+2, 4)); 

            if ($val < 0x7f) $ret .= chr($val); 
            else if($val < 0x800) $ret .= chr(0xc0|($val>>6)).chr(0x80|($val&0x3f)); 
            else $ret .= chr(0xe0|($val>>12)).chr(0x80|(($val>>6)&0x3f)).chr(0x80|($val&0x3f)); 

            $i += 5; 
        } 
        else if ($str[$i] == '%') 
        { 
            $ret .= urldecode(substr($str, $i, 3)); 
            $i += 2; 
        } 
        else $ret .= $str[$i]; 
    } 
    return $ret; 
}


	public function index(){
		C('HTML_CACHE_ON',false);

		ignore_user_abort(true);
		header('Content-Type: text/html;charset=utf-8');
		echo str_pad("",1000);
		echo '准备开始采集...<br>';
		flush();

		$listurl = 'http://xiu.56.com/api/liveListv5.php?gType=3&type=&page=*&province=&rows=100&order=1&t=0.17034454020407663';
		$startpage = 1;
		$endpage = 1;

		$coverfolder = 'Public/snap/'.date('Y-m');
		$this->mkpath($coverfolder);

		$User=D("Member");

		include './config.inc.php';
		include './uc_client/client.php';

		for ($i=$startpage;$i<=$endpage;$i++) {
			$pageurl = str_replace('*',$i,$listurl);
			echo '正在采集页面'.$pageurl.'<br>';

			$body = file_get_contents($pageurl);
			preg_match_all("|\"user_id\":\"(.*)\"|isU", $body, $userid);
			preg_match_all("|\"room_img\":\"(.*)\"|isU", $body, $usercover);
			preg_match_all("|\"nickname\":\"(.*)\"|isU", $body, $usernick);
			
			//foreach ($usernick[1] as $k){
				//echo $this->unescape(str_replace('\\','%',$k)).'<br>';
			//}
			//exit;
			
			$p = 0;
			foreach ($userid[1] as $k){
				//if ( connection_aborted() )
				//{
					//exit;
				//}
				$addusername = $k;
				$snappath = str_replace('\/','/',$usercover[1][$p]);
				//cho $addusername.'|'.$snappath.'<br>';

				$data = $User->where("username='".$addusername."' or 56_room_user_id='".$addusername."'")->select();
				if(!$data){
					echo $addusername.'|'.$snappath.'|'.$this->unescape(str_replace('\\','%',$usernick[1][$p])).'<br>';
					//获取并下载截图
					$savename = time().'_'.rand(100,999);
					$this->savefile($snappath,$savename,$coverfolder);
					$addusersnap = '/'.$coverfolder.'/'.$savename.'.jpg';

					$uid = uc_user_register($addusername, '12345678', $addusername.'@126.com');
					if($uid > 0) {
						echo $uid.'<br>';
						$User->create();
						$User->sid = 4;
						$User->username = $addusername;
						$User->nickname = $this->unescape(str_replace('\\','%',$usernick[1][$p]));
						$User->password = md5('12345678');
						$User->password2 = $this->pswencode('12345678');
						$User->email = $addusername.'@126.com';
						$User->isaudit = 'y';
						$User->regtime = time();
						$roomnum = 99999;    
						do {    
							$roomnum = rand(1000000000,1999999999);   
						} while (checkIt($roomnum)=='');
						$User->curroomnum = $roomnum;
						$User->ucuid = $uid;
						$User->host = $this->defaultserver;
						$User->snap = $addusersnap;
						$User->fakeuser = 'y';
						//$User->56_room_user_id = $addusername;
						$userId = $User->add();

						if($userId > 0){
							$User->execute('update ss_member set 56_room_user_id="'.$addusername.'" where id='.$userId);
							echo '_____<br>';
						}
					}
				}

				$p++;
			}
		}
	}

	public function mkpath($mkpath,$mode=0777){
		$path_arr = explode('/',$mkpath);
		foreach($path_arr as $value){
			if(!empty($value)){
				if(empty($path))$path=$value;
				else $path.='/'.$value;
				is_dir($path) or mkdir($path,$mode);
			}
		}
		if(is_dir($mkpath))return true;
		return false;
	}

	public function savefile($url,$savename,$folder){
		$getfile = file_get_contents($url);
		$filename = $savename.'.jpg';//保存文件名+后缀名
		$file = fopen("$folder/$filename",'w+');//建立文件
		fwrite($file,$getfile);//写入文件
		fclose($file);
	}
}
?>