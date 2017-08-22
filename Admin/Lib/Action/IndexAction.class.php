<?php

class IndexAction extends Action {
	
	
	
	//家族代理申请相关
	public function del_sqagent(){
		$sqid=$_GET['sqid'];
		//var_dump($sqid);
		$res=M("agentfamily")->where("id=".$sqid)->delete();
		if($res){
			$this->success("删除成功");
		}else{
			$this->error("删除失败");
		}
	}
	public function edit_sqagent(){
		$sqid=$_GET['sqid'];
		$uid=M("agentfamily")->where("id=".$sqid)->getField('uid');
		$fix= C('DB_PREFIX');
		$field="m.nickname,m.earnbean,af.*";
		$sqinfo = M('agentfamily af')->field($field)->join("{$fix}member m ON m.id=af.uid")->where("m.id=".$uid)->select();
        $emceelevel = getEmceelevel($sqinfo[0]['earnbean']);
		$sqinfo[0]['emceelevel']=$emceelevel;
		//var_dump($sqinfo);
        $this->assign("sqinfo",$sqinfo); 
		
		$this->display();
	}
	
	public function do_edit_sqagent(){
		//var_dump($_POST);
		//根据接收到的信心更新数据库 需要更新 agentfamily表中的状态字段 以及member 表中的emceeagent字段
        $zhuangtai=$_POST['zhuangtai'];
		//var_dump($zhuangtai);
		$afmodel=M("agentfamily");
		$mmodel=M("member");
	if(!empty($_POST)){
        if($afmodel->create()){
        	$afmodel->id=$_POST['id'];
        	$afmodel->shtime=time();
			$afmodel->zhuangtai=$zhuangtai;
			//var_dump($afmodel);
		
			if($afmodel->save()){
				
					$mmodel->id=$_POST['uid'];
					if($zhuangtai=="已通过"){
					$mmodel->emceeagent="y";
					}else{
						
						$mmodel->emceeagent="n";
					}
				
					
					$mmodel->emceeagenttime=time();
					if($mmodel->save()){
						$this->success("审核成功");
					}else{
						$this->error("审核失败，重新审核");
					}
				
			}else{
				$this->error("审核失败");
			}
        }else{
          $this->error($afmodel->getError());
        }
		
		
		
		
	}	
	}
	public function admin_sqagentwsh(){
		$count=M("agentfamily")->where("zhuangtai='未审核'")->count();
		//使用联合查询带分页 查询出申请用户的相关信息
		import("@.ORG.Page");
		$p = new Page($count,20);
		$p->setConfig('header','条');
		$page = $p->show();
		$fix= C('DB_PREFIX');
		$field="m.nickname,m.earnbean,af.*";
		$res = M('agentfamily af')->field($field)->join("{$fix}member m ON m.id=af.uid")->where("zhuangtai='未审核'")->limit($p->firstRow.",".$p->listRows)->select();
		//根据查到的earnbean 查询用户等级
		$a=0;
		foreach($res as $k=>$vo){
		$emceelevel = getEmceelevel($vo['earnbean']);
		$res[$a]['emceelevel']=$emceelevel;
		$a++;
		}
		$this->assign("page",$page);
		$this->assign("lists",$res);
		$this->display();
	}
	public function admin_sqagentpass(){
		$count=M("agentfamily")->where("zhuangtai='已通过'")->count();
		//使用联合查询带分页 查询出申请用户的相关信息
		import("@.ORG.Page");
		$p = new Page($count,20);
		$p->setConfig('header','条');
		$page = $p->show();
		$fix= C('DB_PREFIX');
		$field="m.nickname,m.earnbean,af.*";
		$res = M('agentfamily af')->field($field)->join("{$fix}member m ON m.id=af.uid")->where("zhuangtai='已通过'")->limit($p->firstRow.",".$p->listRows)->select();
		//根据查到的earnbean 查询用户等级
		$a=0;
		foreach($res as $k=>$vo){
		$emceelevel = getEmceelevel($vo['earnbean']);
		$res[$a]['emceelevel']=$emceelevel;
		$a++;
		}
		$this->assign("page",$page);
		$this->assign("lists",$res);
		$this->display();
	
	}
	public function admin_sqagentnopass(){
		$count=M("agentfamily")->where("zhuangtai='未通过'")->count();
		//使用联合查询带分页 查询出申请用户的相关信息
		import("@.ORG.Page");
		$p = new Page($count,20);
		$p->setConfig('header','条');
		$page = $p->show();
		$fix= C('DB_PREFIX');
		$field="m.nickname,m.earnbean,af.*";
		$res = M('agentfamily af')->field($field)->join("{$fix}member m ON m.id=af.uid")->where("zhuangtai='未通过'")->limit($p->firstRow.",".$p->listRows)->select();
		//根据查到的earnbean 查询用户等级
		$a=0;
		foreach($res as $k=>$vo){
		$emceelevel = getEmceelevel($vo['earnbean']);
		$res[$a]['emceelevel']=$emceelevel;
		$a++;
		}
		$this->assign("page",$page);
		$this->assign("lists",$res);
		$this->display();
	}
	
	
	
	//活动页面轮播管理
	public function admin_huodongrollpic(){
		$hdrollpics = M("huodongrollpic")->where("")->order('orderno')->select();
		//var_dump($hdrollpics);
		$this->assign("hdrollpics",$hdrollpics);
		$this->display();
	}

	public function save_huodongrollpic()
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
		$prefix = date('Y-m');
		$uploadPath =  '../Public/huodongrollpic/'.$prefix.'/';
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
			$rollpicpath = '/Public/huodongrollpic/'.$prefix.'/'.$uploadList[0]['savename'];
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
			M("huodongrollpic")->where('id='.$Edit_DelID[$i])->delete();
		}
		//编辑
		$num = count($Edit_ID);
		for($i=0;$i<$num;$i++)
		{
			M("huodongrollpic")->execute('update ss_huodongrollpic set picpath="'.$Edit_Picpath[$i].'",linkurl="'.$Edit_Linkurl[$i].'",orderno='.$Edit_Orderno[$i].' where id='.$Edit_ID[$i]);
		}
           
		
		if($_POST['add_orderno'] != '' && $rollpicpath != '' && $_POST['add_linkurl'] != ''){
			$Rollpic = M("huodongrollpic");
			$Rollpic->create();
			$Rollpic->orderno = $_POST['add_orderno'];
			$Rollpic->picpath = $rollpicpath;
			$Rollpic->linkurl = $_POST['add_linkurl'];
			$Rollpic->addtime = time();
			var_dump($Rollpic);
		
			$rollpicID = $Rollpic->add();
			var_dump($rollpicID);
		}

		$this->assign('jumpUrl',__URL__."/admin_huodongrollpic/");
		$this->success('操作成功');
	}
	
	
	//活动分类管理
	public function del_huodongfenlei(){
			$fenleiid=$_GET["fenleiid"];
		
		$res=M("announce")->where("fid=".$fenleiid)->select();
		
		if(!empty($res)){
			$this->error("请先删除当前分类下的文章！");
			
		}else{
		$del=M("huodongfenlei")->where("id=".$fenleiid)->delete();
		if($del){
			$this->success("删除成功！");
		}else{
            $this->error("删除失败！");
		}
		}
	}
	
	public function  edit_huodongfenlei(){
		$fenleiid=$_GET["fenleiid"];
		//var_dump($fenleiid);
		$res=M("huodongfenlei")->where("id=".$fenleiid)->find();
		//var_dump($res);
		
		$hmodel=M("huodongfenlei");
		if(!empty($_POST)){
			if($hmodel->create()){
				if($hmodel->save()){
					$this->success("修改成功！","__URL__/admin_huodongfenlei");
				}else{
					$this->error("修改失败！");
				}
			}else{
				$this->error($hmodel->getError());
			}
			
		}
		$this->assign("fenlei",$res);
		$this->display();
	}
	public function  admin_huodongfenlei(){
		//查询出所有的活动分类
		$res=M("huodongfenlei")->select();
		$this->assign("huodongfenleis",$res);
		
		$this->display();
	}
	public function add_huodongfenlei(){
		
		$hmodel=M("huodongfenlei");
		if(!empty($_POST)){
		  if(!empty($_POST['title'])){
			if($hmodel->create()){
				$hmodel->addtime=time();
		        if($hmodel->add()){
		        	$this->success("添加分类成功","__URL__/admin_huodongfenlei");
		        }else{
		        	$this->error("添加失败");
		        }
			}else{
				$this->error($hmodel->getError());
			}
		   }else{
			  $this->error("分类标题不能为空！");
		}
		}
		
		
		$this->display();
	}
	
	
	function _initialize(){
		C('HTML_CACHE_ON',false);

		$curUrl = base64_encode($_SERVER["REQUEST_URI"]);
		if($_SESSION['lock_screen'] == 1 && !strpos($_SERVER["REQUEST_URI"],'login')){
			session('manager',null);
			session('lock_screen',0);
			session('trytimes',0);
			
			$this->assign('jumpUrl',__URL__."/login/return/".$curUrl);
			$this->error('请登录后操作');
		}

		if(!strpos($_SERVER["REQUEST_URI"],'login') && !strpos($_SERVER["REQUEST_URI"],'verify') && !strpos($_SERVER["REQUEST_URI"],'logout') && !$_SESSION['manager'])
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

		$username = $_POST["username"];
		$password = md5($_POST["password"]);

		$adminDao = D('Admin');
		$admin = $adminDao->where("adminname='".$username."' and password='".$password."'")->select();
		if($admin){
			//写入本次登录时间及IP
			//$adminDao->setField('lastlogtime',time(),"id=".$admin[0]['id']);
			//$adminDao->setField('lastlogip',get_client_ip(),"id=".$admin[0]['id']);
			$adminDao->execute('update ss_admin set lastlogtime='.time().',lastlogip="'.get_client_ip().'" where id='.$admin[0]['id']);

			//写入SESSION
			session('adminid',$admin[0]['id']);
			session('adminname',$_POST["username"]);
			session('manager','y');

			if($_POST['next_action']!=''){
				$this->assign('jumpUrl',base64_decode($_POST['next_action']));
			}
			else{
				$this->assign('jumpUrl',__URL__);
			}
			$this->success('登录成功');
		}else{
			$this->error('用户名或密码错误,请重新登录');
		}
    }

	function logout()
	{
		session('adminid',null);
		session('adminname',null);
		session('manager',null);
		$this->assign('jumpUrl',__URL__.'/login/');
		$this->success('退出成功');
	}

	public function index()
    {
		$adminqmenus = D("Adminqmenu")->where("adminid=".$_SESSION['adminid'])->order('addtime')->select();
		$this->assign("adminqmenus",$adminqmenus);
        $this->display();
    }

	public function leftFrame()
	{
		$adminmenus = D("Adminmenu")->where("parentid=".$_GET['menuid'])->order('id')->select();
		foreach($adminmenus as $n=> $val){
			$adminmenus[$n]['voo']=D("Adminmenu")->where('parentid='.$val['id'])->order('id')->select();
			
		}

		if($_GET['menuid'] == 1){
			$adminqmenus = D("Adminqmenu")->where("adminid=".$_SESSION['adminid'])->order('addtime')->select();
			$this->assign("adminqmenus",$adminqmenus);
		}
          
		$this->assign("adminmenus",$adminmenus);

		$this->display();
	}

	public function mainFrame()
	{
		$admin = D("Admin")->find($_SESSION["adminid"]);
		$this->assign('admin',$admin);
		$adminqmenus = D("Adminqmenu")->where("adminid=".$_SESSION['adminid'])->order('addtime')->select();
		$this->assign("adminqmenus",$adminqmenus);
		
		$this->display();
	}

	public function public_map()
	{
		$adminmenus = D("Adminmenu")->where("parentid=0")->order('id')->select();
		foreach($adminmenus as $n=> $val){
			$adminmenus[$n]['voo']=D("Adminmenu")->where('parentid='.$val['id'])->order('id')->select();
			foreach($adminmenus[$n]['voo'] as $n2=> $val2){
				$adminmenus[$n]['voo'][$n2]['voo2']=D("Adminmenu")->where('parentid='.$val2['id'])->order('id')->select();
			}
		}
		$this->assign("adminmenus",$adminmenus);
		$this->display();
	}

	public function public_current_pos()
	{
		$menu = D("Adminmenu")->find($_GET["menuid"]);
		if($menu){
			echo $menu['position'];
		}
	}

	public function public_ajax_add_panel()
	{
		$menu = D("Adminmenu")->find($_POST["menuid"]);
		if($menu){
			$qmenu = D("Adminqmenu")->where("adminid=".$_SESSION['adminid']." and menuid=".$_POST["menuid"])->select();
			if(!$qmenu && $menu['url'] !=''){
				$qmenu = D("Adminqmenu")->execute("insert into ss_adminqmenu(adminid,menuid,menuname,url,addtime) values(".$_SESSION['adminid'].",".$_POST["menuid"].",'".$menu['menuname']."','".$menu['url']."',".time().")");
			}
		}

		$adminqmenus = D("Adminqmenu")->where("adminid=".$_SESSION['adminid'])->order('addtime')->select();
		foreach($adminqmenus as $n=> $val){
			echo "<span><a onclick='paneladdclass(this);' target='right' href='".$val['url']."'>".$val['menuname']."</a>  <a class='panel-delete' href='javascript:delete_panel(".$val['menuid'].");'></a></span>";
		}
	}

	public function public_ajax_delete_panel()
	{
		D("Adminqmenu")->where('adminid='.$_SESSION["adminid"].' and menuid='.$_POST["menuid"])->delete();

		$adminqmenus = D("Adminqmenu")->where("adminid=".$_SESSION['adminid'])->order('addtime')->select();
		foreach($adminqmenus as $n=> $val){
			echo "<span><a onclick='paneladdclass(this);' target='right' href='".$val['url']."'>".$val['menuname']."</a>  <a class='panel-delete' href='javascript:delete_panel(".$val['menuid'].");'></a></span>";
		}
	}



	public function public_session_life()
	{
		session('adminid',$_SESSION['adminid']);
		session('adminname',$_SESSION['adminname']);
		session('manager','y');
	}

	public function public_lock_screen()
	{
		session('lock_screen',1);
	}

	public function public_login_screenlock()
	{
		$password = md5($_REQUEST["lock_password"]);

		$adminDao = D('Admin');
		$admin = $adminDao->where("adminname='".$_SESSION['adminname']."' and password='".$password."'")->select();
		if($admin){
			echo '1';
			session('lock_screen',0);
			session('trytimes',0);
			exit;
		}
		else{
			if($_SESSION['trytimes'] == 3){
				echo '3';
				exit;
			}

			if($_SESSION['trytimes'] == ''){
				echo '2|2';
				session('trytimes',1);
				exit;
			}
			else{
				echo '2|'.(2-$_SESSION['trytimes']);
				session('trytimes',($_SESSION['trytimes']+1));
				exit;
			}
		}
	}

	public function edit_pwd()
	{
		if($_GET['action'] == 'public_password_ajx'){
			$password = md5($_GET["old_password"]);
			$admin = D("Admin")->where("adminname='".$_SESSION["adminname"]."' and password='".$password."'")->select();
			if($admin){
				echo '1';
			}
			else{
				echo '0';
			}
			exit;
		}

		$admin = D("Admin")->find($_SESSION["adminid"]);
		$this->assign('admin',$admin);
		
		$this->display();
	}

	public function do_edit_pwd()
	{
		if($_POST['new_password'] == ''){
			$this->assign('jumpUrl',__URL__."/edit_pwd/");
			$this->success('修改成功');
		}

		$oldpassword = md5($_POST["old_password"]);
		$adminDao = D('Admin');
		$admininfo = $adminDao->where("adminname='".$_SESSION["adminname"]."' and password='".$oldpassword."'")->select();
		if($admininfo){
			$vo = $adminDao->create();
			if(!$vo) {
				$this->error($adminDao->getError());
			}else{
				$adminDao->password = md5($_POST['new_password']);
				$adminDao->save();

				$this->assign('jumpUrl',__URL__."/edit_pwd/");
				$this->success('修改成功');
			}
		}
		else{
			$this->error('旧密码输入错误');
		}
	}



	public function cache_all()
	{
		$this->deldir('../Runtime');
		
		$referer = $_SERVER['HTTP_REFERER'];
		$urlArr = explode("/Admin/", $referer);
		if($urlArr[1] == ''){
			$this->assign('jumpUrl',__URL__.'/mainFrame');
		}
		$this->success('缓存更新成功');
	}

	protected function deldir($dir) {
		if (!file_exists($dir)){
			return true;
		}
		else{
			@chmod($dir, 0777);
		}
		$dh=opendir($dir);
		while ($file=readdir($dh)) {
			if($file!="." && $file!="..") {
				$fullpath=$dir."/".$file;
				if(!is_dir($fullpath)) {
					unlink($fullpath);
				} 
				else {
					$this->deldir($fullpath);
				}
			}
		}

		closedir($dh);

		if(rmdir($dir)) {
			return true;
		} 
		else {
			return false;
		}
	}

	//设置
	public function admin_syspara()
	{
		$siteconfig = D("Siteconfig")->find(1);
		if($siteconfig){
			$this->assign('siteconfig',$siteconfig);
		}
		else{
			$this->assign('jumpUrl',__URL__.'/mainFrame');
			$this->error('系统参数读取错误');
		}
		$this->display();
	}

	public function save_syspara()
	{
		
		$siteconfig = D('Siteconfig');
		$vo = $siteconfig->create();
		if(!$vo) {
			$this->assign('jumpUrl',__URL__.'/admin_syspara/');
			$this->error('修改失败');
		}else{
			$siteconfig->save();
            $cdn=$_POST['cdn'];
			$fps=$_POST['fps'];
			$zddk=$_POST['zddk'];
		    $pz=$_POST['pz'];
			$zjg=$_POST['zjg'];
			$cdnl=$_POST['cdnl'];
			$height=$_POST['height'];
			$width=$_POST['width'];
			$sql="update ss_siteconfig set cdn='{$cdn}',fps='{$fps}',zddk='{$zddk}',pz='{$pz}',zjg='{$zjg}',cdnl='{$cdnl}',height='{$height}',width='{$width}' where id=1";
			M('siteconfig')->execute($sql);
			$this->assign('jumpUrl',__URL__.'/admin_syspara/');
			$this->success('修改成功');
		}
	}

	public function admin_cacheset()
	{
		$this->display();
	}

	public function save_cacheset(){
		$para = $_POST['para'];
		if (is_array($para)) {
			foreach ($para as $key=>$val) {
				$filepath = '../Conf/config.php';
				if (file_exists($filepath)) {
					$arr = include $filepath;
					$arr[$key] = $val;
				} else {
					$arr = array($key=>$val,'disable'=>0, 'dirname'=>$key);
				}
				@file_put_contents($filepath, '<?php return '.var_export($arr, true).';?>');
			}
			$this->success('保存成功');
		} else {
			$this->error('保存失败');
		}
	}

	public function admin_rtmpserver()
	{
		$servers = D("Server")->where("")->order('addtime')->select();
		$this->assign("servers",$servers);

		$this->display();
	}

	public function edit_server(){
		header("Content-type: text/html; charset=utf-8"); 
		if($_GET['serverid'] == ''){
			echo '<script>alert(\'参数错误\');window.top.right.location.reload();window.top.art.dialog({id:"edit"}).close();</script>';
		}
		else{
			$serverinfo = D("Server")->find($_GET["serverid"]);
			if($serverinfo){
				$this->assign('serverinfo',$serverinfo);
			}
			else{
				echo '<script>alert(\'找不到该服务器\');window.top.right.location.reload();window.top.art.dialog({id:"edit"}).close();</script>';
			}
		}
		
		$this->display();
	}

	public function do_edit_server(){
		header("Content-type: text/html; charset=utf-8"); 

		$server = D('Server');
		$vo = $server->create();
		if(!$vo) {
			echo '<script>alert(\''.$server->getError().'\');window.top.art.dialog({id:"edit"}).close();</script>';
		}else{
			
			D("Member")->where("1=1")->save(array("host"=>$_POST['server_ip'])); 
		    
			$server->save();

			echo '<script>alert(\'修改成功\');window.top.right.location.reload();window.top.art.dialog({id:"edit"}).close();</script>';
		}
	}

	public function del_server(){
		if($_GET["serverid"] == '')
		{
			$this->error('缺少参数或参数不正确');
		}
		else{
			$dao = D("Server");
			$serverinfo = $dao->find($_GET["serverid"]);
			if($serverinfo){
				$dao->where('id='.$_GET["serverid"])->delete();
				$this->assign('jumpUrl',__URL__.'/admin_rtmpserver/');
				$this->error('成功删除');
			}
			else{
				$this->error('找不到该服务器');
			}
		}
	}

	public function add_server(){
		$this->display();
	}

	public function do_add_server(){
		if($_POST['server_name'] == ''){
			$this->error('服务器名称不能为空');
		}

		if($_POST['server_ip'] == ''){
			$this->error('访问域名或IP不能为空');
		}
		
		$server = D('Server');
		$vo = $server->create();
		if(!$vo) {
			$this->error($server->getError());
		}else{
			$server->add();

			$this->assign('jumpUrl',__URL__.'/admin_server/');
			$this->success('添加成功');
		}
	}

	public function admin_deduct()
	{
		$siteconfig = D("Siteconfig")->find(1);
		if($siteconfig){
			$this->assign('siteconfig',$siteconfig);
		}
		else{
			$this->assign('jumpUrl',__URL__.'/mainFrame');
			$this->error('系统参数读取错误');
		}
		$this->display();
	}

	public function save_deduct()
	{
		$siteconfig = D('Siteconfig');
		$vo = $siteconfig->create();
		if(!$vo) {
			$this->assign('jumpUrl',__URL__.'/admin_deduct/');
			$this->error('修改失败');
		}else{
			$siteconfig->save();

			$this->assign('jumpUrl',__URL__.'/admin_deduct/');
			$this->success('修改成功');
		}
	}

	public function admin_rollpic(){
		$rollpics = D("Rollpic")->where("")->order('orderno')->select();
		$this->assign("rollpics",$rollpics);
		$this->display();
	}

	public function save_rollpic()
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
		$prefix = date('Y-m');
		$uploadPath =  '../Public/rollpic/'.$prefix.'/';
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
			$rollpicpath = '/Public/rollpic/'.$prefix.'/'.$uploadList[0]['savename'];
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
			D("Rollpic")->where('id='.$Edit_DelID[$i])->delete();
		}
		//编辑
		$num = count($Edit_ID);
		for($i=0;$i<$num;$i++)
		{
			D("Rollpic")->execute('update ss_rollpic set picpath="'.$Edit_Picpath[$i].'",linkurl="'.$Edit_Linkurl[$i].'",orderno='.$Edit_Orderno[$i].' where id='.$Edit_ID[$i]);
		}
           
		if($_POST['add_orderno'] != '' && $rollpicpath != '' && $_POST['add_linkurl'] != ''){
			$Rollpic = D('Rollpic');
			$Rollpic->create();
			$Rollpic->orderno = $_POST['add_orderno'];
			$Rollpic->picpath = $rollpicpath;
			$Rollpic->linkurl = $_POST['add_linkurl'];
			$Rollpic->addtime = time();
			$rollpicID = $Rollpic->add();
		}

		$this->assign('jumpUrl',__URL__."/admin_rollpic/");
		$this->success('操作成功');
	}

	public function admin_announce()
	{
		$condition = '';
		
		$orderby = 'addtime desc';
		$announce = D("Announce");
		$count = $announce->where($condition)->count();
		$listRows = 20;
		$linkFront = '';
		import("@.ORG.Page");
		$p = new Page($count,$listRows,$linkFront);
		$announces = $announce->limit($p->firstRow.",".$p->listRows)->where($condition)->order($orderby)->select();
		$p->setConfig('header','条');
		$page = $p->show();
		$this->assign('page',$page);
		$this->assign('announces',$announces);

		$this->display();
	}

	public function add_announce(){
		//查询出当前所有的分类
		$fenleis=M("huodongfenlei")->select();
		$this->assign("fenlei",$fenleis);
	
		$this->display();
	}

	public function do_add_announce(){
		//var_dump($_POST);
		$announce=D("Announce");
		  if(!empty($_POST)){
            import("ORG.Net.UploadFile");  
            //实例化上传类  
            $upload = new UploadFile(); 
            $upload->maxSize = 3145728;  
            //设置文件上传类型  
            $upload->allowExts = array('jpg','gif','png','jpeg');  
            //设置文件上传位置  
            $upload->savePath = "../Public/Uploads/";//这里说明一下，由于ThinkPHP是有入口文件的，所以这里的./Public是指网站根目录下的Public文件夹  
            //设置文件上传名(按照时间)  
            $upload->saveRule = "time";  
            if (!$upload->upload()){  
                $this->error($upload->getErrorMsg());  
            }else{  
                //上传成功，获取上传信息  
                $info = $upload->getUploadFileInfo(); 
            }
          $savename = $info[0]['savename']; 
		   var_dump($savename);
		  
		
		
		
		$vo = $announce->create();
		
		$announce->fengmian=$savename;
	//var_dump($vo);
	

	
		if(!$vo) {
			$this->error($announce->getError());
		}else{
			$annId = $announce->add();
			
		}
	}
		$this->assign('jumpUrl',__URL__."/admin_announce/");
		$this->success('添加成功');
	}

	public function edit_announce(){
		$fenleis=M("huodongfenlei")->select();
		$this->assign("fenlei",$fenleis);
		if($_GET['annid'] == ''){
			$this->error('参数错误');
		}
		else{
			$anninfo = D("Announce")->getById($_GET["annid"]);
			if($anninfo){
				$this->assign('anninfo',$anninfo);
			}
			else{
				$this->error('找不到该公告');
			}
		}
		
		$this->display();
	}

	public function do_edit_announce(){
		if($_POST["id"] == '')
		{
			$this->error('缺少参数或参数不正确');
		}
		else{
			$anninfo = D("Announce")->getById($_POST["id"]);
			if(!$anninfo){
				$this->error('该公告不存在');
			}
		}

		$announce=D("Announce");
		$vo = $announce->create();
		if(!$vo) {
			$this->error($announce->getError());
		}else{
			$announce->save();
		}

		$this->assign('jumpUrl',__URL__."/edit_announce/annid/".$_POST['id']);
		$this->success('修改成功');
	}

	public function del_announce(){
		if($_GET["annid"] == '')
		{
			$this->error('缺少参数或参数不正确');
		}
		else{
			$dao = D("Announce");
			$anninfo = $dao->getById($_GET["annid"]);
			if($anninfo){
				$dao->where('id='.$_GET["annid"])->delete();
				$this->assign('jumpUrl',base64_decode($_GET['return']));
				$this->success('成功删除');
			}
			else{
				$this->error('找不到该公告');
			}
		}
	}

	public function opt_announce()
	{
		$dao = D("Announce");
		switch ($_GET['action']){
			
			case 'del':
				if(is_array($_REQUEST['ids'])){
					$array = $_REQUEST['ids'];
					$num = count($array);
					for($i=0;$i<$num;$i++)
					{
						$anninfo = $dao->getById($array[$i]);
						if($anninfo){
							$dao->where('id='.$array[$i])->delete();
						}
					}
				}
				$this->assign('jumpUrl',base64_decode($_POST['return']).'#'.time());
				$this->success('操作成功');
				break;
			
		}
	}

	public function admin_admin()
	{
		$adminusers = D("Admin")->where("")->order('addtime')->select();
		$this->assign("adminusers",$adminusers);

		$this->display();
	}

	public function edit_adminuser(){
		header("Content-type: text/html; charset=utf-8"); 
		if($_GET['adminid'] == ''){
			echo '<script>alert(\'参数错误\');window.top.right.location.reload();window.top.art.dialog({id:"edit"}).close();</script>';
		}
		else{
			$admininfo = D("Admin")->find($_GET["adminid"]);
			if($admininfo){
				$this->assign('admininfo',$admininfo);
			}
			else{
				echo '<script>alert(\'找不到该管理员\');window.top.right.location.reload();window.top.art.dialog({id:"edit"}).close();</script>';
			}
		}
		
		$this->display();
	}

	public function do_edit_adminuser(){
		header("Content-type: text/html; charset=utf-8"); 
		if($_POST['password'] == ''){
			echo '<script>window.top.right.location.reload();window.top.art.dialog({id:"edit"}).close();</script>';
			exit;
		}

		$admin = D('Admin');
		$vo = $admin->create();
		if(!$vo) {
			echo '<script>alert(\''.$admin->getError().'\');window.top.art.dialog({id:"edit"}).close();</script>';
		}else{
			$admin->password = md5($_POST['password']);
			$admin->save();

			echo '<script>alert(\'修改成功\');window.top.right.location.reload();window.top.art.dialog({id:"edit"}).close();</script>';
		}
	}

	public function del_adminuser(){
		if($_GET["adminid"] == '')
		{
			$this->error('缺少参数或参数不正确');
		}
		else{
			$dao = D("Admin");
			$admininfo = $dao->find($_GET["adminid"]);
			if($admininfo){
				$dao->where('id='.$_GET["adminid"])->delete();
				$this->assign('jumpUrl',__URL__.'/admin_admin/');
				$this->success('成功删除');
			}
			else{
				$this->error('找不到该管理员');
			}
		}
	}

	public function add_adminuser(){
		if($_GET['clientid'] == 'username'){
			$admininfo = D("Admin")->where("adminname='".$_GET['username']."'")->select();
			if($admininfo){
				echo '0';
				exit;
			}
			else{
				echo '1';
				exit;
			}
		}

		$this->display();
	}

	public function do_add_adminuser(){
		if($_POST['adminname'] == ''){
			$this->error('用户名不能为空');
		}

		if($_POST['password'] == ''){
			$this->error('密码不能为空');
		}
		
		$admin = D('Admin');
		$vo = $admin->create();
		if(!$vo) {
			$this->error($admin->getError());
		}else{
			$admin->password = md5($_POST['password']);
			$admin->add();

			$this->assign('jumpUrl',__URL__.'/admin_admin/');
			$this->success('添加成功');
		}
	}

	//用户
	public function admin_user()
	{
		$condition = 'isdelete="n"';
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
		if($_GET['sign'] != ''){
			$condition .= ' and sign="'.$_GET['sign'].'"';
		}
		if($_GET['emceeagent'] != ''){
			$condition .= ' and emceeagent="'.$_GET['emceeagent'].'"';
		}
		if($_GET['payagent'] != ''){
			$condition .= ' and payagent="'.$_GET['payagent'].'"';
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

	public function edit_user(){
		if($_GET['userid'] == ''){
			echo '<script>alert(\'参数错误\');window.top.right.location.reload();window.top.art.dialog({id:"edit"}).close();</script>';
		}
		else{
			$userinfo = D("Member")->getById($_GET["userid"]);
			if($userinfo){
				$this->assign('userinfo',$userinfo);
				
				$usersorts = D("Usersort")->where("parentid=0")->order('addtime')->select();
				foreach($usersorts as $n=> $val){
					$usersorts[$n]['voo']=D("Usersort")->where('parentid='.$val['id'])->order('addtime')->select();
				}
				$this->assign("usersorts",$usersorts);

				$servers = D("Server")->where("")->order('addtime')->select();
				$this->assign("servers",$servers);
			}
			else{
				echo '<script>alert(\'找不到该用户\');window.top.right.location.reload();window.top.art.dialog({id:"edit"}).close();</script>';
			}
		}
		
		$this->display();
	}

	public function do_edit_user(){
		header("Content-type: text/html; charset=utf-8"); 
		if($_POST["id"] == '')
		{
			echo '<script>alert(\'缺少参数或参数不正确\');window.top.right.location.reload();window.top.art.dialog({id:"edit"}).close();</script>';
			exit;
		}
		else{
			$userinfo = D("Member")->getById($_POST["id"]);
			if(!$userinfo){
				echo '<script>alert(\'该用户不存在\');window.top.right.location.reload();window.top.art.dialog({id:"edit"}).close();</script>';
				exit;
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
		$prefix = date('Y-m');
		$uploadPath =  '../Public/bigpic/'.$prefix.'/';
		if(!is_dir($uploadPath)){
        	mkdir($uploadPath);
		}
		$upload->savePath =  $uploadPath;
		$upload->saveRule = uniqid;
		//执行上传操作
		if(!$upload->upload()) {
			// 捕获上传异常
			if($upload->getErrorMsg() != '没有选择上传文件'){
				echo '<script>alert(\''.$upload->getErrorMsg().'\');window.top.right.location.reload();window.top.art.dialog({id:"edit"}).close();</script>';
				exit;
			}
		}
		else{
			$uploadList = $upload->getUploadFileInfo();
			//$bigpicpath = '/Public/bigpic/'.$prefix.'/'.$uploadList[0]['savename'];
			foreach($uploadList as $picval){
				if($picval['key'] == 0){
					$bigpicpath = '/Public/bigpic/'.$prefix.'/'.$picval['savename'];
				}
				if($picval['key'] == 1){
					$snap = '/Public/bigpic/'.$prefix.'/'.$picval['savename'];
				}
			}
		}

		$Member=D("Member");
		$vo = $Member->create();

		if(!$vo) {
			$this->error($Member->getError());
		}else{
			if($bigpicpath != ''){
				$Member->bigpic = $bigpicpath;
			}
			if($snap != ''){
				$Member->snap = $snap;
			}
			//密码
			if($_POST['newpwd'] != ''){
include '../config.inc.php';
include '../uc_client/client.php';
$ucresult = uc_user_edit($userinfo['username'], '', $_POST['newpwd'], $userinfo['email'], 1);
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
			$Member->password = md5($_POST['newpwd']);
			$Member->password2 = $this->pswencode($_POST['newpwd']);
					
			if($_POST['agentname'] != ''){
				if($_POST['agentname'] == $userinfo['username']){
					$error = '自已不能做自己的代理';
				}
				else{
					$agentinfo = D("Member")->where('username="'.$_POST['agentname'].'"')->select();
					if($agentinfo){
						if($agentinfo[0]['emceeagent'] == 'n'){
							$error = '指定的代理人没有代理权限';
						}
						else{
							$Member->agentuid = $agentinfo[0]['id'];
						}
					}
					else{
						$error = '没有找到指定的代理人信息';
					}
				}
			}
			else{
				$Member->agentuid = 0;
			}
			if($_POST['payagent'] == 'y'){
				$Member->sellm = '1';
			}
			else{
				$Member->sellm = '0';
			}
			if($_POST['idxrec'] == 'y'){
				$Member->idxrec = 'y';
				$Member->idxrectime = time();
			}
			else{
				$Member->idxrec = 'n';
			}
			
			$Member->save();
			
		}
            $zddk=$_POST['zddk'];
			$pz=$_POST['pz'];
			$fps=$_POST['fps'];
			$zjg=$_POST['zjg'];
			$height=$_POST['height'];
			$width=$_POST['width'];
			$sql="update ss_member set pz='{$pz}',fps='{$fps}',zjg='{$zjg}',zddk='{$zddk}',height='{$height}',width='{$width}' where id={$_POST['id']}";
			
			$Member->execute($sql);
		echo '<script>alert(\'修改成功_'.$error.'\');window.top.right.location.reload();window.top.art.dialog({id:"edit"}).close();</script>';
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

	public function checkIt($number) {     
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

	public function del_user(){
		if($_GET["userid"] == '')
		{
			$this->error('缺少参数或参数不正确');
		}
		else{
			$dao = D("Member");
			$userinfo = $dao->getById($_GET["userid"]);
			if($userinfo){
				$dao->query('update ss_member set isdelete="y" where id='.$_GET["userid"]);
				/*
				D("Attention")->where('uid='.$_GET["userid"].' or attuid='.$_GET["userid"])->delete();
				D("Bandingnote")->where('uid='.$_GET["userid"])->delete();
				D("Beandetail")->where('uid='.$_GET["userid"])->delete();
				D("Chargedetail")->where('uid='.$_GET["userid"])->delete();
				D("Coindetail")->where('uid='.$_GET["userid"].' or touid='.$_GET["userid"])->delete();
				D("Emceeagentbeandetail")->where('uid='.$_GET["userid"])->delete();
				D("Favor")->where('uid='.$_GET["userid"].' or favoruid='.$_GET["userid"])->delete();
				D("Giveaway")->where('uid='.$_GET["userid"].' or touid='.$_GET["userid"])->delete();
				D("Liverecord")->where('uid='.$_GET["userid"])->delete();
				D("Member")->where('id='.$_GET["userid"])->delete();
				D("Payagentbeandetail")->where('uid='.$_GET["userid"])->delete();
				D("Roomadmin")->where('uid='.$_GET["userid"].' or adminuid='.$_GET["userid"])->delete();
				D("Roomnum")->where('uid='.$_GET["userid"])->delete();
				D("Showlistsong")->where('uid='.$_GET["userid"].' or pickuid='.$_GET["userid"])->delete();
				D("Usersong")->where('uid='.$_GET["userid"])->delete();
				D("Wish")->where('uid='.$_GET["userid"])->delete();
				*/
				
				$this->assign('jumpUrl',base64_decode($_GET['return']));
				$this->success('成功删除');
			}
			else{
				$this->error('找不到该用户');
			}
		}
	}

	public function opt_user()
	{
		$dao = D("Member");
		switch ($_GET['action']){
			case 'disaudit':
				if($_GET['userid'] != ''){
					$dao->query('update ss_member set isaudit="n" where id='.$_GET['userid']);
				}
				$this->assign('jumpUrl',base64_decode($_REQUEST['return']).'#'.time());
				$this->success('操作成功');
				break;
			case 'audit':
				if($_GET['userid'] != ''){
					$dao->query('update ss_member set isaudit="y" where id='.$_GET['userid']);
				}
				$this->assign('jumpUrl',base64_decode($_REQUEST['return']).'#'.time());
				$this->success('操作成功');
				break;
			case 'restore':
				if($_GET['userid'] != ''){
					$dao->query('update ss_member set isdelete="n" where id='.$_GET['userid']);
				}
				$this->assign('jumpUrl',base64_decode($_REQUEST['return']).'#'.time());
				$this->success('操作成功');
				break;
			case 'restorebat':
				if(is_array($_REQUEST['ids'])){
					$array = $_REQUEST['ids'];
					$num = count($array);
					for($i=0;$i<$num;$i++)
					{
						$userinfo = $dao->getById($array[$i]);
						if($userinfo){
							$dao->query('update ss_member set isdelete="n" where id='.$array[$i]);
						}
					}
				}
				$this->assign('jumpUrl',base64_decode($_POST['return']).'#'.time());
				$this->success('操作成功');
				break;
			case 'del':
				if(is_array($_REQUEST['ids'])){
					$array = $_REQUEST['ids'];
					$num = count($array);
					for($i=0;$i<$num;$i++)
					{
						$userinfo = $dao->getById($array[$i]);
						if($userinfo){
							$dao->query('update ss_member set isdelete="y" where id='.$array[$i]);
							/*
							D("Attention")->where('uid='.$array[$i].' or attuid='.$array[$i])->delete();
							D("Bandingnote")->where('uid='.$array[$i])->delete();
							D("Beandetail")->where('uid='.$array[$i])->delete();
							D("Chargedetail")->where('uid='.$array[$i])->delete();
							D("Coindetail")->where('uid='.$array[$i].' or touid='.$array[$i])->delete();
							D("Emceeagentbeandetail")->where('uid='.$array[$i])->delete();
							D("Favor")->where('uid='.$array[$i].' or favoruid='.$array[$i])->delete();
							D("Giveaway")->where('uid='.$array[$i].' or touid='.$array[$i])->delete();
							D("Liverecord")->where('uid='.$array[$i])->delete();
							D("Member")->where('id='.$array[$i])->delete();
							D("Payagentbeandetail")->where('uid='.$array[$i])->delete();
							D("Roomadmin")->where('uid='.$array[$i].' or adminuid='.$array[$i])->delete();
							D("Roomnum")->where('uid='.$array[$i])->delete();
							D("Showlistsong")->where('uid='.$array[$i].' or pickuid='.$array[$i])->delete();
							D("Usersong")->where('uid='.$array[$i])->delete();
							D("Wish")->where('uid='.$array[$i])->delete();
							*/
						}
					}
				}
				$this->assign('jumpUrl',base64_decode($_POST['return']).'#'.time());
				$this->success('操作成功');
				break;
			
		}
	}

	public function admin_signuser()
	{
		$condition = 'isdelete="n" and sign<>"n"';
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

	public function admin_onlineuser()
	{
		$condition = 'isdelete="n" and broadcasting="y"';
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
		//print_r($members);die;
		$this->display();
	}

	public function add_user(){
		$this->display();
	}

	public function do_add_user(){
		include '../config.inc.php';
		include '../uc_client/client.php';

		$uid = uc_user_register($_POST['username'], $_POST['password'], $_POST['email']);
		if($uid <= 0) {
			if($uid == -1) {
				$this->error('用户名不合法');
			} elseif($uid == -2) {
				$this->error('包含不允许注册的词语');
			} elseif($uid == -3) {
				$this->error('用户名已经存在');
			} elseif($uid == -4) {
				$this->error('Email 格式有误');
			} elseif($uid == -5) {
				$this->error('Email 不允许注册');
			} elseif($uid == -6) {
				$this->error('该 Email 已经被注册');
			} else {
				$this->error('未知错误');
			}
		}
		else {
			$User=D("Member");
			$User->create();
			$User->username = $_POST['username'];
			$User->nickname = $_POST['username'];
			$User->password = md5($_POST['password']);
			$User->password2 = $this->pswencode($_POST['password']);
			$User->email = $_POST['email'];
			$User->isaudit = 'y';
			$User->regtime = time();
			$roomnum = 99999;    
			do {    
				$roomnum = rand(1000000000,1999999999);   
			} while ($this->checkIt($roomnum)=='');
			$User->curroomnum = $roomnum;
			$User->ucuid = $uid;
			$defaultserver = D("Server")->where('isdefault="y"')->select();
			if($defaultserver){
				$User->host = $defaultserver[0]['server_ip'];
			}
			$userId = $User->add();

			D("Roomnum")->execute('insert into ss_roomnum(uid,num,addtime) values('.$userId.','.$roomnum.','.time().')');

			$this->assign('jumpUrl',__URL__.'/admin_user/');
			$this->success('添加成功');
		}
	}

	public function admin_deluser()
	{
		$condition = 'isdelete="y"';
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

	public function view_liverecord()
	{
		$condition = 'uid='.$_GET['userid'];
		if($_GET['start_time'] != ''){
			$timeArr = explode("-", $_GET['start_time']);
			$unixtime = mktime(0,0,0,$timeArr[1],$timeArr[2],$timeArr[0]);
			$condition .= ' and starttime>='.$unixtime;
		}
		if($_GET['end_time'] != ''){
			$timeArr = explode("-", $_GET['end_time']);
			$unixtime = mktime(0,0,0,$timeArr[1],$timeArr[2],$timeArr[0]);
			$condition .= ' and starttime<='.$unixtime;
		}
		
		$orderby = 'id desc';
		$liverecord = D("Liverecord");
		$count = $liverecord->where($condition)->count();
		$listRows = 20;
		$linkFront = '';
		import("@.ORG.Page");
		$p = new Page($count,$listRows,$linkFront);
		$liverecords = $liverecord->limit($p->firstRow.",".$p->listRows)->where($condition)->order($orderby)->select();
		$p->setConfig('header','条');
		$page = $p->show();
		$this->assign('page',$page);
		$this->assign('liverecords',$liverecords);

		$liverecords_all = $liverecord->where($condition)->order($orderby)->select();
		$this->assign('liverecords_all',$liverecords_all);

		$this->display();
	}

	public function admin_usersort()
	{
		$usersorts = D("Usersort")->where("parentid=0")->order('orderno')->select();
		foreach($usersorts as $n=> $val){
			$usersorts[$n]['voo']=D("Usersort")->where('parentid='.$val['id'])->order('orderno')->select();
		}
		$this->assign("usersorts",$usersorts);
		$this->display();
	}

	public function usersortlistorder()
	{
		$Edit_ID = $_POST['id'];
		$Edit_OrderID = $_POST['orderno'];
		
		$num = count($Edit_ID);
		for($i=0;$i<$num;$i++)
		{
			D("Usersort")->execute('update ss_usersort set orderno='.$Edit_OrderID[$i].' where id='.$Edit_ID[$i]);
		}

		$this->assign('jumpUrl',__URL__."/admin_usersort/");
		$this->success('修改成功');
	}

	public function del_usersort()
	{
		D("Usersort")->where('id='.$_GET['sid'].' or parentid='.$_GET['sid'])->delete();
		if($_GET['type'] == 'sub'){
			D("Member")->execute('update ss_member set sid=0 where sid='.$_GET['sid']);
		}
		else{
			D("Member")->execute('update ss_member set sid=0 where sid in (select id from ss_usersort where parentid='.$_GET['sid'].')');
		}

		$this->assign('jumpUrl',__URL__."/admin_usersort/");
		$this->success('删除成功');
	}

	public function add_usersort()
	{
		$usersorts = D("Usersort")->where("parentid=0")->order('orderno')->select();
		
		$this->assign("usersorts",$usersorts);
		$this->display();
	}

	public function do_add_usersort()
	{
		if($_POST['sortname'] != ''){
			$Usersort = D('Usersort');
			$Usersort->create();
			$Usersort->parentid = $_POST['parentid'];
			$Usersort->sortname = $_POST['sortname'];
			$sortID = $Usersort->add();
		}
		
		if($sortID){
			$this->assign('jumpUrl',__URL__."/admin_usersort/");
			$this->success('添加成功');
		}
		else{
			$this->error('添加失败');
		}
	}

	public function edit_usersort()
	{
		if($_GET["sid"] == '')
		{
			$this->error('缺少参数或参数不正确');
		}
		else{
			$dao = D("Usersort");
			$sortinfo = $dao->getById($_GET["sid"]);
			if($sortinfo){
				$usersorts = D("Usersort")->where("parentid=0")->order('orderno')->select();
				$this->assign("usersorts",$usersorts);

				$this->assign('sortinfo',$sortinfo);
			}
			else{
				$this->error('找不到该类别');
			}
		}

		$this->display();
	}

	public function do_edit_usersort()
	{
		if($_POST["id"] == '')
		{
			$this->error('缺少参数或参数不正确');
		}
		else{
			$dao = D("Usersort");
			$sortinfo = $dao->getById($_POST["id"]);
			if($sortinfo){
				$vo = $dao->create();
				if(!$vo) {
					$this->error($dao->getError());
				}else{
					$dao->save();

					$this->assign('jumpUrl',__URL__."/edit_usersort/sid/".$_POST["id"]);
					$this->success('修改成功');
				}
			}
			else{
				$this->error('找不到该类别');
			}
		}
	}

	public function admin_emceelevel(){
		$emceelevels = D("Emceelevel")->where("")->order('levelid asc')->select();
		$this->assign("emceelevels",$emceelevels);
		$this->display();
	}

	public function save_emceelevel()
	{
		$Edit_ID = $_POST['id'];
		$Edit_levelid = $_POST['levelid'];
		$Edit_levelname = $_POST['levelname'];
		$Edit_earnbean_low = $_POST['earnbean_low'];
		$Edit_earnbean_up = $_POST['earnbean_up'];
		$Edit_DelID = $_POST['ids'];

		//删除操作
		$num = count($Edit_DelID);
		for($i=0;$i<$num;$i++)
		{
			D("Emceelevel")->where('id='.$Edit_DelID[$i])->delete();
		}
		//编辑
		$num = count($Edit_ID);
		for($i=0;$i<$num;$i++)
		{
			D("Emceelevel")->execute('update ss_emceelevel set levelid='.$Edit_levelid[$i].',levelname="'.$Edit_levelname[$i].'",earnbean_low='.$Edit_earnbean_low[$i].',earnbean_up='.$Edit_earnbean_up[$i].' where id='.$Edit_ID[$i]);
		}

		if($_POST['add_levelid'] != '' && $_POST['add_levelname'] != '' && $_POST['add_earnbean_low'] != '' && $_POST['add_earnbean_up'] != ''){
			$EmceeLevel = D('Emceelevel');
			$EmceeLevel->create();
			$EmceeLevel->levelid = $_POST['add_levelid'];
			$EmceeLevel->levelname = $_POST['add_levelname'];
			$EmceeLevel->earnbean_low = $_POST['add_earnbean_low'];
			$EmceeLevel->earnbean_up = $_POST['add_earnbean_up'];
			$EmceeLevel->addtime = time();
			$levelID = $EmceeLevel->add();
		}

		$this->assign('jumpUrl',__URL__."/admin_emceelevel/");
		$this->success('操作成功');
	}

	public function admin_richlevel(){
		$richlevels = D("Richlevel")->where("")->order('levelid asc')->select();
		$this->assign("richlevels",$richlevels);
		$this->display();
	}

	public function save_richlevel()
	{
		$Edit_ID = $_POST['id'];
		$Edit_levelid = $_POST['levelid'];
		$Edit_levelname = $_POST['levelname'];
		$Edit_spendcoin_low = $_POST['spendcoin_low'];
		$Edit_spendcoin_up = $_POST['spendcoin_up'];
		$Edit_DelID = $_POST['ids'];

		//删除操作
		$num = count($Edit_DelID);
		for($i=0;$i<$num;$i++)
		{
			D("Richlevel")->where('id='.$Edit_DelID[$i])->delete();
		}
		//编辑
		$num = count($Edit_ID);
		for($i=0;$i<$num;$i++)
		{
			D("Richlevel")->execute('update ss_richlevel set levelid='.$Edit_levelid[$i].',levelname="'.$Edit_levelname[$i].'",spendcoin_low='.$Edit_spendcoin_low[$i].',spendcoin_up='.$Edit_spendcoin_up[$i].' where id='.$Edit_ID[$i]);
		}

		if($_POST['add_levelid'] != '' && $_POST['add_levelname'] != '' && $_POST['spendcoin_low'] != '' && $_POST['spendcoin_up'] != ''){
			$RichLevel = D('Richlevel');
			$RichLevel->create();
			$RichLevel->levelid = $_POST['add_levelid'];
			$RichLevel->levelname = $_POST['add_levelname'];
			$RichLevel->earnbean_low = $_POST['add_spendcoin_low'];
			$RichLevel->earnbean_up = $_POST['add_spendcoin_up'];
			$RichLevel->addtime = time();
			$levelID = $RichLevel->add();
		}

		$this->assign('jumpUrl',__URL__."/admin_richlevel/");
		$this->success('操作成功');
	}

	public function admin_giftsort(){
		$giftsorts = D("Giftsort")->where("")->order('orderno asc')->select();
		$this->assign("giftsorts",$giftsorts);
		$this->display();
	}

	public function save_giftsort()
	{
		$Edit_ID = $_POST['id'];
		$Edit_orderno = $_POST['orderno'];
		$Edit_sortname = $_POST['sortname'];
		$Edit_DelID = $_POST['ids'];

		//删除操作
		$num = count($Edit_DelID);
		for($i=0;$i<$num;$i++)
		{
			D("Giftsort")->where('id='.$Edit_DelID[$i])->delete();
		}
		//编辑
		$num = count($Edit_ID);
		for($i=0;$i<$num;$i++)
		{
			D("Giftsort")->execute('update ss_giftsort set orderno='.$Edit_orderno[$i].',sortname="'.$Edit_sortname[$i].'" where id='.$Edit_ID[$i]);
			D("Gift")->execute('update ss_gift set sid=0 where sid='.$Edit_ID[$i]);
		}

		if($_POST['add_orderno'] != '' && $_POST['add_sortname'] != ''){
			$Giftsort = D('Giftsort');
			$Giftsort->create();
			$Giftsort->orderno = $_POST['add_orderno'];
			$Giftsort->sortname = $_POST['add_sortname'];
			$Giftsort->addtime = time();
			$sortID = $Giftsort->add();
		}

		$this->assign('jumpUrl',__URL__."/admin_giftsort/");
		$this->success('操作成功');
	}

	public function admin_gift(){
		$giftsorts = D("Giftsort")->where("")->order('orderno asc')->select();
		$this->assign("giftsorts",$giftsorts);

		$gifts = D("Gift")->where("")->order('sid asc,needcoin asc')->select();
		$this->assign("gifts",$gifts);

		$this->display();
	}

	public function save_gift()
	{
		//上传图片
		import("@.ORG.UploadFile");
		$upload = new UploadFile();
		//设置上传文件大小
		$upload->maxSize  = 1048576 ;
		//设置上传文件类型
		$upload->allowExts  = explode(',','gif,jpg,png,swf');
		//设置上传目录
		//每个用户一个文件夹
		$prefix = 'gift';
		$uploadPath =  '../Public/images/'.$prefix.'/';
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
			foreach($uploadList as $picval){
				if($picval['key'] == 0){
					$giftIcon_25 = '/Public/images/'.$prefix.'/'.$picval['savename'];
				}
				if($picval['key'] == 1){
					$giftIcon = '/Public/images/'.$prefix.'/'.$picval['savename'];
				}
				if($picval['key'] == 2){
					$giftSwf = '/Public/images/'.$prefix.'/'.$picval['savename'];
				}
			}
		}

		$Edit_ID = $_POST['id'];
		$Edit_sid = $_POST['sid'];
		$Edit_giftname = $_POST['giftname'];
		$Edit_needcoin = $_POST['needcoin'];
		$Edit_giftIcon_25 = $_POST['giftIcon_25'];
		$Edit_giftIcon = $_POST['giftIcon'];
		$Edit_giftSwf = $_POST['giftSwf'];
		$Edit_DelID = $_POST['ids'];

		//删除操作
		$num = count($Edit_DelID);
		for($i=0;$i<$num;$i++)
		{
			D("Gift")->where('id='.$Edit_DelID[$i])->delete();
		}
		//编辑
		$num = count($Edit_ID);
		for($i=0;$i<$num;$i++)
		{
			D("Gift")->execute('update ss_gift set sid='.$Edit_sid[$i].',giftname="'.$Edit_giftname[$i].'",needcoin='.$Edit_needcoin[$i].',giftIcon_25="'.$Edit_giftIcon_25[$i].'",giftIcon="'.$Edit_giftIcon[$i].'",giftSwf="'.$Edit_giftSwf[$i].'" where id='.$Edit_ID[$i]);
		}

		if($_POST['add_giftname'] != '' && $_POST['add_needcoin'] != '' && $giftIcon_25 != '' && $giftIcon != ''){
			$Gift = D('Gift');
			$Gift->create();
			$Gift->sid = $_POST['add_sid'];
			$Gift->giftname = $_POST['add_giftname'];
			$Gift->needcoin = $_POST['add_needcoin'];
			$Gift->giftIcon_25 = $giftIcon_25;
			$Gift->giftIcon = $giftIcon;
			if($giftSwf != ''){
				$Gift->giftSwf = $giftSwf;
			}
			$Gift->addtime = time();
			$giftID = $Gift->add();
		}

		$this->assign('jumpUrl',__URL__."/admin_gift/");
		$this->success('操作成功');
	}

	public function admin_goodnum()
	{
		$condition = 'id>0';
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
		if($_GET['keyword'] != '' && $_GET['keyword'] != '请输入靓号号码'){
			$condition .= ' and num like \'%'.$_GET['keyword'].'%\'';
		}
		if($_GET['length'] != ''){
			$condition .= ' and length='.$_GET['length'];
		}
		if($_GET['issale'] != ''){
			$condition .= ' and issale="'.$_GET['issale'].'"';
		}
		if($_GET['owneruid'] != '' && $_GET['owneruid'] != '请输入用户UID'){
			if(preg_match("/^\d*$/",$_GET['keyword'])){
				$condition .= ' and owneruid='.$_GET['owneruid'];
			}
		}
		
		$orderby = 'id desc';
		$goodnum = D("Goodnum");
		$count = $goodnum->where($condition)->count();
		$listRows = 20;
		$linkFront = '';
		import("@.ORG.Page");
		$p = new Page($count,$listRows,$linkFront);
		$goodnums = $goodnum->limit($p->firstRow.",".$p->listRows)->where($condition)->order($orderby)->select();
		$p->setConfig('header','条');
		$page = $p->show();
		$this->assign('page',$page);
		$this->assign('goodnums',$goodnums);

		$this->display();
	}

	public function edit_goodnum(){
		if($_GET['numid'] == ''){
			echo '<script>alert(\'参数错误\');window.top.right.location.reload();window.top.art.dialog({id:"edit"}).close();</script>';
		}
		else{
			$numinfo = D("Goodnum")->getById($_GET["numid"]);
			if($numinfo){
				if($numinfo['issale'] == 'y'){
					echo '<script>alert(\'该靓号已销售不可修改\');window.top.right.location.reload();window.top.art.dialog({id:"edit"}).close();</script>';
				}
				$this->assign('numinfo',$numinfo);
			}
			else{
				echo '<script>alert(\'找不到该靓号\');window.top.right.location.reload();window.top.art.dialog({id:"edit"}).close();</script>';
			}
		}
		
		$this->display();
	}

	public function do_edit_goodnum(){
		header("Content-type: text/html; charset=utf-8"); 
		if($_POST["id"] == '')
		{
			echo '<script>alert(\'缺少参数或参数不正确\');window.top.right.location.reload();window.top.art.dialog({id:"edit"}).close();</script>';
			exit;
		}
		else{
			$numinfo = D("Goodnum")->getById($_POST["id"]);
			if(!$numinfo){
				echo '<script>alert(\'该靓号不存在\');window.top.right.location.reload();window.top.art.dialog({id:"edit"}).close();</script>';
				exit;
			}
		}

		$Goodnum=D("Goodnum");
		$vo = $Goodnum->create();
		if(!$vo) {
			$this->error($Goodnum->getError());
		}else{
			
			$Goodnum->save();
		}

		echo '<script>alert(\'修改成功\');window.top.right.location.reload();window.top.art.dialog({id:"edit"}).close();</script>';

	}

	public function give_goodnum(){
		if($_GET['numid'] == ''){
			echo '<script>alert(\'参数错误\');window.top.right.location.reload();window.top.art.dialog({id:"edit"}).close();</script>';
		}
		else{
			$numinfo = D("Goodnum")->getById($_GET["numid"]);
			if($numinfo){
				if($numinfo['issale'] == 'y'){
					echo '<script>alert(\'该靓号已销售不可赠送\');window.top.right.location.reload();window.top.art.dialog({id:"edit"}).close();</script>';
				}
				$this->assign('numinfo',$numinfo);
			}
			else{
				echo '<script>alert(\'找不到该靓号\');window.top.right.location.reload();window.top.art.dialog({id:"edit"}).close();</script>';
			}
		}
		
		$this->display();
	}

	public function do_give_goodnum(){
		header("Content-type: text/html; charset=utf-8"); 
		if($_POST["id"] == '')
		{
			echo '<script>alert(\'缺少参数或参数不正确\');window.top.right.location.reload();window.top.art.dialog({id:"edit"}).close();</script>';
			exit;
		}
		else{
			$numinfo = D("Goodnum")->getById($_POST["id"]);
			if(!$numinfo){
				echo '<script>alert(\'该靓号不存在\');window.top.right.location.reload();window.top.art.dialog({id:"edit"}).close();</script>';
				exit;
			}
		}

		if($_POST['givetouid'] == ''){
			echo '<script>alert(\'赠送对象UID不能为空\');window.top.right.location.reload();window.top.art.dialog({id:"edit"}).close();</script>';
			exit;
		}
		else{
			$emceeinfo = D("Member")->getById($_POST['givetouid']);
			if($emceeinfo){
				D("Roomnum")->execute('delete from ss_roomnum where num="'.$numinfo['num'].'"');
				D("Roomnum")->execute('insert into ss_roomnum(uid,num,addtime,expiretime,original) values('.$_POST['givetouid'].','.$numinfo['num'].','.time().',0,"n")');
				D("Goodnum")->execute('update ss_goodnum set issale="y",owneruid='.$_POST['givetouid'].',remark="管理员赠送" where id='.$_POST["id"]);
				D("Giveaway")->execute('insert into ss_giveaway(uid,touid,content,remark,objectIcon,addtime) values(0,'.$_POST['givetouid'].',"('.$numinfo['num'].')","系统赠送","/Public/images/gnum.png",'.time().')');
			}
			else{
				echo '<script>alert(\'找不到该赠送对象\');window.top.right.location.reload();window.top.art.dialog({id:"edit"}).close();</script>';
				exit;
			}
		}

		echo '<script>alert(\'赠送成功\');window.top.right.location.reload();window.top.art.dialog({id:"edit"}).close();</script>';

	}

	public function del_goodnum(){
		if($_GET["numid"] == '')
		{
			$this->error('缺少参数或参数不正确');
		}
		else{
			$dao = D("Goodnum");
			$numinfo = $dao->getById($_GET["numid"]);
			if($numinfo){
				if($numinfo['issale'] == 'y'){
					$this->error('该靓号已销售不可删除');
				}
				$dao->where('id='.$_GET["numid"])->delete();
				$this->assign('jumpUrl',base64_decode($_GET['return']));
				$this->success('成功删除');
			}
			else{
				$this->error('找不到该靓号');
			}
		}
	}

	public function opt_goodnum()
	{
		$dao = D("Goodnum");
		switch ($_GET['action']){
			
			case 'del':
				if(is_array($_REQUEST['ids'])){
					$array = $_REQUEST['ids'];
					$num = count($array);
					for($i=0;$i<$num;$i++)
					{
						$numinfo = $dao->getById($array[$i]);
						if($numinfo){
							if($numinfo['issale'] == 'n'){
								$dao->where('id='.$array[$i])->delete();
							}
						}
					}
				}
				$this->assign('jumpUrl',base64_decode($_POST['return']).'#'.time());
				$this->success('操作成功');
				break;
			
		}
	}

	public function recycle_goodnum(){
		if($_GET["numid"] == '')
		{
			$this->error('缺少参数或参数不正确');
		}
		else{
			$dao = D("Goodnum");
			$numinfo = $dao->getById($_GET["numid"]);
			if($numinfo){
				$emceeoldnum = D("Roomnum")->where('uid='.$numinfo['owneruid'].' and original="y"')->select();
				D("Roomnum")->execute('delete from ss_roomnum where num="'.$numinfo['num'].'"');
				$dao->execute('update ss_goodnum set issale="n",owneruid=0,remark="" where id='.$_GET["numid"]);
				D("Member")->execute('update ss_member set curroomnum='.$emceeoldnum[0]['num'].' where id='.$numinfo['owneruid']);

				$this->assign('jumpUrl',base64_decode($_GET['return']));
				$this->success('成功收回');
			}
			else{
				$this->error('找不到该靓号');
			}
		}
	}

	public function add_goodnum(){
		$this->display();
	}

	public function do_add_goodnum(){
		if($_POST['num'] == ''){
			$this->error('靓号不能为空');
		}

		if($_POST['price'] == ''){
			$this->error('价格不能为空');
		}

		$numinfo = D("Goodnum")->where('num='.$_POST['num'])->select();
		if($numinfo){
			$this->error('该靓号已存在');
		}
		
		$goodnum = D('Goodnum');
		$vo = $goodnum->create();
		if(!$vo) {
			$this->error($goodnum->getError());
		}else{
			$goodnum->length = strlen($_POST['num']);
			$goodnum->add();

			$this->assign('jumpUrl',__URL__.'/admin_goodnum/');
			$this->success('添加成功');
		}
	}

	public function add_goodnum_bat(){
		$this->display();
	}

	public function do_add_goodnum_bat(){
		set_time_limit(0);

		header('Content-Type: text/html;charset=utf-8');
		//ignore_user_abort(true);
		ob_end_flush();
		echo '<style>body { font:normal 12px/20px Arial, Verdana, Lucida, Helvetica, simsun, sans-serif; color:#313131; }</style>';
		echo str_pad("",1000);
		echo '准备开始添加...<br>';
		flush();

		for($i=(int)$_POST['startnum'];$i<=(int)$_POST['endnum'];$i++)
		{
			echo '正在添加靓号'.$i.' ';
			$numinfo = D("Goodnum")->where('num='.$i)->select();
			if($numinfo){
				echo '已存在';
			}
			else{
				D("Goodnum")->execute('insert into ss_goodnum(num,length,price,addtime) values('.$i.','.strlen($i).','.$_POST['price'].','.time().')');
				echo '添加成功';
			}
			echo '<br>';
		}
		echo '批量添加完毕';
	}

	public function admin_eggset()
	{
		$eggsetinfo = D("Eggset")->find(1);
		if($eggsetinfo){
			$this->assign('eggsetinfo',$eggsetinfo);
		}
		else{
			$this->assign('jumpUrl',__URL__.'/mainFrame');
			$this->error('系统参数读取错误');
		}
		$this->display();
	}

	public function save_eggset()
	{
		$eggset = D('Eggset');
		$vo = $eggset->create();
		if(!$vo) {
			$this->assign('jumpUrl',__URL__.'/admin_eggset/');
			$this->error('修改失败');
		}else{
			$eggset->save();

			$this->assign('jumpUrl',__URL__.'/admin_eggset/');
			$this->success('修改成功');
		}
	}

	public function admin_eggwinrecord(){
		$condition = 'remark="砸蛋奖励"';
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
		if($_GET['keyword'] != ''){
			$keyuinfo = D("Member")->where('username="'.$_GET['keyword'].'"')->select();
			if($keyuinfo){
				$condition .= ' and touid='.$keyuinfo[0]['id'];
			}
			else{
				$this->error('没有该用户的记录');
			}

			//if(preg_match("/^\d*$/",$_GET['keyword'])){
				//$condition .= ' and touid='.$_GET['keyword'];
			//}
		}

		$orderby = 'id desc';
		$giveaway = D("Giveaway");
		$count = $giveaway->where($condition)->count();
		$listRows = 100;
		$linkFront = '';
		import("@.ORG.Page");
		$p = new Page($count,$listRows,$linkFront);
		$giveaways = $giveaway->limit($p->firstRow.",".$p->listRows)->where($condition)->order($orderby)->select();
		foreach($giveaways as $n=> $val){
			$giveaways[$n]['voo']=D("Member")->where('id='.$val['touid'])->select();
		}
		$p->setConfig('header','条');
		$page = $p->show();
		$this->assign('page',$page);
		$this->assign('giveaways',$giveaways);

		$this->display();
	}
	
	

	//财务
	public function admin_onlinepay()
	{
		$siteconfig = D("Siteconfig")->find(1);
		if($siteconfig){
			$this->assign('siteconfig',$siteconfig);
		}
		else{
			$this->assign('jumpUrl',__URL__.'/mainFrame');
			$this->error('系统参数读取错误');
		}
		$this->display();
	}
	
	/* 环信IM设置  */
	public function admin_huanxin_conf()
	{
		$siteconfig = D("Siteconfig")->find(1);
	
		if($siteconfig){
			$this->assign('siteconfig',$siteconfig);
		}
		else{
			$this->assign('jumpUrl',__URL__.'/mainFrame');
			$this->error('系统参数读取错误');
		}
		$this->display();
	}
	
	
	public function save_onlinepay()
	{
		$siteconfig = D('Siteconfig');
		$vo = $siteconfig->create();
		if(!$vo) {
			$this->assign('jumpUrl',__URL__.'/admin_onlinepay/');
			$this->error('修改失败');
		}else{
			$siteconfig->save();
			$this->assign('jumpUrl',__URL__.'/admin_onlinepay/');
			$this->success('修改成功');
		}
	}
	/* 跟新环信 update */
	
	public function save_huanxin_conf()
	{
		$siteconfig = D('Siteconfig');
		$vo = $siteconfig->create();
		if(!$vo) {
			$this->assign('jumpUrl',__URL__.'/admin_huanxin_conf/');
			$this->error('修改失败');
		}else{
			$siteconfig->save();
			$this->assign('jumpUrl',__URL__.'/admin_huanxin_conf/');
			$this->success('修改成功');
		}
	}
	public function admin_chargerecord(){
		$condition = 'id>0';
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
		if($_GET['keyword'] != '' && $_GET['keyword'] != '请输入用户名或交易号'){
			$keyuinfo = D("Member")->where('username="'.$_GET['keyword'].'"')->select();
			if(preg_match("/^\d*$/",$_GET['keyword'])){
				if($keyuinfo){
					$condition .= ' and (uid='.$keyuinfo[0]['id'].' or orderno="'.$_GET['keyword'].'")';
				}
				else{
					$condition .= ' and orderno="'.$_GET['keyword'].'"';
				}
			}
			else{
				if($keyuinfo){
					$condition .= ' and uid='.$keyuinfo[0]['id'];
				}
				else{
					$this->error('没有该用户的记录');
				}
			}

			//if(preg_match("/^\d*$/",$_GET['keyword'])){
				//$condition .= ' and (uid='.$_GET['keyword'].' or orderno="'.$_GET['keyword'].'")';
			//}
		}
		if($_GET['status'] != ''){
			$condition .= ' and status="'.$_GET['status'].'"';
		}
		$orderby = 'id desc';
		$chargedetail = D("Chargedetail");
		$count = $chargedetail->where($condition)->count();
		$listRows = 100;
		$linkFront = '';
		import("@.ORG.Page");
		$p = new Page($count,$listRows,$linkFront);
		$charges = $chargedetail->limit($p->firstRow.",".$p->listRows)->where($condition)->order($orderby)->select();
		foreach($charges as $n=> $val){
			$charges[$n]['voo']=D("Member")->where('id='.$val['uid'])->select();
			$charges[$n]['voo2']=D("Member")->where('id='.$val['touid'])->select();
			if($val['touid'] != 0){
				$charges[$n]['voo3']=D("Member")->where('id='.$val['proxyuid'])->select();
			}
		}
		$p->setConfig('header','条');
		$page = $p->show();
		$this->assign('page',$page);
		$this->assign('charges',$charges);

		$charges_all = $chargedetail->where($condition)->order($orderby)->select();
		$this->assign('charges_all',$charges_all);

		$this->display();
	}

	public function del_chargerecord(){
		if($_GET["chargeid"] == '')
		{
			$this->error('缺少参数或参数不正确');
		}
		else{
			$dao = D("Chargedetail");
			$chargeinfo = $dao->getById($_GET["chargeid"]);
			if($chargeinfo){
				$dao->where('id='.$_GET["chargeid"])->delete();
				
				$this->assign('jumpUrl',base64_decode($_GET['return']));
				$this->success('成功删除');
			}
			else{
				$this->error('找不到该交易记录');
			}
		}
	}

	public function opt_chargerecord()
	{
		$dao = D("Chargedetail");
		switch ($_GET['action']){
			
			case 'del':
				if(is_array($_REQUEST['ids'])){
					$array = $_REQUEST['ids'];
					$num = count($array);
					for($i=0;$i<$num;$i++)
					{
						$chargeinfo = $dao->getById($array[$i]);
						if($chargeinfo){
							$dao->where('id='.$array[$i])->delete();
							
						}
					}
				}
				$this->assign('jumpUrl',base64_decode($_POST['return']).'#'.time());
				$this->success('操作成功');
				break;
			
		}
	}

	public function addcointouser(){
		$this->display();
	}

	public function do_addcointouser(){
		if($_POST['username'] != ''){
			$userinfo = D("Member")->where('username="'.$_POST['username'].'"')->select();
			if($userinfo){
				if($_POST['math'] == 'plus'){
					D("Member")->execute('update ss_member set coinbalance=coinbalance+'.$_POST['addcoin'].' where id='.$userinfo[0]['id']);

					D("Giveaway")->execute('insert into ss_giveaway(uid,touid,content,remark,objectIcon,addtime,operator,operatorip) values(0,'.$userinfo[0]['id'].',"'.$_POST['addcoin'].'","系统赠送","/Public/images/coin.png",'.time().',"'.$_SESSION['adminname'].'","'.get_client_ip().'")');
				}
				if($_POST['math'] == 'subtract'){
					D("Member")->execute('update ss_member set coinbalance=coinbalance-'.$_POST['addcoin'].' where id='.$userinfo[0]['id']);

					D("Giveaway")->execute('insert into ss_giveaway(uid,touid,content,remark,objectIcon,addtime,operator,operatorip) values(0,'.$userinfo[0]['id'].',"-'.$_POST['addcoin'].'","系统抵扣","/Public/images/coin.png",'.time().',"'.$_SESSION['adminname'].'","'.get_client_ip().'")');
				}
				$this->assign('jumpUrl',__URL__.'/addcointouser/');
				$this->success('操作成功');
			}
			else{
				$this->error('未找到该用户');
			}
		}
		else{
			$this->error('请填写相关选项');
		}
	}

	public function admin_coindetail(){
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
		if($_GET['keyword'] != '' && $_GET['keyword'] != '请输入用户名'){
			$keyuinfo = D("Member")->where('username="'.$_GET['keyword'].'"')->select();
			if($keyuinfo){
				$condition .= ' and uid='.$keyuinfo[0]['id'];
			}
			else{
				$this->error('没有该用户的记录');
			}

			//if(preg_match("/^\d*$/",$_GET['keyword'])){
				//$condition .= ' and uid='.$_GET['keyword'];
			//}
		}
		if($_GET['keyword2'] != ''  && $_GET['keyword2'] != '请输入用户名'){
			$keyuinfo2 = D("Member")->where('username="'.$_GET['keyword2'].'"')->select();
			if($keyuinfo2){
				$condition .= ' and touid='.$keyuinfo2[0]['id'];
			}
			else{
				$this->error('没有该对象的记录');
			}

			//if(preg_match("/^\d*$/",$_GET['keyword2'])){
				//$condition .= ' and touid='.$_GET['keyword2'];
			//}
		}
		$orderby = 'id desc';
		$coindetail = D("Coindetail");
		$count = $coindetail->where($condition)->count();
		$listRows = 100;
		$linkFront = '';
		import("@.ORG.Page");
		$p = new Page($count,$listRows,$linkFront);
		$details = $coindetail->limit($p->firstRow.",".$p->listRows)->where($condition)->order($orderby)->select();
		foreach($details as $n=> $val){
			$details[$n]['voo']=D("Member")->where('id='.$val['uid'])->select();
			if($val['touid'] != 0){
				$details[$n]['voo2']=D("Member")->where('id='.$val['touid'])->select();
			}
		}
		$p->setConfig('header','条');
		$page = $p->show();
		$this->assign('page',$page);
		$this->assign('details',$details);

		$this->display();
	}

	public function del_coindetail(){
		if($_GET["detailid"] == '')
		{
			$this->error('缺少参数或参数不正确');
		}
		else{
			$dao = D("Coindetail");
			$detailinfo = $dao->getById($_GET["detailid"]);
			if($detailinfo){
				$dao->where('id='.$_GET["detailid"])->delete();
				
				$this->assign('jumpUrl',base64_decode($_GET['return']));
				$this->success('成功删除');
			}
			else{
				$this->error('找不到该消费记录');
			}
		}
	}

	public function opt_coindetail()
	{
		$dao = D("Coindetail");
		switch ($_GET['action']){
			
			case 'del':
				if(is_array($_REQUEST['ids'])){
					$array = $_REQUEST['ids'];
					$num = count($array);
					for($i=0;$i<$num;$i++)
					{
						$detailinfo = $dao->getById($array[$i]);
						if($detailinfo){
							$dao->where('id='.$array[$i])->delete();
							
						}
					}
				}
				$this->assign('jumpUrl',base64_decode($_POST['return']).'#'.time());
				$this->success('操作成功');
				break;
			
		}
	}

	public function admin_adminaddcoinrecord(){
		$condition = 'uid=0 and objectIcon="/Public/images/coin.png" and remark="系统赠送"';
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
		if($_GET['keyword'] != ''){
			$keyuinfo = D("Member")->where('username="'.$_GET['keyword'].'"')->select();
			if($keyuinfo){
				$condition .= ' and touid='.$keyuinfo[0]['id'];
			}
			else{
				$this->error('没有该用户的记录');
			}

			//if(preg_match("/^\d*$/",$_GET['keyword'])){
				//$condition .= ' and touid='.$_GET['keyword'];
			//}
		}

		$orderby = 'id desc';
		$giveaway = D("Giveaway");
		$count = $giveaway->where($condition)->count();
		$listRows = 100;
		$linkFront = '';
		import("@.ORG.Page");
		$p = new Page($count,$listRows,$linkFront);
		$giveaways = $giveaway->limit($p->firstRow.",".$p->listRows)->where($condition)->order($orderby)->select();
		foreach($giveaways as $n=> $val){
			$giveaways[$n]['voo']=D("Member")->where('id='.$val['touid'])->select();
		}
		$p->setConfig('header','条');
		$page = $p->show();
		$this->assign('page',$page);
		$this->assign('giveaways',$giveaways);

		$this->display();
	}

	public function admin_beandetail(){
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
		if($_GET['keyword'] != '' && $_GET['keyword'] != '请输入用户名'){
			$keyuinfo = D("Member")->where('username="'.$_GET['keyword'].'"')->select();
			if($keyuinfo){
				$condition .= ' and uid='.$keyuinfo[0]['id'];
			}
			else{
				$this->error('没有该用户的记录');
			}

			//if(preg_match("/^\d*$/",$_GET['keyword'])){
				//$condition .= ' and uid='.$_GET['keyword'];
			//}
		}
		$orderby = 'id desc';
		$beandetail = D("Beandetail");
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

	public function del_beandetail(){
		if($_GET["detailid"] == '')
		{
			$this->error('缺少参数或参数不正确');
		}
		else{
			$dao = D("Beandetail");
			$detailinfo = $dao->getById($_GET["detailid"]);
			if($detailinfo){
				$dao->where('id='.$_GET["detailid"])->delete();
				
				$this->assign('jumpUrl',base64_decode($_GET['return']));
				$this->success('成功删除');
			}
			else{
				$this->error('找不到该记录');
			}
		}
	}

	public function opt_beandetail()
	{
		$dao = D("Beandetail");
		switch ($_GET['action']){
			
			case 'del':
				if(is_array($_REQUEST['ids'])){
					$array = $_REQUEST['ids'];
					$num = count($array);
					for($i=0;$i<$num;$i++)
					{
						$detailinfo = $dao->getById($array[$i]);
						if($detailinfo){
							$dao->where('id='.$array[$i])->delete();
							
						}
					}
				}
				$this->assign('jumpUrl',base64_decode($_POST['return']).'#'.time());
				$this->success('操作成功');
				break;
			
		}
	}
  //原主播收入统计函数
	public function count_emceeincome(){
		set_time_limit(0);

		header('Content-Type: text/html;charset=utf-8');
		//ignore_user_abort(true);
		ob_end_flush();
		echo '<style>body { font:normal 12px/20px Arial, Verdana, Lucida, Helvetica, simsun, sans-serif; color:#313131; }</style>';
		echo str_pad("",1000);
		echo '准备开始统计...<br>';
		flush();

		$emcces = D("Member")->where('sign="y"')->order('regtime desc')->select();
		echo '共有'.count($emcces).'个签约主播<br>';
		foreach($emcces as $n=> $val){
			if ( connection_aborted() )
			{
				exit;
			}
			echo '正在统计主播 '.$val['nickname'].'<br>';
			if($val['freezestatus'] == '1'){
				if(($val['beanbalance'] - $val['freezeincome']) > 0){
					D("Member")->execute('update ss_member set freezeincome=0,freezestatus="0" where id='.$val['id']);
				}
			}
			if(($val['beanbalance'] - $val['freezeincome']) > 0){
				$costbean = $val['beanbalance'] - $val['freezeincome'];
					
				D("Member")->execute('update ss_member set beanbalance=beanbalance-'.$costbean.' where id='.$val['id']);

				$Beandetail = D("Beandetail");
				$Beandetail->create();
				$Beandetail->type = 'expend';
				$Beandetail->action = 'settlement';
				$Beandetail->uid = $val['id'];
				$Beandetail->content = '系统结算';
				$Beandetail->bean = $costbean;
				$Beandetail->addtime = time();
				$detailId = $Beandetail->add();
			}
		}
		echo '<a href="'.__URL__.'/admin_emccepayrecord/">返回</a>';
	}

	public function admin_emccepayrecord(){
		$condition = 'type="expend" and action="settlement"';
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
		if($_GET['keyword'] != '' && $_GET['keyword'] != '请输入用户名'){
			$keyuinfo = D("Member")->where('username="'.$_GET['keyword'].'"')->select();
			if($keyuinfo){
				$condition .= ' and uid='.$keyuinfo[0]['id'];
			}
			else{
				$this->error('没有该用户的记录');
			}

			//if(preg_match("/^\d*$/",$_GET['keyword'])){
				//$condition .= ' and uid='.$_GET['keyword'];
			//}
		}
		$orderby = 'id desc';
		$beandetail = D("Beandetail");
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

	public function del_emccepayrecord(){
		if($_GET["recordid"] == '')
		{
			$this->error('缺少参数或参数不正确');
		}
		else{
			$dao = D("Beandetail");
			$detailinfo = $dao->getById($_GET["recordid"]);
			if($detailinfo){
				$dao->where('id='.$_GET["recordid"])->delete();
				
				$this->assign('jumpUrl',base64_decode($_GET['return']));
				$this->success('成功删除');
			}
			else{
				$this->error('找不到该记录');
			}
		}
	}

	public function opt_emccepayrecord()
	{
		$dao = D("Beandetail");
		switch ($_GET['action']){
			
			case 'del':
				if(is_array($_REQUEST['ids'])){
					$array = $_REQUEST['ids'];
					$num = count($array);
					for($i=0;$i<$num;$i++)
					{
						$detailinfo = $dao->getById($array[$i]);
						if($detailinfo){
							$dao->where('id='.$array[$i])->delete();
							
						}
					}
				}
				$this->assign('jumpUrl',base64_decode($_POST['return']).'#'.time());
				$this->success('操作成功');
				break;
			
		}
	}

	public function edit_emccepayrecord(){
		header("Content-type: text/html; charset=utf-8"); 
		if($_GET['recordid'] == ''){
			echo '<script>alert(\'参数错误\');window.top.right.location.reload();window.top.art.dialog({id:"edit"}).close();</script>';
		}
		else{
			$recordinfo = D("Beandetail")->find($_GET["recordid"]);
			if($recordinfo){
				$this->assign('recordinfo',$recordinfo);
				$userinfo = D("Member")->find($recordinfo["uid"]);
				$this->assign('userinfo',$userinfo);
			}
			else{
				echo '<script>alert(\'找不到该记录\');window.top.right.location.reload();window.top.art.dialog({id:"edit"}).close();</script>';
			}
		}
		
		$this->display();
	}

	public function do_edit_emccepayrecord(){
		header("Content-type: text/html; charset=utf-8"); 
		$beandetail = D('Beandetail');
		$vo = $beandetail->create();
		if(!$vo) {
			echo '<script>alert(\''.$admin->getError().'\');window.top.art.dialog({id:"edit"}).close();</script>';
		}else{
			$beandetail->save();

			echo '<script>alert(\'修改成功\');window.top.right.location.reload();window.top.art.dialog({id:"edit"}).close();</script>';
		}
	}

	public function admin_emceeagentbeandetail(){
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
		if($_GET['keyword'] != '' && $_GET['keyword'] != '请输入用户名'){
			$keyuinfo = D("Member")->where('username="'.$_GET['keyword'].'"')->select();
			if($keyuinfo){
				$condition .= ' and uid='.$keyuinfo[0]['id'];
			}
			else{
				$this->error('没有该用户的记录');
			}

			//if(preg_match("/^\d*$/",$_GET['keyword'])){
				//$condition .= ' and uid='.$_GET['keyword'];
			//}
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

	public function del_emceeagentbeandetail(){
		if($_GET["detailid"] == '')
		{
			$this->error('缺少参数或参数不正确');
		}
		else{
			$dao = D("Emceeagentbeandetail");
			$detailinfo = $dao->getById($_GET["detailid"]);
			if($detailinfo){
				$dao->where('id='.$_GET["detailid"])->delete();
				
				$this->assign('jumpUrl',base64_decode($_GET['return']));
				$this->success('成功删除');
			}
			else{
				$this->error('找不到该记录');
			}
		}
	}

	public function opt_emceeagentbeandetail()
	{
		$dao = D("Emceeagentbeandetail");
		switch ($_GET['action']){
			
			case 'del':
				if(is_array($_REQUEST['ids'])){
					$array = $_REQUEST['ids'];
					$num = count($array);
					for($i=0;$i<$num;$i++)
					{
						$detailinfo = $dao->getById($array[$i]);
						if($detailinfo){
							$dao->where('id='.$array[$i])->delete();
							
						}
					}
				}
				$this->assign('jumpUrl',base64_decode($_POST['return']).'#'.time());
				$this->success('操作成功');
				break;
			
		}
	}

	public function count_emceeagentincome(){
		set_time_limit(0);

		header('Content-Type: text/html;charset=utf-8');
		//ignore_user_abort(true);
		ob_end_flush();
		echo '<style>body { font:normal 12px/20px Arial, Verdana, Lucida, Helvetica, simsun, sans-serif; color:#313131; }</style>';
		echo str_pad("",1000);
		echo '准备开始统计...<br>';
		flush();

		$emcces = D("Member")->where('emceeagent="y"')->order('regtime desc')->select();
		echo '共有'.count($emcces).'个主播代理<br>';
		foreach($emcces as $n=> $val){
			if ( connection_aborted() )
			{
				exit;
			}
			echo '正在统计主播代理 '.$val['nickname'].'<br>';
			//if($val['freezestatus'] == '1'){
				//if(($val['beanbalance'] - $val['freezeincome']) > 0){
					//D("Member")->execute('update ss_member set freezeincome=0,freezestatus="0" where id='.$val['id']);
				//}
			//}
			//if(($val['beanbalance'] - $val['freezeincome']) > 0){
				//$costbean = $val['beanbalance'] - $val['freezeincome'];
			if($val['beanbalance2'] > 0){
				$costbean = $val['beanbalance2'];
					
				D("Member")->execute('update ss_member set beanbalance2=beanbalance2-'.$costbean.' where id='.$val['id']);

				$Beandetail = D("Emceeagentbeandetail");
				$Beandetail->create();
				$Beandetail->type = 'expend';
				$Beandetail->action = 'settlement';
				$Beandetail->uid = $val['id'];
				$Beandetail->content = '系统结算';
				$Beandetail->bean = $costbean;
				$Beandetail->addtime = time();
				$detailId = $Beandetail->add();
			}
		}
		echo '<a href="'.__URL__.'/admin_emcceagentpayrecord/">返回</a>';
	}

	public function admin_emcceagentpayrecord(){
		$condition = 'type="expend" and action="settlement"';
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
		if($_GET['keyword'] != '' && $_GET['keyword'] != '请输入用户名'){
			$keyuinfo = D("Member")->where('username="'.$_GET['keyword'].'"')->select();
			if($keyuinfo){
				$condition .= ' and uid='.$keyuinfo[0]['id'];
			}
			else{
				$this->error('没有该用户的记录');
			}

			//if(preg_match("/^\d*$/",$_GET['keyword'])){
				//$condition .= ' and uid='.$_GET['keyword'];
			//}
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

	public function del_emcceagentpayrecord(){
		if($_GET["recordid"] == '')
		{
			$this->error('缺少参数或参数不正确');
		}
		else{
			$dao = D("Emceeagentbeandetail");
			$detailinfo = $dao->getById($_GET["recordid"]);
			if($detailinfo){
				$dao->where('id='.$_GET["recordid"])->delete();
				
				$this->assign('jumpUrl',base64_decode($_GET['return']));
				$this->success('成功删除');
			}
			else{
				$this->error('找不到该记录');
			}
		}
	}

	public function opt_emcceagentpayrecord()
	{
		$dao = D("Emceeagentbeandetail");
		switch ($_GET['action']){
			
			case 'del':
				if(is_array($_REQUEST['ids'])){
					$array = $_REQUEST['ids'];
					$num = count($array);
					for($i=0;$i<$num;$i++)
					{
						$detailinfo = $dao->getById($array[$i]);
						if($detailinfo){
							$dao->where('id='.$array[$i])->delete();
							
						}
					}
				}
				$this->assign('jumpUrl',base64_decode($_POST['return']).'#'.time());
				$this->success('操作成功');
				break;
			
		}
	}

	public function edit_emcceagentpayrecord(){
		header("Content-type: text/html; charset=utf-8"); 
		if($_GET['recordid'] == ''){
			echo '<script>alert(\'参数错误\');window.top.right.location.reload();window.top.art.dialog({id:"edit"}).close();</script>';
		}
		else{
			$recordinfo = D("Emceeagentbeandetail")->find($_GET["recordid"]);
			if($recordinfo){
				$this->assign('recordinfo',$recordinfo);
				$userinfo = D("Member")->find($recordinfo["uid"]);
				$this->assign('userinfo',$userinfo);
			}
			else{
				echo '<script>alert(\'找不到该记录\');window.top.right.location.reload();window.top.art.dialog({id:"edit"}).close();</script>';
			}
		}
		
		$this->display();
	}

	public function do_edit_emcceagentpayrecord(){
		header("Content-type: text/html; charset=utf-8"); 
		$beandetail = D('Emceeagentbeandetail');
		$vo = $beandetail->create();
		if(!$vo) {
			echo '<script>alert(\''.$admin->getError().'\');window.top.art.dialog({id:"edit"}).close();</script>';
		}else{
			$beandetail->save();

			echo '<script>alert(\'修改成功\');window.top.right.location.reload();window.top.art.dialog({id:"edit"}).close();</script>';
		}
	}

	public function admin_payagentbeandetail(){
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
		if($_GET['keyword'] != '' && $_GET['keyword'] != '请输入用户名'){
			$keyuinfo = D("Member")->where('username="'.$_GET['keyword'].'"')->select();
			if($keyuinfo){
				$condition .= ' and uid='.$keyuinfo[0]['id'];
			}
			else{
				$this->error('没有该用户的记录');
			}

			//if(preg_match("/^\d*$/",$_GET['keyword'])){
				//$condition .= ' and uid='.$_GET['keyword'];
			//}
		}
		$orderby = 'id desc';
		$beandetail = D("Payagentbeandetail");
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

	public function del_payagentbeandetail(){
		if($_GET["detailid"] == '')
		{
			$this->error('缺少参数或参数不正确');
		}
		else{
			$dao = D("Payagentbeandetail");
			$detailinfo = $dao->getById($_GET["detailid"]);
			if($detailinfo){
				$dao->where('id='.$_GET["detailid"])->delete();
				
				$this->assign('jumpUrl',base64_decode($_GET['return']));
				$this->success('成功删除');
			}
			else{
				$this->error('找不到该记录');
			}
		}
	}

	public function opt_payagentbeandetail()
	{
		$dao = D("Payagentbeandetail");
		switch ($_GET['action']){
			
			case 'del':
				if(is_array($_REQUEST['ids'])){
					$array = $_REQUEST['ids'];
					$num = count($array);
					for($i=0;$i<$num;$i++)
					{
						$detailinfo = $dao->getById($array[$i]);
						if($detailinfo){
							$dao->where('id='.$array[$i])->delete();
							
						}
					}
				}
				$this->assign('jumpUrl',base64_decode($_POST['return']).'#'.time());
				$this->success('操作成功');
				break;
			
		}
	}
    //统计充值代理收入
	public function count_payagentincome(){
		set_time_limit(0);

		header('Content-Type: text/html;charset=utf-8');
		//ignore_user_abort(true);
		ob_end_flush();
		echo '<style>body { font:normal 12px/20px Arial, Verdana, Lucida, Helvetica, simsun, sans-serif; color:#313131; }</style>';
		echo str_pad("",1000);
		echo '准备开始统计...<br>';
		flush();

		$emcces = D("Member")->where('payagent="y"')->order('regtime desc')->select();
		echo '共有'.count($emcces).'个充值代理<br>';
		foreach($emcces as $n=> $val){
			if ( connection_aborted() )
			{
				exit;
			}
			echo '正在统计充值代理 '.$val['nickname'].'<br>';
			//if($val['freezestatus'] == '1'){
				//if(($val['beanbalance'] - $val['freezeincome']) > 0){
					//D("Member")->execute('update ss_member set freezeincome=0,freezestatus="0" where id='.$val['id']);
				//}
			//}
			//if(($val['beanbalance'] - $val['freezeincome']) > 0){
				//$costbean = $val['beanbalance'] - $val['freezeincome'];
			if($val['beanbalance3'] > 0){
				$costbean = $val['beanbalance3'];
					
				D("Member")->execute('update ss_member set beanbalance3=beanbalance3-'.$costbean.' where id='.$val['id']);

				$Beandetail = D("Payagentbeandetail");
				$Beandetail->create();
				$Beandetail->type = 'expend';
				$Beandetail->action = 'settlement';
				$Beandetail->uid = $val['id'];
				$Beandetail->content = '系统结算';
				$Beandetail->bean = $costbean;
				$Beandetail->addtime = time();
				$detailId = $Beandetail->add();
			}
		}
		echo '<a href="'.__URL__.'/admin_payagentpayrecord/">返回</a>';
	}
    public function admin_payagentincome(){
    	$sql="select uid,sum(bean) as bean from ss_payagentbeandetail group by uid";
		$data=M()->query($sql);
		foreach($data as $k=>$v){
			$userinfo=M('Member')->field('username')->where('id='.$v['uid'].' and payagent="y"')->find();
			$data[$k]['username']=$userinfo['username'];
		}
		$this->assign('data',$data);
		$this->display();
		
    }

	public function admin_payagentpayrecord(){
		$condition = 'type="expend" and action="settlement"';
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
		if($_GET['keyword'] != '' && $_GET['keyword'] != '请输入用户名'){
			$keyuinfo = D("Member")->where('username="'.$_GET['keyword'].'"')->select();
			if($keyuinfo){
				$condition .= ' and uid='.$keyuinfo[0]['id'];
			}
			else{
				$this->error('没有该用户的记录');
			}

			//if(preg_match("/^\d*$/",$_GET['keyword'])){
				//$condition .= ' and uid='.$_GET['keyword'];
			//}
		}
		$orderby = 'id desc';
		$beandetail = D("Payagentbeandetail");
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

	public function del_payagentpayrecord(){
		if($_GET["recordid"] == '')
		{
			$this->error('缺少参数或参数不正确');
		}
		else{
			$dao = D("Payagentbeandetail");
			$detailinfo = $dao->getById($_GET["recordid"]);
			if($detailinfo){
				$dao->where('id='.$_GET["recordid"])->delete();
				
				$this->assign('jumpUrl',base64_decode($_GET['return']));
				$this->success('成功删除');
			}
			else{
				$this->error('找不到该记录');
			}
		}
	}

	public function opt_payagentpayrecord()
	{
		$dao = D("Payagentbeandetail");
		switch ($_GET['action']){
			
			case 'del':
				if(is_array($_REQUEST['ids'])){
					$array = $_REQUEST['ids'];
					$num = count($array);
					for($i=0;$i<$num;$i++)
					{
						$detailinfo = $dao->getById($array[$i]);
						if($detailinfo){
							$dao->where('id='.$array[$i])->delete();
							
						}
					}
				}
				$this->assign('jumpUrl',base64_decode($_POST['return']).'#'.time());
				$this->success('操作成功');
				break;
			
		}
	}

	public function edit_payagentpayrecord(){
		header("Content-type: text/html; charset=utf-8"); 
		if($_GET['recordid'] == ''){
			echo '<script>alert(\'参数错误\');window.top.right.location.reload();window.top.art.dialog({id:"edit"}).close();</script>';
		}
		else{
			$recordinfo = D("Payagentbeandetail")->find($_GET["recordid"]);
			if($recordinfo){
				$this->assign('recordinfo',$recordinfo);
				$userinfo = D("Member")->find($recordinfo["uid"]);
				$this->assign('userinfo',$userinfo);
			}
			else{
				echo '<script>alert(\'找不到该记录\');window.top.right.location.reload();window.top.art.dialog({id:"edit"}).close();</script>';
			}
		}
		
		$this->display();
	}

	public function do_edit_payagentpayrecord(){
		header("Content-type: text/html; charset=utf-8"); 
		$beandetail = D('Payagentbeandetail');
		$vo = $beandetail->create();
		if(!$vo) {
			echo '<script>alert(\''.$admin->getError().'\');window.top.art.dialog({id:"edit"}).close();</script>';
		}else{
			$beandetail->save();

			echo '<script>alert(\'修改成功\');window.top.right.location.reload();window.top.art.dialog({id:"edit"}).close();</script>';
		}
	}



	//界面
	public function admin_template()
	{
		$this->display();
	}

	public function tpl_updatefilename() {
		$filepath = '../'.$_POST['style'].'/config.php';
		if (file_exists($filepath)) {
			$style_info = include $filepath;
		}

		$file_explan = isset($_POST['file_explan']) ? $_POST['file_explan'] : '';
		if (!isset($style_info['file_explan'])) $style_info['file_explan'] = array();
		$style_info['file_explan'] = array_merge($style_info['file_explan'], $file_explan);
		@file_put_contents($filepath, '<?php return '.var_export($style_info, true).';?>');
		$this->success('修改成功');
	}

	public function admin_dirlist()
	{
		$this->display();
	}

	public function admin_dirlist2()
	{
		$this->display();
	}

	public function edit_file(){
		$basedir = realpath("../");
		$fp=fopen($basedir.base64_decode($_GET['file']),"r");
		$contents=fread($fp,filesize($basedir.base64_decode($_GET['file'])));
		$contents = str_replace('</textarea>', '&lt;/textarea>', $contents);
		$this->assign('contents',$contents);
		$this->display();
	}

	public function do_edit_file(){
		$basedir = realpath("../");
		$fp=fopen($basedir.$_POST['file'],"wb");
		$contents = str_replace('&lt;/textarea>', '</textarea>', $_POST['str']);
		fputs($fp,stripslashes($contents));
		fclose($fp);
		$this->assign('jumpUrl',__URL__."/admin_dirlist2/?action=chdr&file=".base64_encode($_POST['wdir']));
		$this->success('保存成功');
	}
	
	//数据库操作
	public function admin_database(){
		$model = new Model();
		$li = $model->query("show table status");
		//dump($li);
		$count_free_data = 0;
		$count_data = 0;
		$j = 0;
		for($i=0;$i<count($li);$i++){
			if(preg_match("/^ss_+[a-zA-Z0-9_-]+$/",$li[$i]['Name'])){
				$li[$i]['Data_length']+=$li[$i]['Index_length'];
				$li[$i]['Data_length']=round(floatval($li[$i]['Data_length']/1024),2);
				$count_free_data+=$li[$i]['Data_free'];
				$count_data+=$li[$i]['Data_length'];
				$list[$j]->Name=$li[$i]['Name'];
				$list[$j]->Rows=$li[$i]['Rows'];
				$list[$j]->Data_length=$li[$i]['Data_length'];
				$list[$j]->Data_free=$li[$i]['Data_free'];
				$j++;
			}
		}
		$this->assign("list",$list);
		$this->assign("count_free_data",$count_free_data);
		$this->assign("count_data",$count_data);
		$this->display();
	}

	public function repair_table(){
		if(!empty($_GET['name'])) {
			$model=new Model();
			$list=$model->query("repair table ".$_GET['name']);
			if($list!==false){
				$this->assign('jumpUrl',__URL__."/admin_database/");
				$this->success('修复成功');
			}
			else{
				$this->assign('jumpUrl',__URL__."/admin_database/");
				$this->error('修复失败'); 
			}
		}
		else{
			$this->error('参数错误！');
		}
	}

	public function optimize_table(){
		if(!empty($_GET['name'])) {
			$model=new Model();
			$list=$model->query("optimize table ".$_GET['name']);
			if($list!==false){
				$this->assign('jumpUrl',__URL__."/admin_database/");
				$this->success('优化成功');
			}
			else{
				$this->assign('jumpUrl',__URL__."/admin_database/");
				$this->error('优化失败');
			}
		}else{
			$this->error('参数错误！');
		}
	}

	public function exec_sql(){
		if(!empty($_POST['sqlquery'])) {
			$model=new Model();
			$list=$model->query($_POST['sqlquery']);
			if($list!==false){
				$this->assign('jumpUrl',__URL__."/admin_database/");
				$this->success('sql语句成功执行了');
			}
			else{
				$this->assign('jumpUrl',__URL__."/admin_database/");
				$this->error('sql语句执行失败');
			}
		}else{
			$this->error('SQL语句不能为空！');
		}
	}

	public function backup_database(){
		$model = new Model();
		$li = $model->query("show table status");
		$j = 0;
		for($i=0;$i<count($li);$i++){
			if(preg_match("/^ss_+[a-zA-Z0-9_-]+$/",$li[$i]['Name'])){
				$list[$j]->Name=$li[$i]['Name'];
				$j++;
			}
		}
		$this->assign("list",$list);
		
		$this->display();
	}

	public function restore_database(){
		$this->display();
	}

	public function admin_redbagset()
	{
		$redbagsetinfo = D("Siteconfig")->find(1);
		if($redbagsetinfo){
			$this->assign('redbagsetinfo',$redbagsetinfo);
		}
		else{
			$this->assign('jumpUrl',__URL__.'/mainFrame');
			$this->error('系统参数读取错误');
		}
		$this->display();
	}

	public function save_redbagset()
	{
		$redbagset = D('Siteconfig');
		$vo = $redbagset->create();
		if(!$vo) {
			$this->assign('jumpUrl',__URL__.'/admin_redbagset/');
			$this->error('修改失败');
		}else{
			$redbagset->save();

			$this->assign('jumpUrl',__URL__.'/admin_redbagset/');
			$this->success('修改成功');
		}
	}
    //统计主播收入wp写
	public function admin_statisticsanchor(){
		$sql="select uid,sum(bean) as bean from ss_beandetail group by uid";
		$data=M()->query($sql);
		foreach($data as $k=>$v){
			$userinfo=M('Member')->field('username')->where('id='.$v['uid'])->find();
			$data[$k]['username']=$userinfo['username'];
		}
		$this->assign('data',$data);
		$this->display();
	}
	public function admin_countagentanchor(){
		$sql="select uid,sum(bean) as bean from ss_Emceeagentbeandetail group by uid";
		$data=M()->query($sql);
		foreach($data as $k=>$v){
			$userinfo=M('Member')->field('username')->where('id='.$v['uid'])->find();
			$data[$k]['username']=$userinfo['username'];
		}
		$this->assign('data',$data);
		$this->display();
		
	}
}