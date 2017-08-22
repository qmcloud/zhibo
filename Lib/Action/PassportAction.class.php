<?php
class PassportAction extends BaseAction {
    public function usercenter(){
		C('HTML_CACHE_ON',false);
        $this->display();
    }

	public function dologin() {
		C('HTML_CACHE_ON',false);
		if($_SESSION['verify'] != md5($_REQUEST['validateCode'])) {
			echo '{"code":"001001"}';
			exit;
		}

		include './config.inc.php';
		include './uc_client/client.php';

		list($uid, $username, $password, $email) = uc_user_login($_REQUEST["userName"], $_REQUEST["password"]);
		if($uid > 0) {
			$userinfo = D("Member")->where('username="'.$_REQUEST["userName"].'"')->select();
			if(!$userinfo) {
				$User=D("Member");
				$User->create();
				$User->username = $_REQUEST["userName"];
				$User->nickname = $_REQUEST["userName"];
				$User->password = md5($_REQUEST["password"]);
				$User->password2 = $this->pswencode($_REQUEST["password"]);
				$User->email = $email;
				$User->isaudit = $this->regaudit;
				$User->regtime = time();
				$roomnum = 99999;    
				do {    
					$roomnum = rand(1000000000,9999999999);   
				} while (checkIt($roomnum)=='');
				$User->curroomnum = $roomnum;
				$User->ucuid = $uid;
				$User->host = $this->defaultserver;
				$User->canlive = $this->canlive;
				$userId = $User->add();

				D("Roomnum")->execute('insert into ss_roomnum(uid,num,addtime) values('.$userId.','.$roomnum.','.time().')');

				if($this->regaudit =='n'){
					echo '{"code":"001009"}';
					exit;
				}
				else{
					//写入本次登录时间及IP
					D("Member")->where('id='.$userId)->setField('lastlogtime',time());
					D("Member")->where('id='.$userId)->setField('lastlogip',get_client_ip());
					session('uid',$userId);
					session('ucuid',$uid);
					session('username',$_REQUEST["userName"]);
					session('nickname',$_REQUEST["userName"]);
					session('roomnum',$roomnum);
					cookie('userid',$userId,3600000);
					cookie('ucuid',$uid,3600000);
					cookie('username',$_REQUEST["userName"],3600000);
					cookie('nickname',$_REQUEST["userName"],3600000);
					cookie('roomnum',$roomnum,3600000);
					cookie('autoLogin','0',3600000);
					$ucsynlogin = uc_user_synlogin($uid);
					
					echo '{"code":"000000","user":[{"userName":"'.$_REQUEST["userName"].'","userId":"'.$userId.'","nick":"'.$_REQUEST["userName"].'"}]}';
					exit;
				}
			}
			else{
				if($userinfo[0]['isaudit'] =='n' || $userinfo[0]['isdelete'] =='y'){
					echo '{"code":"001009"}';
					exit;
				}
				else{
					//写入本次登录时间及IP
					D("Member")->where('id='.$userinfo[0]['id'])->setField('lastlogtime',time());
					D("Member")->where('id='.$userinfo[0]['id'])->setField('lastlogip',get_client_ip());
					session('uid',$userinfo[0]['id']);
					session('ucuid',$userinfo[0]['ucuid']);
					session('username',$_REQUEST["userName"]);
					session('nickname',$userinfo[0]['nickname']);
					session('roomnum',$userinfo[0]['curroomnum']);
					cookie('userid',$userinfo[0]['id'],3600000);
					cookie('ucuid',$userinfo[0]['ucuid'],3600000);
					cookie('username',$_REQUEST["userName"],3600000);
					cookie('nickname',$userinfo[0]['nickname'],3600000);
					cookie('roomnum',$userinfo[0]['curroomnum'],3600000);
					cookie('autoLogin',$_REQUEST['autoLogin'],3600000);
					$ucsynlogin = uc_user_synlogin($uid);
					
					echo '{"code":"000000","user":[{"userName":"'.$_REQUEST["userName"].'","userId":"'.$userinfo[0]['id'].'","nick":"'.$_REQUEST["userName"].'"}]}';
					exit;
				}
			}
		} elseif($uid == -1) {
			echo '{"code":"001004"}';
			exit;
		} elseif($uid == -2) {
			echo '{"code":"001004"}';
			exit;
		} else {
			echo '{"code":"001004"}';
			exit;
		}
	}

	public function doreg(){
		C('HTML_CACHE_ON',false);
		if($this->openreg != 'y') {
			echo '{"code":"001500","info":"当前禁止注册新用户!"}';
			exit;
		}

		if($_SESSION['verify'] != md5($_GET['validateCode'])) {
			echo '{"code":"001001"}';
			exit;
		}

		include './config.inc.php';
		include './uc_client/client.php';
	$user = D("Member")->where('username ="'.$_REQUEST['userName'].'"')->select();
		if($user){
			echo '{"code":"001190","info":"用户名重复"}';
				exit;
		}
		$uid = uc_user_register($_GET['userName'], $_GET['password'], $_GET['email']);
		if($uid <= 0) {
			if($uid == -1) {
				echo '{"code":"001190","info":"用户名不合法"}';
				exit;
			} elseif($uid == -2) {
				echo '{"code":"001190","info":"包含不允许注册的词语"}';
				exit;
			} elseif($uid == -3) {
				echo '{"code":"001002"}';
				exit;
			} elseif($uid == -4) {
				echo '{"code":"001190","info":"Email 格式有误'.$_GET['email'].'"}';
				exit;
			} elseif($uid == -5) {
				echo '{"code":"001190","info":"Email 不允许注册"}';
				exit;
			} elseif($uid == -6) {
				echo '{"code":"001190","info":"该 Email 已经被注册"}';
				exit;
			} else {
				echo '{"code":"001190","info":"未知错误"}';
				exit;
			}
		}
		else {
			$User=D("Member");
			$User->create();
			$User->username = $_GET['userName'];
			$User->nickname = $_GET['userName'];
			$User->password = md5($_GET['password']);
			$User->password2 = $this->pswencode($_GET['password']);
			$User->email = $_GET['email'];
			$User->isaudit = $this->regaudit;
			$User->regtime = time();
			$roomnum = 99999;			//获取推荐人						$url = $_SERVER['HTTP_HOST'];			$RecommendID = M("member")->where("RecommendID = 'baidu12.com'")->getField("id");			$User->RecommendID = $RecommendID;
			do {    
				$roomnum = rand(1000000000,1999999999);   
			} while (checkIt($roomnum)=='');
			$User->curroomnum = $roomnum;
			$User->ucuid = $uid;
			$User->host = $this->defaultserver;
			$User->canlive = $this->canlive;
			$userId = $User->add();

			D("Roomnum")->execute('insert into ss_roomnum(uid,num,addtime) values('.$userId.','.$roomnum.','.time().')');

			if($this->regaudit == 'y'){
				//写入本次登录时间及IP
				D("Member")->where('id='.$userId)->setField('lastlogtime',time());
				D("Member")->where('id='.$userId)->setField('lastlogip',get_client_ip());
				session('uid',$userId);
				session('ucuid',$uid);
				session('username',$_GET['userName']);
				session('nickname',$_GET['userName']);
				session('roomnum',$roomnum);
				cookie('userid',$userId,3600000);
				cookie('ucuid',$uid,3600000);
				cookie('username',$_REQUEST["userName"],3600000);
				cookie('nickname',$_REQUEST["userName"],3600000);
				cookie('roomnum',$roomnum,3600000);
				cookie('autoLogin','0',3600000);

				echo '{"code":"000000","userName":"'.$_GET['userName'].'","userId":"'.$userId.'","nick":"'.$_GET['userName'].'"}';
				exit;
			}
			else{
				echo '{"code":"001190","info":"注册成功，等待管理员审核"}';
				exit;
			}
		}

	}

	public function findBackPwdPage(){
		C('HTML_CACHE_ON',false);
		$this->display();
	}

	public function findBackPwd(){
		C('HTML_CACHE_ON',false);
		$userinfo = D("Member")->where('username="'.$_REQUEST["userName"].'"')->select();
		if($userinfo){
			if($userinfo[0]['email'] != $_REQUEST["email"]){
				echo '{"code":"000002","info":"邮箱不匹配"}';
				exit;
			}
			include './config.inc.php';
			include './uc_client/client.php';

			$userpassword = $this->pswdecode($userinfo[0]['password2']);
			//发邮件
			$mailconfig=D('Mailconfig')->find(1);
			if(!$mailconfig){
				echo '{"code":"000003","info":"邮件发送失败"}';
				exit;
			}
			$subject = $this->sitename."会员找回密码";
			$message = $mailconfig['fpasswd_mailtpl'];
			$message = ereg_replace('\{\$siteurl\}', $this->siteurl, $message);
			$message = ereg_replace('\{\$sitelogo\}', $this->sitelogo, $message);
			$message = ereg_replace('\{\$useremail\}', $userinfo[0]['email'], $message);
			$message = ereg_replace('\{\$username\}', $userinfo[0]['username'], $message);
			$message = ereg_replace('\{\$userpassword\}', $userpassword, $message);
			$message = ereg_replace('\{\$adminemail\}', $this->adminemail, $message);

			$res = uc_mail_queue($uid, $userinfo[0]['email'], $subject, $message);
			if(empty($res)) {
				echo '{"code":"000003","info":"邮件发送失败"}';
				exit;
			}
			else{
				echo '{"code":"000000"}';
				exit;
			}
		}
		else{
			echo '{"code":"000001","info":"找不到该用户名"}';
			exit;
		}
	}

	public function findBackPwdSuccess(){
		C('HTML_CACHE_ON',false);
		$this->display();
	}

	public function logout(){
		C('HTML_CACHE_ON',false);
		session('uid',null);
		session('ucuid',null);
		session('username',null);
		session('nickname',null);
		session('roomnum',null);
		cookie('userid',null,3600000);
		cookie('ucuid',null,3600000);
		cookie('username',null,3600000);
		cookie('nickname',null,3600000);
		cookie('autoLogin',null,3600000);
		cookie('roomnum',null,3600000);
		if($_REQUEST['type'] == 'redirect'){
			redirect('/index.php');
		}
		else{
			echo "data='';";
			exit;
		}
	}

	public function checkusername() {
		C('HTML_CACHE_ON',false);
		$user = D("Member")->where('username ="'.$_REQUEST['userName'].'"')->select();
		if($user){
			echo '1';
			exit;
		}else{
			foreach ($this->denyusername as $k){
				if(strstr($_REQUEST['userName'],$k) != ''){
					echo '1';
					exit;
				}
			}
			echo '0';
			exit;
		}
	}

	public function checkemail() {
		C('HTML_CACHE_ON',false);
		$user = D("Member")->where('email ="'.trim(str_replace('%40', '@', $_REQUEST['email'])).'"')->select();
		if($user){
			echo '1';
			exit;
		}else{
			echo '0';
			exit;
		}
	}
}