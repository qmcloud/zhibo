<?php
class IndexAction extends Action {
    function _initialize(){
		C('HTML_CACHE_ON',false);

		$curUrl = base64_encode($_SERVER["REQUEST_URI"]);
		if(!strpos($_SERVER["REQUEST_URI"],'login') && !strpos($_SERVER["REQUEST_URI"],'verify') && !strpos($_SERVER["REQUEST_URI"],'logout') && !$_SESSION['emceeagent'])
		{
			$this->assign('jumpUrl',__URL__."/login/return/".$curUrl);
			$this->error('请登录后操作');
		}
	}

	// 空操作定义
	public function _empty() {
		$this->assign('jumpUrl',__URL__.'/mainFrame');
		$this->error('此操作不存在');
	}

	public function verify() 
    {
        import("ORG.Util.Image");
        Image::buildImageVerify(4,1,'png',130,50);
    }

	public function pswencode($txt,$key='youst'){
		$chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-=+_)(*&^%$#@!~";
		$nh = rand(0,64);
		$ch = $chars[$nh];
		$mdKey = md5($key.$ch);
		$mdKey = substr($mdKey,$nh%8, $nh%8+7);
		$txt = base64_encode($txt);
		$tmp = '';
		$i=0;$j=0;$k = 0;
		for ($i=0; $i<strlen($txt); $i++) {
			$k = $k == strlen($mdKey) ? 0 : $k;
			$j = ($nh+strpos($chars,$txt[$i])+ord($mdKey[$k++]))%64;
			$tmp .= $chars[$j];
		}
		return $ch.$tmp;
	}

	public function login()
    {
		if($_GET['return']!=''){
			$this->assign('returnurl', $_GET['return']);
		}
        $this->display();
    }

	public function dologin()
    {
        if(md5($_POST['code']) != $_SESSION['verify']){
			$this->error('验证码错误,请检查!');
		}

		include '../config.inc.php';
		include '../uc_client/client.php';

		list($uid, $username, $password, $email) = uc_user_login($_POST["username"], $_POST["password"]);
		if($uid > 0) {
			$userinfo = D("Member")->where('username="'.$_POST["username"].'"')->select();
			if(!$userinfo) {
				
			}
			else{
				if($userinfo[0]['emceeagent'] =='n'){
					$this->error('您不是主播代理身份');
				}
				if($userinfo[0]['isaudit'] =='n' || $userinfo[0]['isdelete'] =='y'){
					$this->error('您的账户已被禁用或被删除');
				}
				else{
					//写入本次登录时间及IP
					D("Member")->where('id='.$userinfo[0]['id'])->setField('lastlogtime',time());
					D("Member")->where('id='.$userinfo[0]['id'])->setField('lastlogip',get_client_ip());
					//写入SESSION
					session('agentid',$userinfo[0]['id']);
					session('username',$_POST["username"]);
					session('emceeagent','y');
					
					if($_POST['next_action']!=''){
						$this->assign('jumpUrl',base64_decode($_POST['next_action']));
					}
					else{
						$this->assign('jumpUrl',__URL__);
					}
					$this->success('登录成功');
				}
			}
		}
		else{
			$this->error('用户名或密码错误,请重新登录');
		}

		
    }

	function logout()
	{
		session('agentid',null);
		session('username',null);
		session('emceeagent',null);
		$this->assign('jumpUrl',__URL__.'/login/');
		$this->success('退出成功');
	}

	public function index()
    {
		
        $this->display();
    }

	public function leftFrame()
	{
		
		$this->display();
	}

	public function mainFrame()
	{
		$userinfo = D("Member")->find($_SESSION["agentid"]);
		$this->assign('userinfo',$userinfo);
		
		$this->display();
	}


	public function edit_pwd()
	{
		if($_GET['action'] == 'public_password_ajx'){
			$password = md5($_GET["old_password"]);
			$userinfo = D("Member")->where("username='".$_SESSION["username"]."' and password='".$password."'")->select();
			if($userinfo){
				echo '1';
			}
			else{
				echo '0';
			}
			exit;
		}

		$this->display();
	}

	public function do_edit_pwd()
	{
		if($_POST['new_password'] == ''){
			$this->assign('jumpUrl',__URL__."/edit_pwd/");
			$this->success('修改成功');
			exit;
		}

		$User = D('Member');
		$vo = $User->create();
		if(!$vo) {
			$this->error($User->getError());
		}else{
			if($_POST['new_password'] != ''){
				if($_POST['old_password'] == ''){
					$this->error('原始密码不能为空');
				}
				if($_POST['new_password'] != $_POST['new_pwdconfirm']){
					$this->error('两次新密码不一致');
				}
include '../config.inc.php';
include '../uc_client/client.php';
$ucresult = uc_user_edit($_SESSION['username'], $_POST['old_password'], $_POST['new_password']);
if($ucresult == -1) {
	$this->error('旧密码不正确');
} elseif($ucresult == -4) {
	$this->error('Email 格式有误');
} elseif($ucresult == -5) {
	$this->error('不允许注册');
} elseif($ucresult == -6) {
	$this->error('该 Email 已经被注册');
}

			}
			$User->password = md5($_POST['new_password']);
			$User->password2 = $this->pswencode($_POST['new_password']);
			$User->save();

			$this->assign('jumpUrl',__URL__."/User/edit_pwd/");
			$this->success('修改成功');
		}
	}

	public function view_myemcee()
	{
		$condition = 'agentuid='.$_SESSION['agentid'];
		if($_GET['start_time'] != ''){
			$timeArr = explode("-", $_GET['start_time']);
			$unixtime = mktime(0,0,0,$timeArr[1],$timeArr[2],$timeArr[0]);
			$condition .= ' and addtime>='.$unixtime;
		}
		if($_GET['end_time'] != ''){
			$timeArr = explode("-", $_GET['end_time']);
			$unixtime = mktime(0,0,0,$timeArr[1],$timeArr[2],$timeArr[0]);
			$condition .= ' and addtime<='.$unixtime;
		}
		if($_GET['keyword'] != '' && $_GET['keyword'] != '请输入用户ID或用户名'){
			if(preg_match("/^\d*$/",$_GET['keyword'])){
				$condition .= ' and (id='.$_GET['keyword'].' or username like \'%'.$_GET['keyword'].'%\')';
			}
			else{
				$condition .= ' and username like \'%'.$_GET['keyword'].'%\'';
			}
		}
		
		$orderby = 'id desc';
		$member = D("Member");
		$count = $member->where($condition)->count();
		$listRows = 20;
		$linkFront = '';
		import("@.ORG.Page");
		$p = new Page($count,$listRows,$linkFront);
		$members = $member->limit($p->firstRow.",".$p->listRows)->where($condition)->order($orderby)->select();
		$p->setConfig('header','条');
		$page = $p->show();
		$this->assign('page',$page);
		$this->assign('members',$members);

		$this->display();
	}

	public function view_beandetail(){
		$condition = 'uid='.$_SESSION['agentid'];
		if($_GET['start_time'] != ''){
			$timeArr = explode("-", $_GET['start_time']);
			$unixtime = mktime(0,0,0,$timeArr[1],$timeArr[2],$timeArr[0]);
			$condition .= 'addtime>='.$unixtime;
		}
		if($_GET['end_time'] != ''){
			$timeArr = explode("-", $_GET['end_time']);
			$unixtime = mktime(0,0,0,$timeArr[1],$timeArr[2],$timeArr[0]);
			$condition .= ' and addtime<='.$unixtime;
		}
		
		$orderby = 'id desc';
		$beandetail = D("Emceeagentbeandetail");
		$count = $beandetail->where($condition)->count();
		$listRows = 100;
		$linkFront = '';
		import("@.ORG.Page");
		$p = new Page($count,$listRows,$linkFront);
		$details = $beandetail->limit($p->firstRow.",".$p->listRows)->where($condition)->order($orderby)->select();
		foreach($details as $n=> $val){
			$details[$n]['voo']=D("Member")->where('id='.$val['uid'])->select();
		}
		$p->setConfig('header','条');
		$page = $p->show();
		$this->assign('page',$page);
		$this->assign('details',$details);

		$this->display();
	}
}