<?php
class VoteAction extends Action {
	function _initialize(){
		C('HTML_CACHE_ON',false);

		$curUrl = base64_encode($_SERVER["REQUEST_URI"]);
		if($_SESSION['lock_screen'] == 1 && !strpos($_SERVER["REQUEST_URI"],'login')){
			session('manager',null);
			session('lock_screen',0);
			session('trytimes',0);
			
			$this->assign('jumpUrl',__APP__."/Index/login/return/".$curUrl);
			$this->error('请登录后操作');
		}

		if(!strpos($_SERVER["REQUEST_URI"],'login') && !strpos($_SERVER["REQUEST_URI"],'verify') && !strpos($_SERVER["REQUEST_URI"],'logout') && !$_SESSION['manager'])
		{
			$this->assign('jumpUrl',__APP__."/Index/login/return/".$curUrl);
			$this->error('请登录后操作');
		}
	}

	// 空操作定义
	public function _empty() {
		$this->assign('jumpUrl',__APP__.'/Index/mainFrame');
		$this->error('此操作不存在');
	}

	public function admin_votechannel()
	{
		$channels = D("Votechannel")->where("parentid=0")->order('orderid')->select();
		foreach($channels as $n=> $val){
			$channels[$n]['voo']=D("Votechannel")->where('parentid='.$val['id'])->order('orderid')->select();
		}
		$this->assign("channels",$channels);
		$this->display();
	}

	public function votechannellistorder()
	{
		$Edit_ID = $_POST['id'];
		$Edit_OrderID = $_POST['orderid'];
		
		$num = count($Edit_ID);
		for($i=0;$i<$num;$i++)
		{
			D("Votechannel")->execute('update ss_votechannel set orderid='.$Edit_OrderID[$i].' where id='.$Edit_ID[$i]);
		}

		$this->assign('jumpUrl',__URL__."/admin_votechannel/");
		$this->success('修改成功');
	}

	public function del_channel()
	{
		D("Votechannel")->where('id='.$_GET['cid'].' or parentid='.$_GET['cid'])->delete();
		if($_GET['type'] == 'sub'){
			D("Voteuser")->execute('update ss_voteuser set cid=0 where cid='.$_GET['cid']);
		}
		else{
			D("Voteuser")->execute('update ss_voteuser set pcid=0,cid=0 where pcid='.$_GET['cid']);
		}

		$this->assign('jumpUrl',__URL__."/admin_votechannel/");
		$this->success('删除成功');
	}

	public function add_votechannel()
	{
		$channels = D("Votechannel")->where("parentid=0")->order('orderid')->select();
		
		$this->assign("channels",$channels);
		$this->display();
	}

	public function do_add_votechannel()
	{
		if($_POST['channelstr'] != ''){
			$Channel = D('Votechannel');
			$Channel->create();
			$Channel->parentid = $_POST['parentid'];
			$Channel->channelstr = $_POST['channelstr'];
			$channelID = $Channel->add();
		}
		
		if($channelID){
			$this->assign('jumpUrl',__URL__."/admin_votechannel/");
			$this->success('添加成功');
		}
		else{
			$this->error('添加失败');
		}
	}

	public function edit_votechannel()
	{
		if($_GET["cid"] == '')
		{
			$this->error('缺少参数或参数不正确');
		}
		else{
			$dao = D("Votechannel");
			$channelinfo = $dao->getById($_GET["cid"]);
			if($channelinfo){
				$channels = D("Votechannel")->where("parentid=0")->order('orderid')->select();
				$this->assign("channels",$channels);

				$this->assign('channelinfo',$channelinfo);
			}
			else{
				$this->error('找不到该类别');
			}
		}

		$this->display();
	}

	public function do_edit_votechannel()
	{
		if($_POST["id"] == '')
		{
			$this->error('缺少参数或参数不正确');
		}
		else{
			$dao = D("Votechannel");
			$channelinfo = $dao->getById($_POST["id"]);
			if($channelinfo){
				$vo = $dao->create();
				if(!$vo) {
					$this->error($dao->getError());
				}else{
					$dao->save();

					$this->assign('jumpUrl',__URL__."/edit_votechannel/cid/".$_POST["id"]);
					$this->success('修改成功');
				}
			}
			else{
				$this->error('找不到该类别');
			}
		}
	}

	public function admin_voteuser()
	{
		$condition = 'id>0';
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
		
		$condition .= ' and truename like \'%'.$_GET['keyword'].'%\'';
		$orderby = 'addtime desc';
		$voteuser = D("Voteuser");
		$count = $voteuser->where($condition)->count();
		$listRows = 20;
		$linkFront = '';
		import("@.ORG.Page");
		$p = new Page($count,$listRows,$linkFront);
		$voteusers = $voteuser->limit($p->firstRow.",".$p->listRows)->where($condition)->order($orderby)->select();
		$p->setConfig('header','条');
		$page = $p->show();
		$this->assign('page',$page);
		$this->assign('voteusers',$voteusers);

		$this->display();
	}

	public function add_voteuser(){
		$channels = D("Votechannel")->where("parentid=0")->order('addtime')->select();
		foreach($channels as $n=> $val){
			$channels[$n]['voo']=D("Votechannel")->where('parentid='.$val['id'])->order('addtime')->select();
		}

		$this->assign("channels",$channels);

		
		$this->display();
	}

	public function do_add_voteuser(){
		//上传缩略图
		import("@.ORG.UploadFile");
		$upload = new UploadFile();
		//设置上传文件大小
		$upload->maxSize  = 1048576 ;
		//设置上传文件类型
		$upload->allowExts  = explode(',','jpg,png');
		//设置上传目录
		//每个用户一个文件夹
		$prefix = date('Y/m/d');
		$uploadPath =  '../Public/vote/'.$prefix.'/';
		if(!is_dir($uploadPath)){
        	mkdir($uploadPath);
		}
		$upload->savePath =  $uploadPath;
		$upload->saveRule = uniqid;
		//执行上传操作
		if(!$upload->upload()) {
			// 捕获上传异常
			$this->error($upload->getErrorMsg());
		}
		else{
			$uploadList = $upload->getUploadFileInfo();
			$avatorpath = '/Public/vote/'.$prefix.'/'.$uploadList[0]['savename'];
		}

		$photoArr = explode("|", $_POST['filestr']);

		$Voteuser=D("Voteuser");
		$vo = $Voteuser->create();
		if(!$vo) {
			$this->error($Voteuser->getError());
		}else{
			$cidArr = explode(",", $_POST['cid']);
			if($cidArr[0] != ''){
				$Voteuser->pcid = $cidArr[0];
			}
			if($cidArr[1] != ''){
				$Voteuser->cid = $cidArr[1];
			}
			$Voteuser->avator = $avatorpath;
			$Voteuser->covepath = $photoArr[0];
			$userId = $Voteuser->add();

			D("Votechannel")->execute('update ss_votechannel set haveuser=haveuser+1 where id='.$cidArr[0].' or id='.$cidArr[1]);
		}

		$Voteuserpic = D("Voteuserpic");
		foreach ($photoArr as $k){
			if($k != ''){
				$file_ext = substr(strrchr($k, '.'), 1);
				$thumb_file_name = basename($k, '.'.$file_ext);
				$thumburl = dirname($k).'/'.$thumb_file_name.'_thumb.'.$file_ext;
				D("Voteuserpic")->execute('insert into ss_voteuserpic(voteuid,photourl,thumburl,addtime) values('.$userId.',"'.$k.'","'.$thumburl.'",'.time().')');
			}
		}

		$this->assign('jumpUrl',__URL__."/admin_voteuser/");
		$this->success('添加成功');
	}

	public function edit_voteuser(){
		if($_GET['userid'] == ''){
			$this->error('参数错误');
		}
		else{
			$voteuserinfo = D("Voteuser")->getById($_GET["userid"]);
			if($voteuserinfo){
				$this->assign('voteuserinfo',$voteuserinfo);
				$voteuserpics = D('Voteuserpic')->where('voteuid='.$_GET["userid"])->order('addtime desc')->select();
				$this->assign('voteuserpics',$voteuserpics);
				$channels = D("Votechannel")->where("parentid=0")->order('addtime')->select();
				foreach($channels as $n=> $val){
					$channels[$n]['voo']=D("Votechannel")->where('parentid='.$val['id'])->order('addtime')->select();
				}
				$this->assign("channels",$channels);
			}
			else{
				$this->error('找不到该选手');
			}
		}
		
		$this->display();
	}

	public function do_edit_voteuser(){
		if($_POST["id"] == '')
		{
			$this->error('缺少参数或参数不正确');
		}
		else{
			$voteuserinfo = D("Voteuser")->getById($_POST["id"]);
			if($voteuserinfo){
				$oldpcid = $voteuserinfo['pcid'];
				$oldcid = $voteuserinfo['cid'];
			}
			else{
				$this->error('该选手不存在');
			}
		}

		//上传缩略图
		import("@.ORG.UploadFile");
		$upload = new UploadFile();
		//设置上传文件大小
		$upload->maxSize  = 1048576 ;
		//设置上传文件类型
		$upload->allowExts  = explode(',','jpg,png');
		//设置上传目录
		//每个用户一个文件夹
		$prefix = date('Y/m/d');
		$uploadPath =  '../Public/vote/'.$prefix.'/';
		if(!is_dir($uploadPath)){
        	mkdir($uploadPath);
		}
		$upload->savePath =  $uploadPath;
		$upload->saveRule = uniqid;
		//执行上传操作
		if(!$upload->upload()) {
			// 捕获上传异常
			if($upload->getErrorMsg() != '没有选择上传文件'){
				$this->error($upload->getErrorMsg());
			}
		}
		else{
			$uploadList = $upload->getUploadFileInfo();
			$avatorpath = '/Public/vote/'.$prefix.'/'.$uploadList[0]['savename'];
		}

		$photoArr = explode("|", $_POST['filestr']);

		$Voteuser=D("Voteuser");
		$vo = $Voteuser->create();
		if(!$vo) {
			$this->error($Voteuser->getError());
		}else{
			$cidArr = explode(",", $_POST['cid']);
			if($cidArr[0] != ''){
				$Voteuser->pcid = $cidArr[0];
			}
			if($cidArr[1] != ''){
				$Voteuser->cid = $cidArr[1];
			}
			if($avatorpath != ''){
				$Voteuser->avator = $avatorpath;
			}
			if($_POST['phase1'] == 'y'){
				$Voteuser->phase1 = 'y';
			}
			else{
				$Voteuser->phase1 = 'n';
			}
			if($_POST['phase2'] == 'y'){
				$Voteuser->phase2 = 'y';
			}
			else{
				$Voteuser->phase2 = 'n';
			}
			if($_POST['phase3'] == 'y'){
				$Voteuser->phase3 = 'y';
			}
			else{
				$Voteuser->phase3 = 'n';
			}
			if($_POST['phase4'] == 'y'){
				$Voteuser->phase4 = 'y';
			}
			else{
				$Voteuser->phase4 = 'n';
			}
			$Voteuser->save();
			
			D("Votechannel")->execute('update ss_votechannel set haveuser=haveuser-1 where id='.$oldpcid.' or id='.$oldcid);
			D("Votechannel")->execute('update ss_votechannel set haveuser=haveuser+1 where id='.$cidArr[0].' or id='.$cidArr[1]);
		}

		if(is_array($_REQUEST['ids'])){
			$array = $_REQUEST['ids'];
			$num = count($array);
			for($i=0;$i<$num;$i++)
			{
				D("Voteuserpic")->where('id='.$array[$i])->delete();
			}
		}
		
		$Voteuserpic = D("Voteuserpic");
		foreach ($photoArr as $k){
			if($k != ''){
				$file_ext = substr(strrchr($k, '.'), 1);
				$thumb_file_name = basename($k, '.'.$file_ext);
				$thumburl = dirname($k).'/'.$thumb_file_name.'_thumb.'.$file_ext;
				D("Voteuserpic")->execute('insert into ss_voteuserpic(voteuid,photourl,thumburl,addtime) values('.$_POST['id'].',"'.$k.'","'.$thumburl.'",'.time().')');
			}
		}

		$this->assign('jumpUrl',__URL__."/edit_voteuser/userid/".$_POST['id']);
		$this->success('修改成功');
	}

	public function del_voteuser(){
		if($_GET["userid"] == '')
		{
			$this->error('缺少参数或参数不正确');
		}
		else{
			$dao = D("Voteuser");
			$voteuserinfo = $dao->getById($_GET["userid"]);
			if($voteuserinfo){
				$dao->where('id='.$_GET["userid"])->delete();
				D("Voteuserpic")->where('voteuid='.$_GET["userid"])->delete();
				D("Voterecord")->where('voteuid='.$_GET["userid"])->delete();
				$oldpcid = $voteuserinfo['pcid'];
				$oldcid = $voteuserinfo['cid'];
				D("Votechannel")->execute('update ss_votechannel set haveuser=haveuser-1 where id='.$oldpcid.' or id='.$oldcid);
				$this->assign('jumpUrl',base64_decode($_GET['return']));
				$this->success('成功删除');
			}
			else{
				$this->error('找不到该选手');
			}
		}
	}

	public function opt_voteuser()
	{
		$dao = D("Voteuser");
		switch ($_GET['action']){
			
			case 'del':
				if(is_array($_REQUEST['ids'])){
					$array = $_REQUEST['ids'];
					$num = count($array);
					for($i=0;$i<$num;$i++)
					{
						$voteuserinfo = $dao->getById($array[$i]);
						if($voteuserinfo){
							$dao->where('id='.$array[$i])->delete();
							D("Voteuserpic")->where('voteuid='.$array[$i])->delete();
							D("Voterecord")->where('voteuid='.$array[$i])->delete();
							$oldpcid = $voteuserinfo['pcid'];
							$oldcid = $voteuserinfo['cid'];
							D("Votechannel")->execute('update ss_votechannel set haveuser=haveuser-1 where id='.$oldpcid.' or id='.$oldcid);
						}
					}
				}
				$this->assign('jumpUrl',base64_decode($_POST['return']).'#'.time());
				$this->success('操作成功');
				break;
			
		}
	}

	public function admin_votenote()
	{
		$condition = '';
		
		$orderby = 'orderno asc';
		$votenote = D("Votenote");
		$count = $votenote->where($condition)->count();
		$listRows = 20;
		$linkFront = '';
		import("@.ORG.Page");
		$p = new Page($count,$listRows,$linkFront);
		$votenotes = $votenote->limit($p->firstRow.",".$p->listRows)->where($condition)->order($orderby)->select();
		$p->setConfig('header','条');
		$page = $p->show();
		$this->assign('page',$page);
		$this->assign('votenotes',$votenotes);

		$this->display();
	}

	public function add_votenote(){
		
		$this->display();
	}

	public function do_add_votenote(){
		$Votenote=D("Votenote");
		$vo = $Votenote->create();
		if(!$vo) {
			$this->error($Votenote->getError());
		}else{
			$noteId = $Votenote->add();
		}

		$this->assign('jumpUrl',__URL__."/admin_votenote/");
		$this->success('添加成功');
	}

	public function edit_votenote(){
		if($_GET['noteid'] == ''){
			$this->error('参数错误');
		}
		else{
			$votenoteinfo = D("Votenote")->getById($_GET["noteid"]);
			if($votenoteinfo){
				$this->assign('votenoteinfo',$votenoteinfo);
			}
			else{
				$this->error('找不到该公告');
			}
		}
		
		$this->display();
	}

	public function do_edit_votenote(){
		if($_POST["id"] == '')
		{
			$this->error('缺少参数或参数不正确');
		}
		else{
			$votenoteinfo = D("Votenote")->getById($_POST["id"]);
			if(!$votenoteinfo){
				$this->error('该公告不存在');
			}
		}

		$Votenote=D("Votenote");
		$vo = $Votenote->create();
		if(!$vo) {
			$this->error($Votenote->getError());
		}else{
			$Votenote->save();
		}

		$this->assign('jumpUrl',__URL__."/edit_votenote/noteid/".$_POST['id']);
		$this->success('修改成功');
	}

	public function del_votenote(){
		if($_GET["noteid"] == '')
		{
			$this->error('缺少参数或参数不正确');
		}
		else{
			$dao = D("Votenote");
			$votenoteinfo = $dao->getById($_GET["noteid"]);
			if($votenoteinfo){
				$dao->where('id='.$_GET["noteid"])->delete();
				$this->assign('jumpUrl',base64_decode($_GET['return']));
				$this->success('成功删除');
			}
			else{
				$this->error('找不到该公告');
			}
		}
	}

	public function opt_votenote()
	{
		$Edit_ID = $_POST['id'];
		$Edit_Orderno = $_POST['orderno'];
		$Edit_DelID = $_POST['ids'];

		//删除操作
		$num = count($Edit_DelID);
		for($i=0;$i<$num;$i++)
		{
			D("Votenote")->where('id='.$Edit_DelID[$i])->delete();
		}
		//编辑
		$num = count($Edit_ID);
		for($i=0;$i<$num;$i++)
		{
			D("Votenote")->execute('update ss_votenote set orderno='.$Edit_Orderno[$i].' where id='.$Edit_ID[$i]);
		}

		$this->assign('jumpUrl',__URL__."/admin_votenote/");
		$this->success('操作成功');
		/*
		$dao = D("Votenote");
		switch ($_GET['action']){
			
			case 'del':
				if(is_array($_REQUEST['ids'])){
					$array = $_REQUEST['ids'];
					$num = count($array);
					for($i=0;$i<$num;$i++)
					{
						$votenoteinfo = $dao->getById($array[$i]);
						if($votenoteinfo){
							$dao->where('id='.$array[$i])->delete();
						}
					}
				}
				$this->assign('jumpUrl',base64_decode($_POST['return']).'#'.time());
				$this->success('操作成功');
				break;
			
		}
		*/
	}

	public function admin_votesignup()
	{
		$condition = 'id>0';
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
		
		$condition .= ' and truename like \'%'.$_GET['keyword'].'%\'';
		$orderby = 'addtime desc';
		$votesignup = D("Votesignup");
		$count = $votesignup->where($condition)->count();
		$listRows = 20;
		$linkFront = '';
		import("@.ORG.Page");
		$p = new Page($count,$listRows,$linkFront);
		$votesignups = $votesignup->limit($p->firstRow.",".$p->listRows)->where($condition)->order($orderby)->select();
		$p->setConfig('header','条');
		$page = $p->show();
		$this->assign('page',$page);
		$this->assign('votesignups',$votesignups);

		$this->display();
	}

	public function view_signupuser(){
		if($_GET['signid'] == ''){
			$this->error('参数错误');
		}
		else{
			$votesignupinfo = D("Votesignup")->getById($_GET["signid"]);
			if($votesignupinfo){
				$this->assign('votesignupinfo',$votesignupinfo);
			}
			else{
				$this->error('找不到该报名记录');
			}
		}
		
		$this->display();
	}

	public function del_votesignup(){
		if($_GET["signid"] == '')
		{
			$this->error('缺少参数或参数不正确');
		}
		else{
			$dao = D("Votesignup");
			$votesignupinfo = $dao->getById($_GET["signid"]);
			if($votesignupinfo){
				$dao->where('id='.$_GET["signid"])->delete();
				$this->assign('jumpUrl',base64_decode($_GET['return']));
				$this->success('成功删除');
			}
			else{
				$this->error('找不到该报名');
			}
		}
	}

	public function opt_votesignup()
	{
		$dao = D("Votesignup");
		switch ($_GET['action']){
			
			case 'del':
				if(is_array($_REQUEST['ids'])){
					$array = $_REQUEST['ids'];
					$num = count($array);
					for($i=0;$i<$num;$i++)
					{
						$votesignupinfo = $dao->getById($array[$i]);
						if($votesignupinfo){
							$dao->where('id='.$array[$i])->delete();
						}
					}
				}
				$this->assign('jumpUrl',base64_decode($_POST['return']).'#'.time());
				$this->success('操作成功');
				break;
			
		}
	}

	public function admin_voteconfig(){
		$voteconfig = D("Voteconfig")->find(1);
		if($voteconfig){
			$this->assign('voteconfig',$voteconfig);
		}
		else{
			$this->assign('jumpUrl',__APP__.'/Index/mainFrame');
			$this->error('系统参数读取错误');
		}
		$this->display();
	}

	public function savevoteconfig()
	{
		$voteconfig = D('Voteconfig');
		$vo = $voteconfig->create();
		if(!$vo) {
			$this->assign('jumpUrl',__URL__.'/admin_voteconfig/');
			$this->error('修改失败');
		}else{
			$voteconfig->save();

			$this->assign('jumpUrl',__URL__.'/admin_voteconfig/');
			$this->success('修改成功');
		}
	}

	public function admin_voterollpic()
	{
		$rollpics = D("Voterollpic")->where("")->order('orderno')->select();
		$this->assign("rollpics",$rollpics);
		$this->display();
	}

	public function save_voterollpic()
	{
		//上传图片
		import("@.ORG.UploadFile");
		$upload = new UploadFile();
		//设置上传文件大小
		$upload->maxSize  = 1048576 ;
		//设置上传文件类型
		$upload->allowExts  = explode(',','jpg,png');
		//设置上传目录
		//每个用户一个文件夹
		$prefix = date('Y/m/d');
		$uploadPath =  '../Public/vote/'.$prefix.'/';
		if(!is_dir($uploadPath)){
        	mkdir($uploadPath);
		}
		$upload->savePath =  $uploadPath;
		$upload->saveRule = uniqid;
		//执行上传操作
		if(!$upload->upload()) {
			// 捕获上传异常
			if($upload->getErrorMsg() != '没有选择上传文件'){
				$this->error($upload->getErrorMsg());
			}
		}
		else{
			$uploadList = $upload->getUploadFileInfo();
			$rollpicpath = '/Public/vote/'.$prefix.'/'.$uploadList[0]['savename'];
		}

		$Edit_ID = $_POST['id'];
		$Edit_Orderno = $_POST['orderno'];
		$Edit_Picpath = $_POST['picpath'];
		$Edit_Linkurl = $_POST['linkurl'];
		$Edit_DelID = $_POST['ids'];

		//删除操作
		$num = count($Edit_DelID);
		for($i=0;$i<$num;$i++)
		{
			D("Voterollpic")->where('id='.$Edit_DelID[$i])->delete();
		}
		//编辑
		$num = count($Edit_ID);
		for($i=0;$i<$num;$i++)
		{
			D("Voterollpic")->execute('update ss_voterollpic set picpath="'.$Edit_Picpath[$i].'",linkurl="'.$Edit_Linkurl[$i].'",orderno='.$Edit_Orderno[$i].' where id='.$Edit_ID[$i]);
		}

		if($_POST['add_orderno'] != '' && $rollpicpath != '' && $_POST['add_linkurl'] != ''){
			$Voterollpic = D('Voterollpic');
			$Voterollpic->create();
			$Voterollpic->orderno = $_POST['add_orderno'];
			$Voterollpic->picpath = $rollpicpath;
			$Voterollpic->linkurl = $_POST['add_linkurl'];
			$Voterollpic->addtime = time();
			$rollpicID = $Voterollpic->add();
		}

		$this->assign('jumpUrl',__URL__."/admin_voterollpic/");
		$this->success('操作成功');
	}

	public function admin_votepartner()
	{
		$votelinks = D("Votelink")->where("")->order('orderno')->select();
		$this->assign("votelinks",$votelinks);
		$this->display();
	}

	public function save_votepartner()
	{
		//上传图片
		import("@.ORG.UploadFile");
		$upload = new UploadFile();
		//设置上传文件大小
		$upload->maxSize  = 1048576 ;
		//设置上传文件类型
		$upload->allowExts  = explode(',','jpg,png');
		//设置上传目录
		//每个用户一个文件夹
		$prefix = date('Y/m/d');
		$uploadPath =  '../Public/vote/'.$prefix.'/';
		if(!is_dir($uploadPath)){
        	mkdir($uploadPath);
		}
		$upload->savePath =  $uploadPath;
		$upload->saveRule = uniqid;
		//执行上传操作
		if(!$upload->upload()) {
			// 捕获上传异常
			if($upload->getErrorMsg() != '没有选择上传文件'){
				$this->error($upload->getErrorMsg());
			}
		}
		else{
			$uploadList = $upload->getUploadFileInfo();
			$partnerpicpath = '/Public/vote/'.$prefix.'/'.$uploadList[0]['savename'];
		}

		$Edit_ID = $_POST['id'];
		$Edit_Orderno = $_POST['orderno'];
		$Edit_Linklogo = $_POST['linklogo'];
		$Edit_Linkurl = $_POST['linkurl'];
		$Edit_DelID = $_POST['ids'];

		//删除操作
		$num = count($Edit_DelID);
		for($i=0;$i<$num;$i++)
		{
			D("Votelink")->where('id='.$Edit_DelID[$i])->delete();
		}
		//编辑
		$num = count($Edit_ID);
		for($i=0;$i<$num;$i++)
		{
			D("Votelink")->execute('update ss_votelink set linklogo="'.$Edit_Linklogo[$i].'",linkurl="'.$Edit_Linkurl[$i].'",orderno='.$Edit_Orderno[$i].' where id='.$Edit_ID[$i]);
		}

		if($_POST['add_orderno'] != '' && $partnerpicpath != '' && $_POST['add_linkurl'] != ''){
			$Votelink = D('Votelink');
			$Votelink->create();
			$Votelink->orderno = $_POST['add_orderno'];
			$Votelink->linklogo = $partnerpicpath;
			$Votelink->linkurl = $_POST['add_linkurl'];
			$Votelink->addtime = time();
			$linkID = $Votelink->add();
		}

		$this->assign('jumpUrl',__URL__."/admin_votepartner/");
		$this->success('操作成功');
	}


}