<?php
class BaseAction extends Action 
{
	var $sitelogo;
	var $sitename;
	var $siteurl;
	var $openreg;
	var $regaudit;
	var $denyusername;
	
	public function _initialize()
	{
		
		$siteconfig=D('Siteconfig');
		$site=$siteconfig->find();
		if(!$site) {
			$this->assign('closeWin', true);
			$this->error('站点初始化失败');
		}if(isset($_GET['ajax'])&&$_GET['ajax']=='getinfo'){			$this->ajaxReturn($site);			exit;		} 

		$this->assign('sitelogo', $site['sitelogo']);
		$this->assign('sitename', $site['sitename']);
		$this->assign('sitetitle', $site['sitetitle']);
		$this->assign('siteurl', $site['siteurl']);
		$this->assign('domainroot', $site['domainroot']);
		$this->assign('footinfo', $site['footinfo']);
		$this->assign('titleattach', $site['titleattach']);
		$this->assign('metakeyword', $site['metakeyword']);
		$this->assign('metadesp', $site['metadesp']);
		$this->assign('ucurl', $site['ucurl']);
		
		$this->sitelogo = $site['sitelogo'];
		$this->sitename = $site['sitename'];
		$this->sitetitle = $site['sitetitle'];
		$this->siteurl = $site['siteurl'];
		$this->domainroot = $site['domainroot'];
		$this->openreg	= $site['openreg'];
		$this->regaudit	= $site['regaudit'];
		$this->denyusername = explode(",", $site['denyusername']);
		$this->adminemail = $site['adminemail'];
		$this->ucurl = $site['ucurl'];

		$this->bill_MerchantAcctID = $site['99bill_MerchantAcctID'];
		$this->bill_key = $site['99bill_key'];
		$this->emceededuct = $site['emceededuct'];
		$this->emceeagentdeduct = $site['emceeagentdeduct'];
		$this->payagentdeduct = $site['payagentdeduct'];
		$this->canlive = $site['canlive'];
		$this->ratio = $site['ratio'];

		//默认直播服务器
		$defaultserver = D("Server")->where('isdefault="y"')->select();
		if($defaultserver){
			$this->defaultserver = $defaultserver[0]['server_ip'];
		}

		$this->gethbinterval = $site['gethbinterval'];
		$this->maxdaygethb = $site['maxdaygethb'];
		$this->vip_gethbinterval = $site['vip_gethbinterval'];
		$this->vip_maxdaygethb = $site['vip_maxdaygethb'];
		$this->sendhb = $site['sendhb'];
		$this->spendcoin = $site['spendcoin'];
		$this->gethb = $site['gethb'];
		$this->changecoin = $site['changecoin'];

		if($site['siteclosed']=='y' && $_SESSION['manager']==''){
			$this->assign('closeWin', true);
			$this->error($site['closereason']);
		}

		$virtualcount = D('Member')->where('isvirtual="y"')->count();
		$this->virtualcount = $virtualcount;
		$this->assign('virtualcount', $virtualcount);
	}

	public function verify() 
    {
		C('HTML_CACHE_ON',false);
        import("ORG.Util.Image");
        Image::buildImageVerify();
    }



	//加密函数
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
	//解密函数
	public function pswdecode($txt,$key='youst'){
		$chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-=+_)(*&^%$#@!~";
		$ch = $txt[0];
		$nh = strpos($chars,$ch);
		$mdKey = md5($key.$ch);
		$mdKey = substr($mdKey,$nh%8, $nh%8+7);
		$txt = substr($txt,1);
		$tmp = '';
		$i=0;$j=0; $k = 0;
		for ($i=0; $i<strlen($txt); $i++) {
			$k = $k == strlen($mdKey) ? 0 : $k;
			$j = strpos($chars,$txt[$i])-$nh - ord($mdKey[$k++]);
			while ($j<0) $j+=64;
			$tmp .= $chars[$j];
		}
		return base64_decode($tmp);
	}

	public function checkLogin()
	{
		if(Session::get("uid") != '')
		{
			Cookie::set("username",$_SESSION['username'],36000000);
			return true;
		}else {
			return false;
		}
	}

	

	// 空操作定义
	public function _empty() {
		//$this->assign('jumpUrl',__APP__);
		$this->error('此操作不存在');
	}
}
?>