<?php
class ThirdPartyAction extends BaseAction {
	public function qqlogin(){
		C('HTML_CACHE_ON',false);
		header("Content-type: text/html; charset=utf-8"); 
		if(!isset($_SESSION["openid"])||empty($_SESSION["openid"])){
			echo '<script>document.domain="'.$this->domainroot.'";alert(\'异常错误\');if(window.parent.location.href.indexOf("/ThirdParty/qqlogin/")>=0){window.location.href=\'/index.php\';}else{window.parent.location.reload();}</script>';
			exit;
		}
		else{
			//echo 'success_'.$_SESSION["openid"];
			$userinfo = D("Member")->where('qqopenid="'.$_SESSION["openid"].'"')->select();
			if($userinfo){
				include './config.inc.php';
				include './uc_client/client.php';

				D("Member")->where('id='.$userinfo[0]['id'])->setField('lastlogtime',time());
				D("Member")->where('id='.$userinfo[0]['id'])->setField('lastlogip',get_client_ip());
				session('uid',$userinfo[0]['id']);
				session('ucuid',$userinfo[0]['ucuid']);
				session('username',$userinfo[0]["username"]);
				session('nickname',$userinfo[0]["nickname"]);
				session('roomnum',$userinfo[0]["curroomnum"]);
				cookie('userid',$userinfo[0]['id'],3600000);
				cookie('roomnum',$userinfo[0]["curroomnum"],3600000);
				$ucsynlogin = uc_user_synlogin($userinfo[0]['ucuid']);

				//jump
				echo '<script>document.domain="'.$this->domainroot.'";if(window.parent.location.href.indexOf("/ThirdParty/qqlogin/")>=0){window.location.href=\'/index.php\';}else{window.parent.location.reload();}</script>';
				exit;
			}
			else{
				$roomnum = 99999;    
				do {    
					$roomnum = rand(1000000000,1999999999);   
				} while (checkIt($roomnum)=='');

				include './config.inc.php';
				include './uc_client/client.php';

				$uid = uc_user_register($roomnum, '12345678', $roomnum.'@5show.tv');
				if($uid <= 0) {
					//jump
					echo '<script>document.domain="'.$this->domainroot.'";alert(\'异常错误\');if(window.parent.location.href.indexOf("/ThirdParty/qqlogin/")>=0){window.location.href=\'/index.php\';}else{window.parent.location.reload();}</script>';
					exit;
				}
				else{
					$User=D("Member");
					$User->create();
					$User->username = $roomnum;
					$User->nickname = $roomnum;
					$User->password = md5('12345678');
					$User->password2 = $this->pswencode('12345678');
					$User->email = $roomnum.'@5show.tv';
					$User->isaudit = $this->regaudit;
					$User->regtime = time();
					$User->curroomnum = $roomnum;
					$User->ucuid = $uid;
					$User->host = $this->defaultserver;
					$User->qqopenid = $_SESSION["openid"];
					$userId = $User->add();

					D("Roomnum")->execute('insert into ss_roomnum(uid,num,addtime) values('.$userId.','.$roomnum.','.time().')');

					//写入本次登录时间及IP
					D("Member")->where('id='.$userId)->setField('lastlogtime',time());
					D("Member")->where('id='.$userId)->setField('lastlogip',get_client_ip());
					session('uid',$userId);
					session('ucuid',$uid);
					session('username',$roomnum);
					session('nickname',$roomnum);
					session('roomnum',$roomnum);
					cookie('userid',$userId,3600000);
					cookie('roomnum',$roomnum,3600000);
						
					//jump
					echo '<script>document.domain="'.$this->domainroot.'";if(window.parent.location.href.indexOf("/ThirdParty/qqlogin/")>=0){window.location.href=\'/index.php\';}else{window.parent.location.reload();}</script>';
					exit;
				}
			}
		}
	}

	public function renrenloginlink(){
		C('HTML_CACHE_ON',false);
		include './renrenlogin/class/config.inc.php';

		redirect('https://graph.renren.com/oauth/authorize?client_id='.$config->APPID.'&response_type=code&scope='.$config->scope.'&state=a%3d1%26b%3d2&redirect_uri='.$config->redirecturi.'&x_renew=true');
	}

	public function rr_accesstoken(){
		C('HTML_CACHE_ON',false);
		header("Content-type: text/html; charset=utf-8"); 

		//echo $_REQUEST['rruid'];
		//exit;

		$userinfo = D("Member")->where('rruid="'.$_REQUEST['rruid'].'"')->select();
		if($userinfo){
			include './config.inc.php';
			include './uc_client/client.php';

			D("Member")->where('id='.$userinfo[0]['id'])->setField('lastlogtime',time());
			D("Member")->where('id='.$userinfo[0]['id'])->setField('lastlogip',get_client_ip());
			session('uid',$userinfo[0]['id']);
			session('ucuid',$userinfo[0]['ucuid']);
			session('username',$userinfo[0]["username"]);
			session('nickname',$userinfo[0]["nickname"]);
			session('roomnum',$userinfo[0]["curroomnum"]);
			cookie('userid',$userinfo[0]['id'],3600000);
			cookie('roomnum',$userinfo[0]["curroomnum"],3600000);
			$ucsynlogin = uc_user_synlogin($userinfo[0]['ucuid']);

			//jump
			echo '<script>document.domain="'.$this->domainroot.'";if(window.parent.location.href.indexOf("/ThirdParty/rr_accesstoken/")>=0){window.location.href=\'/index.php\';}else{window.parent.location.reload();}</script>';
			exit;
		}
		else{
			$roomnum = 99999;    
			do {    
				$roomnum = rand(1000000000,1999999999);   
			} while (checkIt($roomnum)=='');

			include './config.inc.php';
			include './uc_client/client.php';

			$uid = uc_user_register($roomnum, '12345678', $roomnum.'@5show.tv');
			if($uid <= 0) {
				//jump
				echo '<script>document.domain="'.$this->domainroot.'";alert(\'异常错误\');if(window.parent.location.href.indexOf("/ThirdParty/rr_accesstoken/")>=0){window.location.href=\'/index.php\';}else{window.parent.location.reload();}</script>';
				exit;
			}
			else{
				$User=D("Member");
				$User->create();
				$User->username = $roomnum;
				$User->nickname = $roomnum;
				$User->password = md5('12345678');
				$User->password2 = $this->pswencode('12345678');
				$User->email = $roomnum.'@5show.tv';
				$User->isaudit = $this->regaudit;
				$User->regtime = time();
				$User->curroomnum = $roomnum;
				$User->ucuid = $uid;
				$User->host = $this->defaultserver;
				$User->rruid = $_REQUEST['rruid'];
				$userId = $User->add();

				D("Roomnum")->execute('insert into ss_roomnum(uid,num,addtime) values('.$userId.','.$roomnum.','.time().')');

				//写入本次登录时间及IP
				D("Member")->where('id='.$userId)->setField('lastlogtime',time());
				D("Member")->where('id='.$userId)->setField('lastlogip',get_client_ip());
				session('uid',$userId);
				session('ucuid',$uid);
				session('username',$roomnum);
				session('nickname',$roomnum);
				session('roomnum',$roomnum);
				cookie('userid',$userId,3600000);
				cookie('roomnum',$roomnum,3600000);
						
				//jump
				echo '<script>document.domain="'.$this->domainroot.'";if(window.parent.location.href.indexOf("/ThirdParty/rr_accesstoken/")>=0){window.location.href=\'/index.php\';}else{window.parent.location.reload();}</script>';
				exit;
			}	
		}
	}
}
?>