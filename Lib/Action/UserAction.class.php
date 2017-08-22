<?php
/* 松松重构版 */
class UserAction extends BaseAction {
	//我的家族管理
	public function del_sqmyfamily(){
		$sqid=$_GET['sqid'];
	
		$uid=M("sqjoinfamily")->where("id=".$sqid)->getField("uid");

	
		$res=M("sqjoinfamily")->where("id=".$sqid)->delete();
		if($res){
			$mmodel=M("member");
			$mmodel->id=$uid;
			$mmodel->agentuid=0;
			if($mmodel->save()){
				$this->success("删除成功");
			}else{
				$this->error("删除失败");
			}
		}else{
			$this->error("删除失败");
		}
	}
	
	
	
	public function edit_sqmyfamily(){
		$sqid=$_GET['sqid'];
		
		//根据申请id 得到申请用户的相关信息
		$sqinfo=M("sqjoinfamily")->where("id=".$sqid)->select();
		$userid=$sqinfo[0]['uid'];
		
		$zhuangtai=$sqinfo[0]['zhuangtai'];
		if($zhuangtai==0){
			$dqzhuangtai="未审核";
		}elseif($zhuangtai==1){
			$dqzhuangtai="已通过";
		}elseif($zhuangtai==2){
			$dqzhuangtai="未通过";
		}
		$userinfo=M("member")->where("id=".$userid)->select();
	   $emceelevel = getEmceelevel($userinfo[0]['earnbean']);
	   $userinfo[0]["emceelevel"]=$emceelevel;
	   
	   
	   $this->assign("dqzhuangtai",$dqzhuangtai);
	   $this->assign("userinfo",$userinfo);
	   $this->assign("sqinfo",$sqinfo);
	   //接收提交信息更改状态
	   if(!empty($_POST)){
	   	$agentuid=$_SESSION['uid'];
		  var_dump($agentuid);
	   	$squid=$_POST['uid'];
	   	$sqid=$_POST['id'];
		$newzhuangtai=$_POST['zhuangtai'];
		
		$sqmodel=M("sqjoinfamily");
		$mmodel=M("member");
		$sqmodel->id=$sqid;
		$sqmodel->shtime=time();
		$sqmodel->zhuangtai=$newzhuangtai;
		if($sqmodel->save()){
			$mmodel->id=$squid;
			if($newzhuangtai=='1'){
				$mmodel->agentuid=	$agentuid;
			}else{
				$mmodel->agentuid=0;
			}
			if($mmodel->save()){
				$this->success("更新成功");
			}else{
				$this->error("更新失败");
			}
		
			
			
		}else{
			$this->error("更新失败");
		}
		
		
		
		
		   
	   }
	  
	   $this->display();
		
		
	}	
		
		
		
	//我的家族管理
	public function  sqmyfamily(){
		$agentid=$_SESSION['uid'];
	
		$count=M("sqjoinfamily")->where("familyid=".$agentid)->count();
 
		
		//带分页关联用户信息
		import("@.ORG.Page");
		$p = new Page($count,20);
		$p->setConfig('header','条');
		$page = $p->show();
		$fix= C('DB_PREFIX');
		$field="m.nickname,m.curroomnum,m.earnbean,sq.*";
		$res = M('sqjoinfamily sq')->field($field)->join("{$fix}member m ON m.id=sq.uid")->where("familyid=".$agentid)->limit($p->firstRow.",".$p->listRows)->select();
	
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
	
	
	//我的家族成员列表
	public  function  myfamilyemcee(){
		$condition = 'agentuid='.$_SESSION['uid'];
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
	//更换我的家族封面
	public function myfamilyimg(){
		$uid=$_SESSION['uid'];
		$res=M("agentfamily")->where("uid='$uid' && zhuangtai='已通过'")->select();
		//var_dump($res);
		$this->assign("jzinfo",$res);
		//var_dump($_POST);
		if(!empty($_POST)){
			import("ORG.Net.UploadFile");  
            //实例化上传类  
            $upload = new UploadFile(); 
            $upload->maxSize = 3145728;  
            //设置文件上传类型  
            $upload->allowExts = array('jpg','gif','png','jpeg');  
            //设置文件上传位置  
            $upload->savePath = "./Public/Familyimg/";//这里说明一下，由于ThinkPHP是有入口文件的，所以这里的./Public是指网站根目录下的Public文件夹  
            //设置文件上传名(按照时间)  
            $upload->saveRule = "time";  
            if (!$upload->upload()){  
                $this->error($upload->getErrorMsg());  
            }else{  
                //上传成功，获取上传信息  
                $info = $upload->getUploadFileInfo(); 
            }
          $savename = $info[0]['savename'];
		  	$model=M("agentfamily"); 
		  if($model->create()){
			$model->id=$_POST['id'];
			$model->familyimg=$savename;
			if($model->save()){
				$this->success("封面更新成功！");
			}else{
				$this->error("封面更新失败！");
			}
		}else{
			$this->error($model->getError());
		}
		      
		  
		}
	

		
	
		
		
		$this->display();
	}
	
	 public function do_myfamily_edit(){
	 	$model=M("agentfamily");

		
	
		if($model->create()){
			$model->id=$_POST['id'];
			$model->familyname=$_POST['familyname'];
			$model->familyinfo=$_POST['familyinfo'];
			if($model->save()){
				$this->success("资料更新成功！");
			}else{
				$this->error("资料更新失败！");
			}
		}else{
			$this->error($model->getError());
		}
       
	 }
	
	  public function myfamily(){
	  	$uid=$_SESSION['uid'];
		$res=M("agentfamily")->where("uid='$uid' && zhuangtai='已通过'")->select();
		
		$this->assign("jzinfo",$res);
		
		$this->display();
	}
	public function getroominfo(){
		C('HTML_CACHE_ON',false);
		header('Content-Type: text/xml');
		$roominfo = D("Member")->where('curroomnum='.$_GET["roomnum"].'')->select();
		if($roominfo){
			if($roominfo[0]['fakeuser'] == 'y'){
				$body = file_get_contents('http://xiu.56.com/api/userFlvApi.php?room_user_id='.$roominfo[0]['56_room_user_id']);
				if(strstr($body,"status=1")){
					echo '<?xml version="1.0" encoding="UTF-8"?>';
					echo '<ROOT>';
					echo '<broadcasting>yy</broadcasting>';
					$bodyArray = explode("%3D",$body);
					$bodyArray2 = explode("&",$bodyArray[1]);
					$token = $bodyArray2[0];
					echo '<token>'.$token.'</token>';
					echo '</ROOT>';
				}
				else{ 
					echo '<?xml version="1.0" encoding="UTF-8"?>';
					echo '<ROOT>';
					echo '<broadcasting>n</broadcasting>';
					echo '<offlinevideo></offlinevideo>';
					echo '</ROOT>';
				}
			}
			else{
				echo '<?xml version="1.0" encoding="UTF-8"?>';
				echo '<ROOT>';
				echo '<broadcasting>'.$roominfo[0]['broadcasting'].'</broadcasting>';
				if($roominfo[0]['broadcasting'] == 'y'){

					$roomtype = $roominfo[0]['roomtype'];

					if($roomtype == 1){
						if($_SESSION['enter_'.$roominfo[0]['showId']] == 'y'){
							$roomtype = 0;
						}
					}

					if($roomtype == 2){
						if($_SESSION['enter_'.$roominfo[0]['showId']] == 'y'){
							$roomtype = 0;
						}
					}

					//判断是否VIP以及金钥匙
					$viewerinfo = D("Member")->find($_SESSION['uid']);
					if($roominfo[0]['online'] >= $roominfo[0]['maxonline']){
						if((int)$viewerinfo['vip'] > 0 && $viewerinfo['vipexpire'] > time()){
			
						}
						else{
							$roomtype = 3;
						}
					}	

					if($_SESSION['uid'] == $roominfo[0]['id']){
						$roomtype = 0;
					}

					if($viewerinfo['showadmin'] == '1'){
						$roomtype = 0;
					}

					echo '<roomtype>'.$roomtype.'</roomtype>';
				}
				else{
					echo '<offlinevideo>'.$roominfo[0]['offlinevideo'].'</offlinevideo>';
				}
				echo '</ROOT>';
			}
		}
		else{
			echo '<?xml version="1.0" encoding="UTF-8"?>';
			echo '<ROOT>';
			echo '</ROOT>';
		}
	}

	public function getuserinfo(){
		C('HTML_CACHE_ON',false);
		header('Content-Type: text/xml');
		if(!$_SESSION['uid']){
			$userid = rand(1000,9999);
			$_SESSION['uid'] = -$userid;
		}

		$roominfo = D("Member")->where('curroomnum='.$_GET["roomnum"].'')->select();
		$roomrichlevel = getRichlevel($roominfo[0]['spendcoin']);
		$roomemceelevel = getEmceelevel($roominfo[0]['earnbean']);

		if((int)$roominfo[0]['virtualguest'] > 0 ){
			$virtualusers = D('Member')->where('isvirtual="y"')->order('rand()')->select();
			$virtualusers_str = '';
			foreach($virtualusers as $val){
				$richlevel = getRichlevel($val['spendcoin']);
				$virtualusers_str .= $val['id'].'$$'.$val['nickname'].'$$'.$val['curroomnum'].'$$'.$val['vip'].'$$'.$richlevel[0]['levelid'].'$$'.$val['spendcoin'].'***';
			}
		}
		
		if($_SESSION['uid'] < 0){
			echo '<?xml version="1.0" encoding="UTF-8"?>';
			echo '<ROOT>';
			echo '<err>no</err>';
			echo '<Badge></Badge>';
			echo '<familyname></familyname>';
			echo '<goodnum></goodnum>';
			echo '<h>0</h>';
			echo '<level>0</level>';
			echo '<richlevel>0</richlevel>';
			echo '<spendcoin>0</spendcoin>';
			echo '<sellm>0</sellm>';
			echo '<sortnum></sortnum>';
			echo '<userType>20</userType>';
			echo '<userid>'.$_SESSION['uid'].'</userid>';
			echo '<username>游客'.$_SESSION['uid'].'</username>';
			echo '<vip>0</vip>';
			if($roominfo[0]['fakeuser'] == 'y'){
				echo '<fakeroom>y</fakeroom>';
				echo '<roomBadge></roomBadge>';
				echo '<roomfamilyname></roomfamilyname>';
				echo '<roomgoodnum>'.$roominfo[0]['curroomnum'].'</roomgoodnum>';
				echo '<roomlevel>'.$roomemceelevel[0]['levelid'].'</roomlevel>';
				echo '<roomrichlevel>'.$roomrichlevel[0]['levelid'].'</roomrichlevel>';
				echo '<roomuserid>'.$roominfo[0]['id'].'</roomuserid>';
				echo '<roomusername>'.$roominfo[0]['nickname'].'</roomusername>';
				echo '<roomvip>1</roomvip>';
			}
			else{
				echo '<fakeroom>n</fakeroom>';
			}
			if($roominfo[0]['broadcasting'] == 'y'){
				echo '<virtualguest>'.$roominfo[0]['virtualguest'].'</virtualguest>';
				echo '<virtualusers_str>'.$virtualusers_str.'</virtualusers_str>';
			}
			else{
				echo '<virtualguest>0</virtualguest>';
				echo '<virtualusers_str></virtualusers_str>';
			}
			echo '</ROOT>';
		}
		else{
			$userinfo = D("Member")->find($_SESSION['uid']);
			$richlevel = getRichlevel($userinfo['spendcoin']);
			$emceelevel = getEmceelevel($userinfo['earnbean']);
			
			echo '<?xml version="1.0" encoding="UTF-8"?>';
			echo '<ROOT>';
			echo '<err>no</err>';
			echo '<Badge></Badge>';
			echo '<familyname></familyname>';
			echo '<goodnum>'.$_SESSION['roomnum'].'</goodnum>';
			echo '<h>0</h>';
			echo '<level>'.$emceelevel[0]['levelid'].'</level>';
			echo '<richlevel>'.$richlevel[0]['levelid'].'</richlevel>';
			echo '<spendcoin>'.$userinfo['spendcoin'].'</spendcoin>';
			echo '<sellm>'.$userinfo['sellm'].'</sellm>';
			if($_SESSION['roomnum'] == $_GET['roomnum']){
				echo '<sortnum></sortnum>';
				echo '<userType>50</userType>';
			}
			else{
				echo '<sortnum></sortnum>';
				$myshowadmin = D("Roomadmin")->where('uid='.$roominfo[0]['id'].' and adminuid='.$_SESSION['uid'])->order('id asc')->select();
				if($userinfo['showadmin'] == '1' || $myshowadmin){
					echo '<userType>40</userType>';
				}
				else{
					echo '<userType>30</userType>';
				}
			}
			echo '<userid>'.$_SESSION['uid'].'</userid>';
			echo '<username>'.$_SESSION['nickname'].'</username>';
			if($userinfo['vipexpire'] > time()){
				echo '<vip>'.$userinfo['vip'].'</vip>';
			}
			else{
				echo '<vip>0</vip>';
			}
			if($roominfo[0]['fakeuser'] == 'y'){
				echo '<fakeroom>y</fakeroom>';
				echo '<roomBadge></roomBadge>';
				echo '<roomfamilyname></roomfamilyname>';
				echo '<roomgoodnum>'.$roominfo[0]['curroomnum'].'</roomgoodnum>';
				echo '<roomlevel>'.$roomemceelevel[0]['levelid'].'</roomlevel>';
				echo '<roomrichlevel>'.$roomrichlevel[0]['levelid'].'</roomrichlevel>';
				echo '<roomuserid>'.$roominfo[0]['id'].'</roomuserid>';
				echo '<roomusername>'.$roominfo[0]['nickname'].'</roomusername>';
				echo '<roomvip>1</roomvip>';
			}
			else{
				echo '<fakeroom>n</fakeroom>';
			}
			if($roominfo[0]['broadcasting'] == 'y' || $_SESSION['roomnum'] == $_GET['roomnum']){
				echo '<virtualguest>'.$roominfo[0]['virtualguest'].'</virtualguest>';
				echo '<virtualusers_str>'.$virtualusers_str.'</virtualusers_str>';
			}
			else{
				echo '<virtualguest>0</virtualguest>';
				echo '<virtualusers_str></virtualusers_str>';
			}
			echo '</ROOT>';
		}
    }

	public function createroom(){
		C('HTML_CACHE_ON',false);
		header('Content-Type: text/xml');

		if(!$_SESSION['uid'] || $_SESSION['uid'] < 0){
			$err = "您尚未登录，请登录后重试";
			echo '<?xml version="1.0" encoding="UTF-8"?>';
			echo '<ROOT>';
			echo '<err>yes</err>';
			echo '<msg>'.$err.'</msg>';
			echo '</ROOT>';
			exit;
		}

		$userinfo = D("Member")->find($_SESSION['uid']);

		if($userinfo['canlive'] == 'n'){
			$err = "您暂时没有直播权限";
			echo '<?xml version="1.0" encoding="UTF-8"?>';
			echo '<ROOT>';
			echo '<err>yes</err>';
			echo '<msg>'.$err.'</msg>';
			echo '</ROOT>';
			exit;
		}

		if($_REQUEST['roomtype'] == '1'){
			//判断用户虚拟币是否足够
			if($userinfo['coinbalance'] < 100){
				$err = "您的余额不足";
				echo '<?xml version="1.0" encoding="UTF-8"?>';
				echo '<ROOT>';
				echo '<err>yes</err>';
				echo '<msg>'.$err.'</msg>';
				echo '</ROOT>';
				exit;
			}
			else{
				//扣费
				D("Member")->execute('update ss_member set spendcoin=spendcoin+100,coinbalance=coinbalance-100 where id='.$_SESSION['uid']);
				//记入虚拟币交易明细
				$Coindetail = D("Coindetail");
				$Coindetail->create();
				$Coindetail->type = 'expend';
				$Coindetail->action = 'createspeshow';
				$Coindetail->uid = $_SESSION['uid'];
				
				$Coindetail->content = $userinfo['nickname'].' 创建了一个收费房间';
				$Coindetail->objectIcon = '/Public/images/fei.png';
				$Coindetail->coin = 100;
				
				$Coindetail->addtime = time();
				$detailId = $Coindetail->add();
			}
		}

		if($_REQUEST['roomtype'] == '2'){
			//判断用户虚拟币是否足够
			if($userinfo['coinbalance'] < 50){
				$err = "您的余额不足";
				echo '<?xml version="1.0" encoding="UTF-8"?>';
				echo '<ROOT>';
				echo '<err>yes</err>';
				echo '<msg>'.$err.'</msg>';
				echo '</ROOT>';
				exit;
			}
			else{
				//扣费
				D("Member")->execute('update ss_member set spendcoin=spendcoin+50,coinbalance=coinbalance-50 where id='.$_SESSION['uid']);
				//记入虚拟币交易明细
				$Coindetail = D("Coindetail");
				$Coindetail->create();
				$Coindetail->type = 'expend';
				$Coindetail->action = 'createspeshow';
				$Coindetail->uid = $_SESSION['uid'];
				
				$Coindetail->content = $userinfo['nickname'].' 创建了一个密码房间';
				$Coindetail->objectIcon = '/Public/images/fei.png';
				$Coindetail->coin = 50;
				
				$Coindetail->addtime = time();
				$detailId = $Coindetail->add();
			}
		}
		
			$User=D("Member");
			$User->create();
			$User->id = $_SESSION['uid'];
			$User->broadcasting = 'y';
			$showId = time();
			$User->showId = $showId;
			$User->starttime = time();
			$User->roomtype = $_REQUEST['roomtype'];
			if($_REQUEST['roomtype'] == '1'){
				$User->needmoney = $_REQUEST['needmoney'];
			}
			if($_REQUEST['roomtype'] == '2'){
				$User->roompsw = $_REQUEST['roompsw'];
			}
			$userId = $User->save();
			
			//新加一条直播记录
			$Liverecord=D("Liverecord");
			$Liverecord->create();
			$Liverecord->roomtype = $_REQUEST['roomtype'];
			$Liverecord->uid = $_SESSION['uid'];
			$Liverecord->showId = $showId;
			$Liverecord->starttime = time();
			$Liverecord->sign = $userinfo['sign'];
			$liveId = $Liverecord->add();
	
			echo '<?xml version="1.0" encoding="UTF-8"?>';
			echo '<ROOT>';
			echo '<err>no</err>';
			echo '<showId>'.$showId.'</showId>';
			echo '</ROOT>';

	}

	public function enterroom(){
		C('HTML_CACHE_ON',false);
		$userinfo = D("Member")->where('curroomnum='.$_REQUEST['roomid'].'')->select();
		D("Member")->execute('update ss_member set online=online+1 where curroomnum='.$_REQUEST['roomid']);
		if($userinfo[0]['broadcasting'] == 'y'){
			D("Liverecord")->execute('update ss_liverecord set entercount=entercount+1 where showId='.$userinfo[0]['showId']);
		}
	}

	public function exitroom(){
		C('HTML_CACHE_ON',false);
		$userinfo = D("Member")->find($_REQUEST['uid']);
		if($userinfo && $_REQUEST['roomid'] == $userinfo['curroomnum']){
			if($userinfo['broadcasting'] == 'y'){
				D("Member")->execute('update ss_member set ispublic="1",SongApply="1",broadcasting="n",showId=0,seat1_ucuid=0,seat1_nickname="",seat1_count=0,seat2_ucuid=0,seat2_nickname="",seat2_count=0,seat3_ucuid=0,seat3_nickname="",seat3_count=0,seat4_ucuid=0,seat4_nickname="",seat4_count=0,seat5_ucuid=0,seat5_nickname="",seat5_count=0 where id='.$_REQUEST['uid']);
				//写入当次直播记录的结束时间
				D("Liverecord")->execute('update ss_liverecord set endtime='.time().' where showId='.$userinfo['showId']);
			}
			else{
				D("Member")->execute('update ss_member set seat1_ucuid=0,seat1_nickname="",seat1_count=0,seat2_ucuid=0,seat2_nickname="",seat2_count=0,seat3_ucuid=0,seat3_nickname="",seat3_count=0,seat4_ucuid=0,seat4_nickname="",seat4_count=0,seat5_ucuid=0,seat5_nickname="",seat5_count=0 where id='.$_REQUEST['uid']);
			}
		}
		D("Member")->execute('update ss_member set online=online-1 where curroomnum='.$_REQUEST['roomid']);
	}

	public function resetonline(){
		C('HTML_CACHE_ON',false);
		D("Member")->execute('update ss_member set online=0 where host="'.$_REQUEST['host'].'"');
	}

	
	public function makesnap2(){
		C('HTML_CACHE_ON',false);
		if(!$_SESSION['uid'] || $_SESSION['uid'] < 0){
			echo '&err=nologin';
			exit;
		}

		$prefix = date('Y-m');
		$uploadPath = '/Public/snap/'.$prefix.'/';
		if(!is_dir('.'.$uploadPath)){
        	mkdir('.'.$uploadPath);
		}
		$filename = md5($_SESSION['roomnum']).'.jpg';

		if (isset($GLOBALS["HTTP_RAW_POST_DATA"]))  
		{  
			$png = gzuncompress($GLOBALS["HTTP_RAW_POST_DATA"]);   
			$file = fopen('.'.$uploadPath.$filename,"w");//打开文件准备写入  
			fwrite($file,$png);  
			fclose($file); 
			
			D("Member")->query('update ss_member set snap="'.$uploadPath.$filename.'" where id='.$_SESSION['uid']);
			echo "ok";
		}
	}

	public function makesnap(){
		C('HTML_CACHE_ON',false);
		if(!$_SESSION['uid'] || $_SESSION['uid'] < 0){
			echo '&err=nologin';
			exit;
		}

		$w = 160;
		$h = 120;

		$img = imagecreatetruecolor($w, $h);

		imagefill($img, 0, 0, 0xFFFFFF);

		$rows = 0;
		$cols = 0;

		$dataArr = explode("|", $_POST['imgdata']);

		for($rows = 0; $rows < $h; $rows++){
			$c_row = explode(",", $dataArr[$rows]);
			for($cols = 0; $cols < $w; $cols++){
				$value = $c_row[$cols];
				if($value != ""){
					$hex = $value;
					while(strlen($hex) < 6){
						$hex = "0" . $hex;
					}
					$r = hexdec(substr($hex, 0, 2));
					$g = hexdec(substr($hex, 2, 2));
					$b = hexdec(substr($hex, 4, 2));
					$test = imagecolorallocate($img, $r, $g, $b);
					imagesetpixel($img, $cols, $rows, $test);
				}
			}
		}
		//D("Siteconfig")->query('update ss_siteconfig set imgdata="'.$tmpstr.'" where id=1');

		$prefix = date('Y-m');
		$uploadPath = '/Public/snap/'.$prefix.'/';
		if(!is_dir('.'.$uploadPath)){
        	mkdir('.'.$uploadPath);
		}
		$filename = md5($_SESSION['roomnum']).'.jpg';

		imagejpeg($img, '.'.$uploadPath.$filename, 90);

		D("Member")->query('update ss_member set snap="'.$uploadPath.$filename.'" where id='.$_SESSION['uid']);
		echo '&snap='.$uploadPath.$filename.'?t='.time();
		exit;
	}

	public function setBulletin(){
		C('HTML_CACHE_ON',false);
		if(!$_SESSION['uid'] || $_SESSION['uid'] < 0){
			echo '{"info":"您尚未登录"}';
			exit;
		}

		if($_SESSION['uid'] != $_REQUEST['eid']){
			echo '{"info":"您不是该房间主人"}';
			exit;
		}

		$User=D("Member");
		$User->create();
		$User->id = $_SESSION['uid'];
		if($_REQUEST['bt'] == 2){
			$User->announce = $_REQUEST['t'];
			$User->annlink = $_REQUEST['u'];
		}
		if($_REQUEST['bt'] == 3){
			$User->announce2 = $_REQUEST['t'];
			$User->ann2link = $_REQUEST['u'];
		}
		$userId = $User->save();

		echo '{"code":"0"}';
		exit;
	}

	public function setBackground(){
		C('HTML_CACHE_ON',false);
		header("Content-type: text/html; charset=utf-8"); 
		if(!$_SESSION['uid'] || $_SESSION['uid'] < 0){
			echo "<script>alert('您尚未登录');</script>";
			exit;
		}

		if($_SESSION['uid'] != $_REQUEST['eid']){
			echo "<script>alert('您不是该房间主人');</script>";
			exit;
		}

		//上传缩略图
		import("@.ORG.UploadFile");
		$upload = new UploadFile();
		//设置上传文件大小
		$upload->maxSize  = 1048576 ;
		//设置上传文件类型
		$upload->allowExts  = explode(',','jpg');
		//设置上传目录
		//每个用户一个文件夹
		$prefix = date('Y-m');
		$uploadPath =  './Public/bgimg/'.$prefix.'/';
		if(!is_dir($uploadPath)){
        	mkdir($uploadPath);
		}
		$upload->savePath =  $uploadPath;
		$upload->saveRule = uniqid;
		//执行上传操作
		if(!$upload->upload()) {
			// 捕获上传异常
			echo "<script>alert('".$upload->getErrorMsg()."');</script>";
			exit;
		}
		else{
			$uploadList = $upload->getUploadFileInfo();
			$picpath = '/Public/bgimg/'.$prefix.'/'.$uploadList[0]['savename'];
		}

		D("Member")->execute('update ss_member set bgimg="'.$picpath.'" where id='.$_SESSION['uid']);
		
		echo "<script>document.domain='".$this->domainroot."';alert('上传成功');window.parent.playerMenu.setBackground2('".$picpath."');</script>";
		exit;
	}

	public function cancelBackground(){
		C('HTML_CACHE_ON',false);
		if(!$_SESSION['uid'] || $_SESSION['uid'] < 0){
			echo '{"code":"1"}';
			exit;
		}

		if($_SESSION['uid'] != $_REQUEST['eid']){
			echo '{"code":"2"}';
			exit;
		}

		D("Member")->execute('update ss_member set bgimg="" where id='.$_SESSION['uid']);

		echo '{"code":"0"}';
		exit;
	}

	public function setOfflineVideo(){
		C('HTML_CACHE_ON',false);
		if(!$_SESSION['uid'] || $_SESSION['uid'] < 0){
			echo '{"code":"1","info":"您尚未登录"}';
			exit;
		}

		if($_SESSION['uid'] != $_REQUEST['eid']){
			echo '{"code":"2","info":"您不是该房间主人"}';
			exit;
		}

		D("Member")->execute('update ss_member set offlinevideo="'.$_REQUEST['url'].'" where id='.$_SESSION['uid']);

		echo '{"code":"0"}';
		exit;
	}

	public function cancelOfflineVideo(){
		C('HTML_CACHE_ON',false);
		if(!$_SESSION['uid'] || $_SESSION['uid'] < 0){
			echo '{"code":"1","info":"您尚未登录"}';
			exit;
		}

		if($_SESSION['uid'] != $_REQUEST['eid']){
			echo '{"code":"2","info":"您不是该房间主人"}';
			exit;
		}

		D("Member")->execute('update ss_member set offlinevideo="" where id='.$_SESSION['uid']);

		echo '{"code":"0"}';
		exit;
	}

	public function setPublicChat(){
		C('HTML_CACHE_ON',false);
		if(!$_SESSION['uid'] || $_SESSION['uid'] < 0){
			echo '{"state":"3","info":"您尚未登录"}';
			exit;
		}

		if($_SESSION['uid'] != $_REQUEST['eid']){
			echo '{"state":"3","info":"您不是该房间主人"}';
			exit;
		}

		D("Member")->execute('update ss_member set ispublic="'.$_REQUEST['flag'].'" where id='.$_SESSION['uid']);

		echo '{"state":"'.$_REQUEST['flag'].'"}';
		exit;
	}

	public function wishing(){
		C('HTML_CACHE_ON',false);
		if(!$_SESSION['uid'] || $_SESSION['uid'] < 0){
			echo '{"state":"3","info":"您尚未登录"}';
			exit;
		}

		if($_REQUEST['action'] == 'isWished'){
			/*
			$userinfo = D("Member")->find($_SESSION['uid']);
			if($userinfo){
				if(date('Y-m-d',$userinfo['wishtime']) == date('Y-m-d',time())){
					echo '1';
					exit;
				}
				else{
					echo '0';
					exit;
				}
			}
			*/
			$userwishs = D("Wish")->where('uid='.$_SESSION['uid'].' and date_format(FROM_UNIXTIME(wishtime),"%m-%d-%Y")=date_format(now(),"%m-%d-%Y")')->order('id asc')->select();
			if($userwishs){
				echo '1';
				exit;
			}
			else{
				echo '0';
				exit;
			}
		}
		
		if($_REQUEST['action'] == 'save'){
			//判断虚拟币是否足够

			//添加许愿
			/*
			$User=D("Member");
			$User->create();
			$User->id = $_SESSION['uid'];
			if($_REQUEST['type'] == '1'){
				$User->wish = '<strong class="p1">我的心愿：</strong>我今天希望得到<strong class="p2">'.$_REQUEST['num'].'</strong>个'.$_REQUEST['giftName'];
			}
			if($_REQUEST['type'] == '2'){
				$User->wish = '<strong class="p1">我的心愿：</strong>我今天希望得到<strong class="p2">'.$_REQUEST['num'].'</strong>人热捧';
			}
			$User->wishtime = time();
			$userId = $User->save();
			*/
			$Wish=D("Wish");
			$Wish->create();
			$Wish->uid = $_SESSION['uid'];
			if($_REQUEST['type'] == '1'){
				$Wish->wish = '<strong class="p1">我的心愿：</strong>我今天希望得到<strong class="p2">'.$_REQUEST['num'].'</strong>个'.$_REQUEST['giftName'];
			}
			if($_REQUEST['type'] == '2'){
				$Wish->wish = '<strong class="p1">我的心愿：</strong>我今天希望得到<strong class="p2">'.$_REQUEST['num'].'</strong>人热捧';
			}
			$Wish->wishtime = time();
			$wishId = $Wish->add();

			echo '{"wishedFlag":"1","wishType":"'.$_REQUEST['type'].'","count":"'.$_REQUEST['num'].'","giftName":"'.$_REQUEST['giftName'].'"}';
			exit;
		}
	}

	public function sign_view(){
		C('HTML_CACHE_ON',false);
		if(!$_SESSION['uid'] || $_SESSION['uid'] < 0){
			$this->assign('jumpUrl',__APP__);
			$this->error('您尚未登录');
		}

		$userinfo = D("Member")->find($_SESSION['uid']);
		if($userinfo['sign'] == 'y'){
			$this->assign('jumpUrl',__APP__);
			$this->error('您已是签约主播，更改资料请联系客服');
		}

		$this->display();
	}

	public function do_sign_view(){
		C('HTML_CACHE_ON',false);
		if(!$_SESSION['uid'] || $_SESSION['uid'] < 0){
			$this->assign('jumpUrl',__APP__);
			$this->error('您尚未登录');
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
		$uploadPath =  './Public/bigpic/'.$prefix.'/';
		if(!is_dir($uploadPath)){
        	mkdir($uploadPath);
		}
		$upload->savePath =  $uploadPath;
		$upload->saveRule = uniqid;
		//执行上传操作
		if(!$upload->upload()) {
			// 捕获上传异常 
			if($upload->getErrorMsg() != '没有选择上传文件'){
				//echo "<script>alert('".$upload->getErrorMsg()."');</script>";
				//exit;
				$this->error($upload->getErrorMsg());
				exit;
			}
		}
		else{
			$uploadList = $upload->getUploadFileInfo();
			$picpath = '/Public/bigpic/'.$prefix.'/'.$uploadList[0]['savename'];
		}

		$User = D('Member');
		$vo = $User->create();
		if(!$vo) {
			$this->error($User->getError());
		}else{
			$User->sign = 'i';
			$User->bigpic = $picpath;
			$User->save();
			
			$this->assign('jumpUrl',__APP__);
			$this->success('签约审核中，请等待管理员与您联系');
		}

		$this->display();
	}

	public function index(){
		/*if(!$_SESSION['uid'] || $_SESSION['uid'] < 0){
			$this->assign('jumpUrl',__APP__);
			$this->error('您尚未登录');
		}*/
		//根据用户uid判断用户是否为充值代理
		$uid=$_SESSION["uid"];
		//var_dump($uid);
		$emceeagent=M("member")->where("id=".$uid)->getField("emceeagent");
		//var_dump($emceeagent);
		$this->assign("emceeagent",$emceeagent);
		$this->display();
	}

	public function myfavor(){
		C('HTML_CACHE_ON',false);
		if(!$_SESSION['uid'] || $_SESSION['uid'] < 0){
			$this->assign('jumpUrl',__APP__);
			$this->error('您尚未登录');
		}

		$favors = D("Favor")->where("uid=".$_SESSION['uid'])->order('addtime desc')->select();
		foreach($favors as $n=> $val){
			$favors[$n]['voo']=D("Member")->where('id='.$val['favoruid'])->select();
		}
		$this->assign('favors', $favors);

		$this->display();
	}

	public function delfavor(){
		C('HTML_CACHE_ON',false);
		if(!$_SESSION['uid'] || $_SESSION['uid'] < 0){
			$this->assign('jumpUrl',__APP__);
			$this->error('您尚未登录');
		}

		$fidArr = explode(",", $_GET['fid']);
		foreach ($fidArr as $k){
			$favorinfo = D("Favor")->find($k);
			if($favorinfo && $favorinfo['uid'] == $_SESSION['uid']){
				D("Favor")->where('id='.$k)->delete();
			}
		}

		$this->assign('jumpUrl',__URL__."/myfavor/");
		$this->success('操作成功');
	}

	public function bookmark_add(){
		C('HTML_CACHE_ON',false);
		if(!$_SESSION['uid'] || $_SESSION['uid'] < 0){
			echo '{"state":"1"}';
			exit;
		}

		$favors = D("Favor")->where('uid='.$_SESSION['uid'].' and favoruid='.$_REQUEST['emceeid'])->order('id asc')->select();
		if($favors){
			echo '{"state":"0","op":"repeat"}';
			exit;
		}
		else{
			$Favor=D("Favor");
			$Favor->create();
			$Favor->uid = $_SESSION['uid'];
			$Favor->favoruid = $_REQUEST['emceeid'];
			$favorId = $Favor->add();

			if($favorId > 0){
				echo '{"state":"0","op":"cancle"}';
				exit;
			}
			else{
				echo '{"state":"1"}';
				exit;
			}
		}
	}

	public function bookmark_cancle(){
		C('HTML_CACHE_ON',false);
		if(!$_SESSION['uid'] || $_SESSION['uid'] < 0){
			echo '{"state":"1"}';
			exit;
		}
		
		D("Favor")->where('uid='.$_SESSION['uid'].' and favoruid='.$_REQUEST['emceeid'])->delete();
		
		echo '{"state":"0","op":""}';
		exit;
	}

	public function interestToList(){
		C('HTML_CACHE_ON',false);
		if(!$_SESSION['uid'] || $_SESSION['uid'] < 0){
			$this->assign('jumpUrl',__APP__);
			$this->error('您尚未登录');
		}

		//$attentions = D("Attention")->where("uid=".$_SESSION['uid'])->order('addtime desc')->select();
		//foreach($attentions as $n=> $val){
			//$attentions[$n]['voo']=D("Member")->where('id='.$val['attuid'])->select();
		//}
		//$this->assign('attentions', $attentions);

		$Attention = D("Attention");
		$count = $Attention->where("uid=".$_SESSION['uid'])->count();
		$listRows = 12;
		import("@.ORG.Page2");
		$p = new Page($count,$listRows,$linkFront);
		$attentions = $Attention->where("uid=".$_SESSION['uid'])->limit($p->firstRow.",".$p->listRows)->order('addtime desc')->select();
		foreach($attentions as $n=> $val){
			$attentions[$n]['voo']=D("Member")->where('id='.$val['attuid'])->select();
		}
		$page = $p->show();
		$this->assign('attentions',$attentions);
		$this->assign('count',$count);
		$this->assign('page',$page);

		//我捧的人
		$mypengusers = D('Coindetail')->query('SELECT touid,sum(coin) as total FROM `ss_coindetail` where type="expend" and uid='.$_SESSION['uid'].' and touid>0 group by touid order by total desc LIMIT 5');
		foreach($mypengusers as $n=> $val){
			$mypengusers[$n]['voo']=D("Member")->where('id='.$val['touid'])->select();
		}
		$this->assign('mypengusers', $mypengusers);

		$this->display();
	}

	public function cancelInterest(){
		C('HTML_CACHE_ON',false);
		if(!$_SESSION['uid'] || $_SESSION['uid'] < 0){
			$this->assign('jumpUrl',__APP__);
			$this->error('您尚未登录');
		}
		
		D("Attention")->where('uid='.$_SESSION['uid'].' and attuid='.$_REQUEST['uid'])->delete();
		
		//$this->assign('jumpUrl',__URL__."/interestToList/");
		//$this->success('操作成功');
		echo '1';
		exit;
	}

	public function interest(){
		C('HTML_CACHE_ON',false);
		if(!$_SESSION['uid'] || $_SESSION['uid'] < 0){
			$this->assign('jumpUrl',__APP__);
			$this->error('您尚未登录');
		}

		$Attention=D("Attention");
		$Attention->create();
		$Attention->uid = $_SESSION['uid'];
		$Attention->attuid = $_REQUEST['uid'];
		$attId = $Attention->add();

		if($attId > 0){
			echo '1';
			exit;
		}
		else{
			echo '0';
			exit;
		}
	}

	public function myNos(){
		C('HTML_CACHE_ON',false);
		if(!$_SESSION['uid'] || $_SESSION['uid'] < 0){
			$this->assign('jumpUrl',__APP__);
			$this->error('您尚未登录');
		}

		$userinfo = D("Member")->find($_SESSION['uid']);
		$this->assign('userinfo', $userinfo);

		$mynos = D("Roomnum")->where("uid=".$_SESSION['uid'])->order('addtime asc')->select();
		$this->assign('mynos', $mynos);

		$attentions = D("Attention")->where("uid=".$_SESSION['uid'])->order('addtime desc')->select();
		foreach($attentions as $n=> $val){
			$attentions[$n]['voo']=D("Member")->where('id='.$val['attuid'])->select();
		}
		$this->assign('attentions', $attentions);

		$this->display();
	}

	public function setcurroomnum(){
		C('HTML_CACHE_ON',false);
		if(!$_SESSION['uid'] || $_SESSION['uid'] < 0){
			$this->assign('jumpUrl',__APP__);
			$this->error('您尚未登录');
		}

		if($_GET["roomnum"] == '')
		{
			$this->assign('jumpUrl',__APP__.'/User/');
			$this->error('缺少参数或参数不正确');
		}
		else{
			$numinfo = D("Roomnum")->where('num='.$_GET["roomnum"].'')->select();
			if($numinfo){
				if($numinfo[0]['uid'] == $_SESSION['uid']){
					D("Member")->execute('update ss_member set curroomnum='.$_GET["roomnum"].' where id='.$_SESSION['uid']);
					session('roomnum',$_GET["roomnum"]);
					cookie('roomnum',$_GET["roomnum"],3600000);
					$this->assign('jumpUrl',__APP__.'/User/myNos/');
					$this->success('启用成功');
				}
				else{
					$this->assign('jumpUrl',__APP__.'/User/myNos/');
					$this->error('您不是该房间号的主人');
				}
			}
			else{
				$this->assign('jumpUrl',__APP__.'/User/myNos/');
				$this->error('没有该房间号');
			}
		}
	}

	public function transroomnum(){
		C('HTML_CACHE_ON',false);
		if(!$_SESSION['uid'] || $_SESSION['uid'] < 0){
			echo 'error';
			exit;
		}

		if($_GET["roomnum"] == '' || $_GET["grantId"] == '')
		{
			echo 'error';
			exit;
		}
		else{
			$numinfo = D("Roomnum")->where('num='.$_GET["roomnum"].'')->select();
			if($numinfo){
				if($numinfo[0]['uid'] == $_SESSION['uid']){
					if($_GET["grantId"] == $_SESSION['uid']){
						echo 'error';
						exit;
					}
					else{
						D("Roomnum")->execute('update ss_roomnum set uid='.$_GET["grantId"].' where num='.$_GET["roomnum"]);
						//写一条记录到ss_giveaway
						$Giveaway = D("Giveaway");
						$Giveaway->create();
						$Giveaway->uid = $_SESSION['uid'];
						$Giveaway->touid = $_GET["grantId"];
						$Giveaway->content = '('.$_GET["roomnum"].')';
						$Giveaway->objectIcon = '/Public/images/gnum.png';
						$giveId = $Giveaway->add();
						echo 'success';
						exit;
					}
				}
				else{
					echo 'error';
					exit;
				}
			}
			else{
				echo 'error';
				exit;
			}
		}
	}

	public function toolinuse(){
		C('HTML_CACHE_ON',false);
		if(!$_SESSION['uid'] || $_SESSION['uid'] < 0){
			$this->assign('jumpUrl',__APP__);
			$this->error('您尚未登录');
		}

		$userinfo = D("Member")->find($_SESSION['uid']);
		$this->assign('userinfo', $userinfo);

		$this->display();
	}

	public function toolItem(){
		/* if(!$_SESSION['uid'] || $_SESSION['uid'] < 0){
			$this->assign('jumpUrl',__APP__);
			$this->error('您尚未登录');
		} */

		$this->display();
	}

	public function buyTool(){
		C('HTML_CACHE_ON',false);
		if(!$_SESSION['uid'] || $_SESSION['uid'] < 0){
			echo '{"msg":"请重新登录"}';
			exit;
		}

		$userinfo = D("Member")->find($_SESSION['uid']);
		$richlevel = getRichlevel($userinfo['spendcoin']);

		switch ($_GET['toolid']){
			case '1':
				//判断用户富豪级别
				if($richlevel[0]['levelid'] < 10){
					echo '{"msg":"限10富及以上等级购买"}';
					exit;
				}

				if($_GET['toolsubid']  == 1){
					$needcoin = 20000;
					$duration = 3600 * 24 * 30 * 1;
					$duration2 = '一个月';
				}
				else if($_GET['toolsubid']  == 2){
					$needcoin = 48000;
					$duration = 3600 * 24 * 30 * 3;
					$duration2 = '三个月';
				}
				else if($_GET['toolsubid']  == 3){
					$needcoin = 84000;
					$duration = 3600 * 24 * 30 * 6;
					$duration2 = '六个月';
				}
				else if($_GET['toolsubid']  == 4){
					$needcoin = 120000;
					$duration = 3600 * 24 * 30 * 12;
					$duration2 = '十二个月';
				}

				if($userinfo['coinbalance'] < $needcoin){
					echo '{"msg":"您的余额不足"}';
					exit;
				}
				else{
					if($userinfo['vipexpire'] == 0){
						D("Member")->execute('update ss_member set vip="1",vipexpire=vipexpire+'.(time() + $duration).',spendcoin=spendcoin+'.$needcoin.',coinbalance=coinbalance-'.$needcoin.' where id='.$_SESSION['uid']);
					}
					else{
						D("Member")->execute('update ss_member set vip="1",vipexpire=vipexpire+'.$duration.',spendcoin=spendcoin+'.$needcoin.',coinbalance=coinbalance-'.$needcoin.' where id='.$_SESSION['uid']);
					}
					//写入消费明细
					$Coindetail = D("Coindetail");
					$Coindetail->create();
					$Coindetail->type = 'expend';
					$Coindetail->action = 'buy';
					$Coindetail->uid = $_SESSION['uid'];
					$Coindetail->giftcount = 1;
					$Coindetail->content = '您购买了 '.$duration2.' 至尊VIP';
					$Coindetail->objectIcon = '/Public/images/vip1.png';
					$Coindetail->coin = $needcoin;
					$Coindetail->addtime = time();
					$detailId = $Coindetail->add();
					
					echo '{"msg":"购买成功"}';
					exit;
				}
				break;
			case '2':
				//判断用户富豪级别
				if($richlevel[0]['levelid'] < 3){
					echo '{"msg":"限3富及以上等级购买"}';
					exit;
				}
				if($userinfo['vip'] == '1' && $userinfo['vipexpire'] > time()){
					echo '{"msg":"您已经是至尊VIP了"}';
					exit;
				}

				if($_GET['toolsubid']  == 5){
					$needcoin = 15000;
					$duration = 3600 * 24 * 30 * 1;
					$duration2 = '一个月';
				}
				else if($_GET['toolsubid']  == 6){
					$needcoin = 40000;
					$duration = 3600 * 24 * 30 * 3;
					$duration2 = '三个月';
				}
				else if($_GET['toolsubid']  == 7){
					$needcoin = 65000;
					$duration = 3600 * 24 * 30 * 6;
					$duration2 = '六个月';
				}
				else if($_GET['toolsubid']  == 8){
					$needcoin = 100000;
					$duration = 3600 * 24 * 30 * 12;
					$duration2 = '十二个月';
				}
				
				if($userinfo['coinbalance'] < $needcoin){
					echo '{"msg":"您的余额不足"}';
					exit;
				}
				else{
					if($userinfo['vipexpire'] == 0){
						D("Member")->execute('update ss_member set vip="2",vipexpire=vipexpire+'.(time() + $duration).',spendcoin=spendcoin+'.$needcoin.',coinbalance=coinbalance-'.$needcoin.' where id='.$_SESSION['uid']);
					}
					else{
						D("Member")->execute('update ss_member set vip="2",vipexpire=vipexpire+'.$duration.',spendcoin=spendcoin+'.$needcoin.',coinbalance=coinbalance-'.$needcoin.' where id='.$_SESSION['uid']);
					}
					//写入消费明细
					$Coindetail = D("Coindetail");
					$Coindetail->create();
					$Coindetail->type = 'expend';
					$Coindetail->action = 'buy';
					$Coindetail->uid = $_SESSION['uid'];
					$Coindetail->giftcount = 1;
					$Coindetail->content = '您购买了 '.$duration2.' VIP';
					$Coindetail->objectIcon = '/Public/images/vip2.png';
					$Coindetail->coin = $needcoin;
					$Coindetail->addtime = time();
					$detailId = $Coindetail->add();
					
					echo '{"msg":"购买成功"}';
					exit;
				}
				break;
			case '3':
				if($_GET['toolsubid']  == 9){
					$needcoin = 15000;
					$duration = 3600 * 24 * 30 * 1;
					$duration2 = '一个月';
				}
				
				if($userinfo['coinbalance'] < $needcoin){
					echo '{"msg":"您的余额不足"}';
					exit;
				}
				else{
					if($userinfo['gkexpire'] == 0){
						D("Member")->execute('update ss_member set goldkey="y",gkexpire=gkexpire+'.(time() + $duration).',spendcoin=spendcoin+'.$needcoin.',coinbalance=coinbalance-'.$needcoin.' where id='.$_SESSION['uid']);
					}
					else{
						D("Member")->execute('update ss_member set goldkey="y",gkexpire=gkexpire+'.$duration.',spendcoin=spendcoin+'.$needcoin.',coinbalance=coinbalance-'.$needcoin.' where id='.$_SESSION['uid']);
					}
					//写入消费明细
					$Coindetail = D("Coindetail");
					$Coindetail->create();
					$Coindetail->type = 'expend';
					$Coindetail->action = 'buy';
					$Coindetail->uid = $_SESSION['uid'];
					$Coindetail->giftcount = 1;
					$Coindetail->content = '您购买了 '.$duration2.' 金钥匙';
					$Coindetail->objectIcon = '/Public/images/goldkey.png';
					$Coindetail->coin = $needcoin;
					$Coindetail->addtime = time();
					$detailId = $Coindetail->add();
					
					echo '{"msg":"购买成功"}';
					exit;
				}
				break;
			case '4':
				if($_GET['toolsubid']  == 10){
					$needcoin = 50000;
					$duration = 3600 * 24 * 30 * 1;
					$duration2 = '一个月';
				}
				
				if($userinfo['coinbalance'] < $needcoin){
					echo '{"msg":"您的余额不足"}';
					exit;
				}
				else{
					if($userinfo['awexpire'] == 0){
						D("Member")->execute('update ss_member set atwill="y",awexpire=awexpire+'.(time() + $duration).',spendcoin=spendcoin+'.$needcoin.',coinbalance=coinbalance-'.$needcoin.' where id='.$_SESSION['uid']);
					}
					else{
						D("Member")->execute('update ss_member set atwill="y",awexpire=awexpire+'.$duration.',spendcoin=spendcoin+'.$needcoin.',coinbalance=coinbalance-'.$needcoin.' where id='.$_SESSION['uid']);
					}
					//写入消费明细
					$Coindetail = D("Coindetail");
					$Coindetail->create();
					$Coindetail->type = 'expend';
					$Coindetail->action = 'buy';
					$Coindetail->uid = $_SESSION['uid'];
					$Coindetail->giftcount = 1;
					$Coindetail->content = '您购买了 '.$duration2.' 随意说';
					$Coindetail->objectIcon = '/Public/images/vip1.png';
					$Coindetail->coin = $needcoin;
					$Coindetail->addtime = time();
					$detailId = $Coindetail->add();
					
					echo '{"msg":"购买成功"}';
					exit;
				}
				break;
//新道具开始
			case '5':
				if($_GET['toolsubid']  == 11){
					$needcoin = 50000;
					$duration = 3600 * 24 * 30 * 1;
					$duration2 = '一个月';
				}
				
				if($userinfo['coinbalance'] < $needcoin){
					echo '{"msg":"您的余额不足"}';
					exit;
				}
				else{
					if($userinfo['daoju1expire'] == 0){
						D("Member")->execute('update ss_member set daoju1="y",daoju1expire=daoju1expire+'.(time() + $duration).',spendcoin=spendcoin+'.$needcoin.',coinbalance=coinbalance-'.$needcoin.' where id='.$_SESSION['uid']);
					}
					else{
						D("Member")->execute('update ss_member set daoju1="y",daoju1expire=daoju1expire+'.$duration.',spendcoin=spendcoin+'.$needcoin.',coinbalance=coinbalance-'.$needcoin.' where id='.$_SESSION['uid']);
					}
					//写入消费明细
					$Coindetail = D("Coindetail");
					$Coindetail->create();
					$Coindetail->type = 'expend';
					$Coindetail->action = 'buy';
					$Coindetail->uid = $_SESSION['uid'];
					$Coindetail->giftcount = 1;
					$Coindetail->content = '您购买了 '.$duration2.' 道具1';
					$Coindetail->objectIcon = '/Public/images/vip1.png';//名字前面显示的图标
					$Coindetail->coin = $needcoin;
					$Coindetail->addtime = time();
					$detailId = $Coindetail->add();
					
					echo '{"msg":"购买成功"}';
					exit;
				}
				break;
			case '6':
				if($_GET['toolsubid']  == 12){
					$needcoin = 50000;
					$duration = 3600 * 24 * 30 * 1;
					$duration2 = '一个月';
				}
				
				if($userinfo['coinbalance'] < $needcoin){
					echo '{"msg":"您的余额不足"}';
					exit;
				}
				else{
					if($userinfo['daoju2expire'] == 0){
						D("Member")->execute('update ss_member set daoju2="y",daoju2expire=daoju2expire+'.(time() + $duration).',spendcoin=spendcoin+'.$needcoin.',coinbalance=coinbalance-'.$needcoin.' where id='.$_SESSION['uid']);
					}
					else{
						D("Member")->execute('update ss_member set daoju2="y",daoju2expire=daoju2expire+'.$duration.',spendcoin=spendcoin+'.$needcoin.',coinbalance=coinbalance-'.$needcoin.' where id='.$_SESSION['uid']);
					}
					//写入消费明细
					$Coindetail = D("Coindetail");
					$Coindetail->create();
					$Coindetail->type = 'expend';
					$Coindetail->action = 'buy';
					$Coindetail->uid = $_SESSION['uid'];
					$Coindetail->giftcount = 1;
					$Coindetail->content = '您购买了 '.$duration2.' 道具2';
					$Coindetail->objectIcon = '/Public/images/vip1.png';//名字前面显示的图标
					$Coindetail->coin = $needcoin;
					$Coindetail->addtime = time();
					$detailId = $Coindetail->add();
					
					echo '{"msg":"购买成功"}';
					exit;
				}
				break;
			case '7':
				if($_GET['toolsubid']  == 13){
					$needcoin = 50000;
					$duration = 3600 * 24 * 30 * 1;
					$duration2 = '一个月';
				}
				
				if($userinfo['coinbalance'] < $needcoin){
					echo '{"msg":"您的余额不足"}';
					exit;
				}
				else{
					if($userinfo['daoju3expire'] == 0){
						D("Member")->execute('update ss_member set daoju3="y",daoju3expire=daoju3expire+'.(time() + $duration).',spendcoin=spendcoin+'.$needcoin.',coinbalance=coinbalance-'.$needcoin.' where id='.$_SESSION['uid']);
					}
					else{
						D("Member")->execute('update ss_member set daoju3="y",daoju3expire=daoju3expire+'.$duration.',spendcoin=spendcoin+'.$needcoin.',coinbalance=coinbalance-'.$needcoin.' where id='.$_SESSION['uid']);
					}
					//写入消费明细
					$Coindetail = D("Coindetail");
					$Coindetail->create();
					$Coindetail->type = 'expend';
					$Coindetail->action = 'buy';
					$Coindetail->uid = $_SESSION['uid'];
					$Coindetail->giftcount = 1;
					$Coindetail->content = '您购买了 '.$duration2.' 道具3';
					$Coindetail->objectIcon = '/Public/images/vip1.png';//名字前面显示的图标
					$Coindetail->coin = $needcoin;
					$Coindetail->addtime = time();
					$detailId = $Coindetail->add();
					
					echo '{"msg":"购买成功"}';
					exit;
				}
				break;
			case '8':
				if($_GET['toolsubid']  == 14){
					$needcoin = 50000;
					$duration = 3600 * 24 * 30 * 1;
					$duration2 = '一个月';
				}
				
				if($userinfo['coinbalance'] < $needcoin){
					echo '{"msg":"您的余额不足"}';
					exit;
				}
				else{
					if($userinfo['daoju4expire'] == 0){
						D("Member")->execute('update ss_member set daoju4="y",daoju4expire=daoju4expire+'.(time() + $duration).',spendcoin=spendcoin+'.$needcoin.',coinbalance=coinbalance-'.$needcoin.' where id='.$_SESSION['uid']);
					}
					else{
						D("Member")->execute('update ss_member set daoju4="y",daoju4expire=daoju4expire+'.$duration.',spendcoin=spendcoin+'.$needcoin.',coinbalance=coinbalance-'.$needcoin.' where id='.$_SESSION['uid']);
					}
					//写入消费明细
					$Coindetail = D("Coindetail");
					$Coindetail->create();
					$Coindetail->type = 'expend';
					$Coindetail->action = 'buy';
					$Coindetail->uid = $_SESSION['uid'];
					$Coindetail->giftcount = 1;
					$Coindetail->content = '您购买了 '.$duration2.' 道具4';
					$Coindetail->objectIcon = '/Public/images/vip1.png';//名字前面显示的图标
					$Coindetail->coin = $needcoin;
					$Coindetail->addtime = time();
					$detailId = $Coindetail->add();
					
					echo '{"msg":"购买成功"}';
					exit;
				}
				break;
			case '9':
				if($_GET['toolsubid']  == 15){
					$needcoin = 50000;
					$duration = 3600 * 24 * 30 * 1;
					$duration2 = '一个月';
				}
				
				if($userinfo['coinbalance'] < $needcoin){
					echo '{"msg":"您的余额不足"}';
					exit;
				}
				else{
					if($userinfo['daoju5expire'] == 0){
						D("Member")->execute('update ss_member set daoju5="y",daoju5expire=daoju5expire+'.(time() + $duration).',spendcoin=spendcoin+'.$needcoin.',coinbalance=coinbalance-'.$needcoin.' where id='.$_SESSION['uid']);
					}
					else{
						D("Member")->execute('update ss_member set daoju5="y",daoju5expire=daoju5expire+'.$duration.',spendcoin=spendcoin+'.$needcoin.',coinbalance=coinbalance-'.$needcoin.' where id='.$_SESSION['uid']);
					}
					//写入消费明细
					$Coindetail = D("Coindetail");
					$Coindetail->create();
					$Coindetail->type = 'expend';
					$Coindetail->action = 'buy';
					$Coindetail->uid = $_SESSION['uid'];
					$Coindetail->giftcount = 1;
					$Coindetail->content = '您购买了 '.$duration2.' 道具5';
					$Coindetail->objectIcon = '/Public/images/vip1.png';//名字前面显示的图标
					$Coindetail->coin = $needcoin;
					$Coindetail->addtime = time();
					$detailId = $Coindetail->add();
					
					echo '{"msg":"购买成功"}';
					exit;
				}
				break;
			case '10':
				if($_GET['toolsubid']  == 16){
					$needcoin = 50000;
					$duration = 3600 * 24 * 30 * 1;
					$duration2 = '一个月';
				}
				
				if($userinfo['coinbalance'] < $needcoin){
					echo '{"msg":"您的余额不足"}';
					exit;
				}
				else{
					if($userinfo['daoju6expire'] == 0){
						D("Member")->execute('update ss_member set daoju6="y",daoju6expire=daoju6expire+'.(time() + $duration).',spendcoin=spendcoin+'.$needcoin.',coinbalance=coinbalance-'.$needcoin.' where id='.$_SESSION['uid']);
					}
					else{
						D("Member")->execute('update ss_member set daoju6="y",daoju6expire=daoju6expire+'.$duration.',spendcoin=spendcoin+'.$needcoin.',coinbalance=coinbalance-'.$needcoin.' where id='.$_SESSION['uid']);
					}
					//写入消费明细
					$Coindetail = D("Coindetail");
					$Coindetail->create();
					$Coindetail->type = 'expend';
					$Coindetail->action = 'buy';
					$Coindetail->uid = $_SESSION['uid'];
					$Coindetail->giftcount = 1;
					$Coindetail->content = '您购买了 '.$duration2.' 道具6';
					$Coindetail->objectIcon = '/Public/images/vip1.png';//名字前面显示的图标
					$Coindetail->coin = $needcoin;
					$Coindetail->addtime = time();
					$detailId = $Coindetail->add();
					
					echo '{"msg":"购买成功"}';
					exit;
				}
				break;
			case '11':
				if($_GET['toolsubid']  == 17){
					$needcoin = 50000;
					$duration = 3600 * 24 * 30 * 1;
					$duration2 = '一个月';
				}
				
				if($userinfo['coinbalance'] < $needcoin){
					echo '{"msg":"您的余额不足"}';
					exit;
				}
				else{
					if($userinfo['daoju7expire'] == 0){
						D("Member")->execute('update ss_member set daoju7="y",daoju7expire=daoju7expire+'.(time() + $duration).',spendcoin=spendcoin+'.$needcoin.',coinbalance=coinbalance-'.$needcoin.' where id='.$_SESSION['uid']);
					}
					else{
						D("Member")->execute('update ss_member set daoju7="y",daoju7expire=daoju7expire+'.$duration.',spendcoin=spendcoin+'.$needcoin.',coinbalance=coinbalance-'.$needcoin.' where id='.$_SESSION['uid']);
					}
					//写入消费明细
					$Coindetail = D("Coindetail");
					$Coindetail->create();
					$Coindetail->type = 'expend';
					$Coindetail->action = 'buy';
					$Coindetail->uid = $_SESSION['uid'];
					$Coindetail->giftcount = 1;
					$Coindetail->content = '您购买了 '.$duration2.' 道具7';
					$Coindetail->objectIcon = '/Public/images/vip1.png';//名字前面显示的图标
					$Coindetail->coin = $needcoin;
					$Coindetail->addtime = time();
					$detailId = $Coindetail->add();
					
					echo '{"msg":"购买成功"}';
					exit;
				}
				break;
			case '12':
				if($_GET['toolsubid']  == 18){
					$needcoin = 50000;
					$duration = 3600 * 24 * 30 * 1;
					$duration2 = '一个月';
				}
				
				if($userinfo['coinbalance'] < $needcoin){
					echo '{"msg":"您的余额不足"}';
					exit;
				}
				else{
					if($userinfo['daoju8expire'] == 0){
						D("Member")->execute('update ss_member set daoju8="y",daoju8expire=daoju8expire+'.(time() + $duration).',spendcoin=spendcoin+'.$needcoin.',coinbalance=coinbalance-'.$needcoin.' where id='.$_SESSION['uid']);
					}
					else{
						D("Member")->execute('update ss_member set daoju8="y",daoju8expire=daoju8expire+'.$duration.',spendcoin=spendcoin+'.$needcoin.',coinbalance=coinbalance-'.$needcoin.' where id='.$_SESSION['uid']);
					}
					//写入消费明细
					$Coindetail = D("Coindetail");
					$Coindetail->create();
					$Coindetail->type = 'expend';
					$Coindetail->action = 'buy';
					$Coindetail->uid = $_SESSION['uid'];
					$Coindetail->giftcount = 1;
					$Coindetail->content = '您购买了 '.$duration2.' 道具8';
					$Coindetail->objectIcon = '/Public/images/vip1.png';//名字前面显示的图标
					$Coindetail->coin = $needcoin;
					$Coindetail->addtime = time();
					$detailId = $Coindetail->add();
					
					echo '{"msg":"购买成功"}';
					exit;
				}
				break;
			case '13':
				if($_GET['toolsubid']  == 19){
					$needcoin = 50000;
					$duration = 3600 * 24 * 30 * 1;
					$duration2 = '一个月';
				}
				
				if($userinfo['coinbalance'] < $needcoin){
					echo '{"msg":"您的余额不足"}';
					exit;
				}
				else{
					if($userinfo['daoju9expire'] == 0){
						D("Member")->execute('update ss_member set daoju9="y",daoju9expire=daoju9expire+'.(time() + $duration).',spendcoin=spendcoin+'.$needcoin.',coinbalance=coinbalance-'.$needcoin.' where id='.$_SESSION['uid']);
					}
					else{
						D("Member")->execute('update ss_member set daoju9="y",daoju9expire=daoju9expire+'.$duration.',spendcoin=spendcoin+'.$needcoin.',coinbalance=coinbalance-'.$needcoin.' where id='.$_SESSION['uid']);
					}
					//写入消费明细
					$Coindetail = D("Coindetail");
					$Coindetail->create();
					$Coindetail->type = 'expend';
					$Coindetail->action = 'buy';
					$Coindetail->uid = $_SESSION['uid'];
					$Coindetail->giftcount = 1;
					$Coindetail->content = '您购买了 '.$duration2.' 道具9';
					$Coindetail->objectIcon = '/Public/images/vip1.png';//名字前面显示的图标
					$Coindetail->coin = $needcoin;
					$Coindetail->addtime = time();
					$detailId = $Coindetail->add();
					
					echo '{"msg":"购买成功"}';
					exit;
				}
				break;
			case '14':
				if($_GET['toolsubid']  == 20){
					$needcoin = 50000;
					$duration = 3600 * 24 * 30 * 1;
					$duration2 = '一个月';
				}
				
				if($userinfo['coinbalance'] < $needcoin){
					echo '{"msg":"您的余额不足"}';
					exit;
				}
				else{
					if($userinfo['daoju10expire'] == 0){
						D("Member")->execute('update ss_member set daoju10="y",daoju10expire=daoju10expire+'.(time() + $duration).',spendcoin=spendcoin+'.$needcoin.',coinbalance=coinbalance-'.$needcoin.' where id='.$_SESSION['uid']);
					}
					else{
						D("Member")->execute('update ss_member set daoju10="y",daoju10expire=daoju10expire+'.$duration.',spendcoin=spendcoin+'.$needcoin.',coinbalance=coinbalance-'.$needcoin.' where id='.$_SESSION['uid']);
					}
					//写入消费明细
					$Coindetail = D("Coindetail");
					$Coindetail->create();
					$Coindetail->type = 'expend';
					$Coindetail->action = 'buy';
					$Coindetail->uid = $_SESSION['uid'];
					$Coindetail->giftcount = 1;
					$Coindetail->content = '您购买了 '.$duration2.' 道具10';
					$Coindetail->objectIcon = '/Public/images/vip1.png';//名字前面显示的图标
					$Coindetail->coin = $needcoin;
					$Coindetail->addtime = time();
					$detailId = $Coindetail->add();
					
					echo '{"msg":"购买成功"}';
					exit;
				}
				break;
			case '15':
				if($_GET['toolsubid']  == 21){
					$needcoin = 50000;
					$duration = 3600 * 24 * 30 * 1;
					$duration2 = '一个月';
				}
				
				if($userinfo['coinbalance'] < $needcoin){
					echo '{"msg":"您的余额不足"}';
					exit;
				}
				else{
					if($userinfo['daoju11expire'] == 0){
						D("Member")->execute('update ss_member set daoju11="y",daoju11expire=daoju11expire+'.(time() + $duration).',spendcoin=spendcoin+'.$needcoin.',coinbalance=coinbalance-'.$needcoin.' where id='.$_SESSION['uid']);
					}
					else{
						D("Member")->execute('update ss_member set daoju11="y",daoju11expire=daoju11expire+'.$duration.',spendcoin=spendcoin+'.$needcoin.',coinbalance=coinbalance-'.$needcoin.' where id='.$_SESSION['uid']);
					}
					//写入消费明细
					$Coindetail = D("Coindetail");
					$Coindetail->create();
					$Coindetail->type = 'expend';
					$Coindetail->action = 'buy';
					$Coindetail->uid = $_SESSION['uid'];
					$Coindetail->giftcount = 1;
					$Coindetail->content = '您购买了 '.$duration2.' 道具11';
					$Coindetail->objectIcon = '/Public/images/vip1.png';//名字前面显示的图标
					$Coindetail->coin = $needcoin;
					$Coindetail->addtime = time();
					$detailId = $Coindetail->add();
					
					echo '{"msg":"购买成功"}';
					exit;
				}
				break;
			case '16':
				if($_GET['toolsubid']  == 22){
					$needcoin = 50000;
					$duration = 3600 * 24 * 30 * 1;
					$duration2 = '一个月';
				}
				
				if($userinfo['coinbalance'] < $needcoin){
					echo '{"msg":"您的余额不足"}';
					exit;
				}
				else{
					if($userinfo['daoju12expire'] == 0){
						D("Member")->execute('update ss_member set daoju12="y",daoju12expire=daoju12expire+'.(time() + $duration).',spendcoin=spendcoin+'.$needcoin.',coinbalance=coinbalance-'.$needcoin.' where id='.$_SESSION['uid']);
					}
					else{
						D("Member")->execute('update ss_member set daoju12="y",daoju12expire=daoju12expire+'.$duration.',spendcoin=spendcoin+'.$needcoin.',coinbalance=coinbalance-'.$needcoin.' where id='.$_SESSION['uid']);
					}
					//写入消费明细
					$Coindetail = D("Coindetail");
					$Coindetail->create();
					$Coindetail->type = 'expend';
					$Coindetail->action = 'buy';
					$Coindetail->uid = $_SESSION['uid'];
					$Coindetail->giftcount = 1;
					$Coindetail->content = '您购买了 '.$duration2.' 道具12';
					$Coindetail->objectIcon = '/Public/images/vip1.png';//名字前面显示的图标
					$Coindetail->coin = $needcoin;
					$Coindetail->addtime = time();
					$detailId = $Coindetail->add();
					
					echo '{"msg":"购买成功"}';
					exit;
				}
				break;
			case '17':
				if($_GET['toolsubid']  == 23){
					$needcoin = 50000;
					$duration = 3600 * 24 * 30 * 1;
					$duration2 = '一个月';
				}
				
				if($userinfo['coinbalance'] < $needcoin){
					echo '{"msg":"您的余额不足"}';
					exit;
				}
				else{
					if($userinfo['daoju13expire'] == 0){
						D("Member")->execute('update ss_member set daoju13="y",daoju13expire=daoju13expire+'.(time() + $duration).',spendcoin=spendcoin+'.$needcoin.',coinbalance=coinbalance-'.$needcoin.' where id='.$_SESSION['uid']);
					}
					else{
						D("Member")->execute('update ss_member set daoju13="y",daoju13expire=daoju13expire+'.$duration.',spendcoin=spendcoin+'.$needcoin.',coinbalance=coinbalance-'.$needcoin.' where id='.$_SESSION['uid']);
					}
					//写入消费明细
					$Coindetail = D("Coindetail");
					$Coindetail->create();
					$Coindetail->type = 'expend';
					$Coindetail->action = 'buy';
					$Coindetail->uid = $_SESSION['uid'];
					$Coindetail->giftcount = 1;
					$Coindetail->content = '您购买了 '.$duration2.' 道具13';
					$Coindetail->objectIcon = '/Public/images/vip1.png';//名字前面显示的图标
					$Coindetail->coin = $needcoin;
					$Coindetail->addtime = time();
					$detailId = $Coindetail->add();
					
					echo '{"msg":"购买成功"}';
					exit;
				}
				break;
			case '18':
				if($_GET['toolsubid']  == 24){
					$needcoin = 50000;
					$duration = 3600 * 24 * 30 * 1;
					$duration2 = '一个月';
				}
				
				if($userinfo['coinbalance'] < $needcoin){
					echo '{"msg":"您的余额不足"}';
					exit;
				}
				else{
					if($userinfo['daoju14expire'] == 0){
						D("Member")->execute('update ss_member set daoju14="y",daoju14expire=daoju14expire+'.(time() + $duration).',spendcoin=spendcoin+'.$needcoin.',coinbalance=coinbalance-'.$needcoin.' where id='.$_SESSION['uid']);
					}
					else{
						D("Member")->execute('update ss_member set daoju14="y",daoju14expire=daoju14expire+'.$duration.',spendcoin=spendcoin+'.$needcoin.',coinbalance=coinbalance-'.$needcoin.' where id='.$_SESSION['uid']);
					}
					//写入消费明细
					$Coindetail = D("Coindetail");
					$Coindetail->create();
					$Coindetail->type = 'expend';
					$Coindetail->action = 'buy';
					$Coindetail->uid = $_SESSION['uid'];
					$Coindetail->giftcount = 1;
					$Coindetail->content = '您购买了 '.$duration2.' 道具14';
					$Coindetail->objectIcon = '/Public/images/vip1.png';//名字前面显示的图标
					$Coindetail->coin = $needcoin;
					$Coindetail->addtime = time();
					$detailId = $Coindetail->add();
					
					echo '{"msg":"购买成功"}';
					exit;
				}
				break;
//新道具结束

		}
	}

	public function wishing_wishing(){
		C('HTML_CACHE_ON',false);
		if(!$_SESSION['uid'] || $_SESSION['uid'] < 0){
			$this->assign('jumpUrl',__APP__);
			$this->error('您尚未登录');
		}

		//礼物
		$gifts = D('Giftsort')->query('select * from ss_giftsort order by orderno asc');
		foreach($gifts as $n=> $val){
			$gifts[$n]['voo']=D("Gift")->where('sid='.$val['id'])->select();
		}
		$this->assign('gifts',$gifts);

		$this->display();
	}

	public function showadmin(){
		C('HTML_CACHE_ON',false);
		if(!$_SESSION['uid'] || $_SESSION['uid'] < 0){
			$this->assign('jumpUrl',__APP__);
			$this->error('您尚未登录');
		}

		$roomadmins = D("Roomadmin")->where("uid=".$_SESSION['uid'])->order('addtime desc')->select();
		foreach($roomadmins as $n=> $val){
			$roomadmins[$n]['voo']=D("Member")->where('id='.$val['adminuid'])->select();
		}
		$this->assign('roomadmins', $roomadmins);

		$this->display();
	}

	public function toggleEmceeShowAdmin(){
		C('HTML_CACHE_ON',false);
		if(!$_SESSION['uid'] || $_SESSION['uid'] < 0){
			$this->assign('jumpUrl',__APP__);
			$this->error('您尚未登录');
		}

		$myshowadmin = D("Roomadmin")->where('uid='.$_SESSION['uid'].' and adminuid='.$_REQUEST['userid'])->order('id asc')->select();
		if($myshowadmin){
			D("Roomadmin")->where('uid='.$_SESSION['uid'].' and adminuid='.$_REQUEST['userid'])->delete();
			echo '1';
			exit;
		}
		else{
			$Roomadmin=D("Roomadmin");
			$Roomadmin->create();
			$Roomadmin->uid = $_SESSION['uid'];
			$Roomadmin->adminuid = $_REQUEST['userid'];
			$Roomadmin->add();

			echo '0';
			exit;
		}
	}

	public function familyIJoin(){
		C('HTML_CACHE_ON',false);
		if(!$_SESSION['uid'] || $_SESSION['uid'] < 0){
			$this->assign('jumpUrl',__APP__);
			$this->error('您尚未登录');
		}

		$this->display();
	}

	public function familyICreate(){
		C('HTML_CACHE_ON',false);
		/*if(!$_SESSION['uid'] || $_SESSION['uid'] < 0){
			$this->assign('jumpUrl',__APP__);
			$this->error('您尚未登录');
		}*/

		$this->display();
	}

	public function familyBadge(){
		C('HTML_CACHE_ON',false);
		if(!$_SESSION['uid'] || $_SESSION['uid'] < 0){
			$this->assign('jumpUrl',__APP__);
			$this->error('您尚未登录');
		}

		$this->display();
	}

	public function familyPrerogative(){
		C('HTML_CACHE_ON',false);
		if(!$_SESSION['uid'] || $_SESSION['uid'] < 0){
			$this->assign('jumpUrl',__APP__);
			$this->error('您尚未登录');
		}

		$this->display();
	}

	public function familyOperationLog(){
		C('HTML_CACHE_ON',false);
		if(!$_SESSION['uid'] || $_SESSION['uid'] < 0){
			$this->assign('jumpUrl',__APP__);
			$this->error('您尚未登录');
		}

		$this->display();
	}

	public function interestByList(){
		C('HTML_CACHE_ON',false);
		if(!$_SESSION['uid'] || $_SESSION['uid'] < 0){
			$this->assign('jumpUrl',__APP__);
			$this->error('您尚未登录');
		}

		$Attention = D("Attention");
		$count = $Attention->where("attuid=".$_SESSION['uid'])->count();
		$listRows = 12;
		import("@.ORG.Page2");
		$p = new Page($count,$listRows,$linkFront);
		$attentions = $Attention->where("attuid=".$_SESSION['uid'])->limit($p->firstRow.",".$p->listRows)->order('addtime desc')->select();
		foreach($attentions as $n=> $val){
			$attentions[$n]['voo']=D("Member")->where('id='.$val['uid'])->select();
		}
		$page = $p->show();
		$this->assign('attentions',$attentions);
		$this->assign('count',$count);
		$this->assign('page',$page);

		//捧我的人
		$mypengusers = D('Coindetail')->query('SELECT uid,sum(coin) as total FROM `ss_coindetail` where type="expend" and uid>0 and touid='.$_SESSION['uid'].' group by uid order by total desc LIMIT 5');
		foreach($mypengusers as $n=> $val){
			$mypengusers[$n]['voo']=D("Member")->where('id='.$val['uid'])->select();
		}
		$this->assign('mypengusers', $mypengusers);

		$this->display();
	}

	public function info_edit(){
		C('HTML_CACHE_ON',false);
		if(!$_SESSION['uid'] || $_SESSION['uid'] < 0){
			$this->assign('jumpUrl',__APP__);
			$this->error('您尚未登录');
		}

		$userinfo = D("Member")->find($_SESSION['uid']);
		$this->assign('userinfo',$userinfo);

		$this->display();
	}

	public function do_info_edit(){
		C('HTML_CACHE_ON',false);
		if(!$_SESSION['uid'] || $_SESSION['uid'] < 0){
			$this->assign('jumpUrl',__APP__);
			$this->error('您尚未登录');
		}

		$User = D('Member');
		$vo = $User->create();
		if(!$vo) {
			$this->error($User->getError());
		}else{
			if($_POST['province'] != '请选择...'){
				$User->province = $_POST['province'];
			}
			if($_POST['city'] != '请选择...'){
				$User->city = $_POST['city'];
			}
			$User->save();

			session('nickname',$_POST["nickname"]);
			cookie('nickname',$_POST["nickname"],3600000);
			
			$this->assign('jumpUrl',__APP__.'/User/info_edit/');
			$this->success('保存成功');
		}
	}

	public function info_icon(){
		C('HTML_CACHE_ON',false);
		if(!$_SESSION['uid'] || $_SESSION['uid'] < 0){
			$this->assign('jumpUrl',__APP__);
			$this->error('您尚未登录');
		}

		$this->display();
	}

	public function info_changepass(){
		C('HTML_CACHE_ON',false);
		if(!$_SESSION['uid'] || $_SESSION['uid'] < 0){
			$this->assign('jumpUrl',__APP__);
			$this->error('您尚未登录');
		}

		$this->display();
	}

	public function do_info_changepass(){
		C('HTML_CACHE_ON',false);
		if(!$_SESSION['uid'] || $_SESSION['uid'] < 0){
			$this->assign('jumpUrl',__APP__);
			$this->error('您尚未登录');
		}

		$User = D('Member');
		$vo = $User->create();
		if(!$vo) {
			$this->error($User->getError());
		}else{
			if($_POST['newpass'] != ''){
				if($_POST['oldpass'] == ''){
					$this->error('原始密码不能为空');
				}
				if($_POST['newpass'] != $_POST['newpwd_1']){
					$this->error('两次新密码不一致');
				}
include './config.inc.php';
include './uc_client/client.php';
$ucresult = uc_user_edit($_SESSION['username'], $_POST['oldpass'], $_POST['newpass']);
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
			$User->password = md5($_POST['newpass']);
			$User->password2 = $this->pswencode($_POST['newpass']);
			$User->save();

			$this->assign('jumpUrl',__APP__."/User/info_changepass/");
			$this->success('修改成功');
		}
	}
	
	public function getGiftStat(){
		C('HTML_CACHE_ON',false);
		if(!$_SESSION['uid'] || $_SESSION['uid'] < 0){
			$this->assign('jumpUrl',__APP__);
			$this->error('您尚未登录');
		}

		$getgifts = D('Coindetail')->query('SELECT objectIcon,sum(giftcount) as total FROM `ss_coindetail` where type="expend" and action="sendgift" and touid='.$_SESSION['uid'].' group by giftid order by total desc');
		$this->assign('getgifts', $getgifts);

		$sendgifts = D('Coindetail')->query('SELECT objectIcon,sum(giftcount) as total FROM `ss_coindetail` where type="expend" and action="sendgift" and uid='.$_SESSION['uid'].' group by giftid order by total desc');
		$this->assign('sendgifts', $sendgifts);

		$this->display();
	}

	public function getTakedGift(){
		C('HTML_CACHE_ON',false);
		if(!$_SESSION['uid'] || $_SESSION['uid'] < 0){
			$this->assign('jumpUrl',__APP__);
			$this->error('您尚未登录');
		}

		if($_GET['begin'] != '' && $_GET['end'] != ''){
			$beginArr = explode("-", $_GET['begin']);
			$starttime = mktime(0,0,0,$beginArr[1],$beginArr[2],$beginArr[0]);
			$endArr = explode("-", $_GET['end']);
			$endtime = mktime(0,0,0,$endArr[1],$endArr[2],$endArr[0]);
			$condition = 'addtime>='.$starttime.' and addtime<='.$endtime;
		}
		else{
			$condition = 'date_format(FROM_UNIXTIME(addtime),"%m-%Y")=date_format(now(),"%m-%Y")';
		}

		$Coindetail = D("Coindetail");
		$count = $Coindetail->where('type="expend" and action="sendgift" and touid='.$_SESSION['uid'].' and '.$condition)->count();
		$listRows = 20;
		import("@.ORG.Page2");
		$p = new Page($count,$listRows,$linkFront);
		$getgifts = $Coindetail->where('type="expend" and action="sendgift" and touid='.$_SESSION['uid'].' and '.$condition)->limit($p->firstRow.",".$p->listRows)->order('addtime desc')->select();
		foreach($getgifts as $n=> $val){
			$getgifts[$n]['voo']=D("Member")->where('id='.$val['uid'])->select();
		}
		$page = $p->show();
		$this->assign('getgifts',$getgifts);
		$this->assign('count',$count);
		$pagecount = ceil($count/$listRows);
		if($pagecount == 0){$pagecount = 1;}
		$this->assign('pagecount',$pagecount);
		$this->assign('page',$page);

		$this->display();
	}

	public function getBuyedGift(){
		C('HTML_CACHE_ON',false);
		if(!$_SESSION['uid'] || $_SESSION['uid'] < 0){
			$this->assign('jumpUrl',__APP__);
			$this->error('您尚未登录');
		}

		if($_GET['begin'] != '' && $_GET['end'] != ''){
			$beginArr = explode("-", $_GET['begin']);
			$starttime = mktime(0,0,0,$beginArr[1],$beginArr[2],$beginArr[0]);
			$endArr = explode("-", $_GET['end']);
			$endtime = mktime(0,0,0,$endArr[1],$endArr[2],$endArr[0]);
			$condition = 'addtime>='.$starttime.' and addtime<='.$endtime;
		}
		else{
			$condition = 'date_format(FROM_UNIXTIME(addtime),"%m-%Y")=date_format(now(),"%m-%Y")';
		}

		$Coindetail = D("Coindetail");
		$count = $Coindetail->where('type="expend" and action="sendgift" and uid='.$_SESSION['uid'].' and '.$condition)->count();
		$listRows = 20;
		import("@.ORG.Page2");
		$p = new Page($count,$listRows,$linkFront);
		$sendgifts = $Coindetail->where('type="expend" and action="sendgift" and uid='.$_SESSION['uid'].' and '.$condition)->limit($p->firstRow.",".$p->listRows)->order('addtime desc')->select();
		foreach($sendgifts as $n=> $val){
			$sendgifts[$n]['voo']=D("Member")->where('id='.$val['touid'])->select();
		}
		$page = $p->show();
		$this->assign('sendgifts',$sendgifts);
		$this->assign('count',$count);
		$pagecount = ceil($count/$listRows);
		if($pagecount == 0){$pagecount = 1;}
		$this->assign('pagecount',$pagecount);
		$this->assign('page',$page);

		$this->display();
	}

	public function getConsume(){
		C('HTML_CACHE_ON',false);
		if(!$_SESSION['uid'] || $_SESSION['uid'] < 0){
			$this->assign('jumpUrl',__APP__);
			$this->error('您尚未登录');
		}

		if($_GET['begin'] != '' && $_GET['end'] != ''){
			$beginArr = explode("-", $_GET['begin']);
			$starttime = mktime(0,0,0,$beginArr[1],$beginArr[2],$beginArr[0]);
			$endArr = explode("-", $_GET['end']);
			$endtime = mktime(0,0,0,$endArr[1],$endArr[2],$endArr[0]);
			$condition = 'addtime>='.$starttime.' and addtime<='.$endtime;
		}
		else{
			$condition = 'date_format(FROM_UNIXTIME(addtime),"%m-%Y")=date_format(now(),"%m-%Y")';
		}

		$Coindetail = D("Coindetail");
		$count = $Coindetail->where('type="expend" and uid='.$_SESSION['uid'].' and '.$condition)->count();
		$listRows = 20;
		import("@.ORG.Page2");
		$p = new Page($count,$listRows,$linkFront);
		$consumes = $Coindetail->where('type="expend" and uid='.$_SESSION['uid'].' and '.$condition)->limit($p->firstRow.",".$p->listRows)->order('addtime desc')->select();
		$page = $p->show();
		$this->assign('consumes',$consumes);
		$this->assign('count',$count);
		$pagecount = ceil($count/$listRows);
		if($pagecount == 0){$pagecount = 1;}
		$this->assign('pagecount',$pagecount);
		$this->assign('page',$page);

		$this->display();
	}

	public function getPresentation(){
		C('HTML_CACHE_ON',false);
		if(!$_SESSION['uid'] || $_SESSION['uid'] < 0){
			$this->assign('jumpUrl',__APP__);
			$this->error('您尚未登录');
		}

		$Giveaway = D("Giveaway");
		$count = $Giveaway->where('uid=0 and touid='.$_SESSION['uid'])->count();
		$listRows = 10;
		import("@.ORG.Page3");
		$p = new Page($count,$listRows,$linkFront);
		$systemsendtome = $Giveaway->where('uid=0 and touid='.$_SESSION['uid'])->limit($p->firstRow.",".$p->listRows)->order('addtime desc')->select();
		$page = $p->show();
		$this->assign('systemsendtome',$systemsendtome);
		$this->assign('page',$page);

		$count2 = $Giveaway->where('uid>0 and touid='.$_SESSION['uid'])->count();
		$listRows2 = 10;
		import("@.ORG.Page4");
		$p = new Page4($count2,$listRows2,$linkFront);
		$othersendtome = $Giveaway->where('uid>0 and touid='.$_SESSION['uid'])->limit($p->firstRow.",".$p->listRows)->order('addtime desc')->select();
		foreach($othersendtome as $n=> $val){
			$othersendtome[$n]['voo']=D("Member")->where('id='.$val['uid'])->select();
		}
		$page2 = $p->show();
		$this->assign('othersendtome',$othersendtome);
		$this->assign('page2',$page2);

		$this->display();
	}

	public function getSystemPresentation(){
		C('HTML_CACHE_ON',false);
		if(!$_SESSION['uid'] || $_SESSION['uid'] < 0){
			$this->assign('jumpUrl',__APP__);
			$this->error('您尚未登录');
		}

		$Giveaway = D("Giveaway");
		$count = $Giveaway->where('uid=0 and touid='.$_SESSION['uid'])->count();
		$listRows = 10;
		import("@.ORG.Page3");
		$p = new Page($count,$listRows,$linkFront);
		$systemsendtome = $Giveaway->where('uid=0 and touid='.$_SESSION['uid'])->limit($p->firstRow.",".$p->listRows)->order('addtime desc')->select();
		$page = $p->show();
		$this->assign('systemsendtome',$systemsendtome);
		$this->assign('page',$page);
		
		$this->display();
	}

	public function getEmceenoPresentation(){
		C('HTML_CACHE_ON',false);
		if(!$_SESSION['uid'] || $_SESSION['uid'] < 0){
			$this->assign('jumpUrl',__APP__);
			$this->error('您尚未登录');
		}

		$Giveaway = D("Giveaway");
		$count2 = $Giveaway->where('uid>0 and touid='.$_SESSION['uid'])->count();
		$listRows2 = 10;
		import("@.ORG.Page4");
		$p = new Page4($count2,$listRows2,$linkFront);
		$othersendtome = $Giveaway->where('uid>0 and touid='.$_SESSION['uid'])->limit($p->firstRow.",".$p->listRows)->order('addtime desc')->select();
		foreach($othersendtome as $n=> $val){
			$othersendtome[$n]['voo']=D("Member")->where('id='.$val['uid'])->select();
		}
		$page2 = $p->show();
		$this->assign('othersendtome',$othersendtome);
		$this->assign('page2',$page2);

		$this->display();
	}

	public function getShowList(){
		C('HTML_CACHE_ON',false);
		if(!$_SESSION['uid'] || $_SESSION['uid'] < 0){
			$this->assign('jumpUrl',__APP__);
			$this->error('您尚未登录');
		}

		if($_GET['date'] != ''){
			$condition = 'date_format(FROM_UNIXTIME(starttime),"%Y%m")="'.$_GET['date'].'"';
		}
		else{
			$condition = 'date_format(FROM_UNIXTIME(starttime),"%m-%Y")=date_format(now(),"%m-%Y")';
		}

		$liverecords = D('Liverecord')->query('SELECT date_format(FROM_UNIXTIME(starttime),"%Y年%m月%d日") as livedate FROM `ss_liverecord` where uid='.$_SESSION['uid'].' and '.$condition.' group by livedate order by livedate desc');
		$this->assign('liverecords', $liverecords);

		$this->display();
	}

	public function listAward(){
		C('HTML_CACHE_ON',false);
		if(!$_SESSION['uid'] || $_SESSION['uid'] < 0){
			$this->assign('jumpUrl',__APP__);
			$this->error('您尚未登录');
		}

		$this->display();
	}

	public function bl_list(){
		C('HTML_CACHE_ON',false);
		if(!$_SESSION['uid'] || $_SESSION['uid'] < 0){
			$this->assign('jumpUrl',__APP__);
			$this->error('您尚未登录');
		}

		$this->display();
	}

	public function charge(){
		C('HTML_CACHE_ON',false);
		if(!$_SESSION['uid'] || $_SESSION['uid'] < 0){
			$this->assign('jumpUrl',__APP__);
			$this->error('您尚未登录');
		}

		if($_GET['ProxyUserID'] != ''){
			$proxyuserinfo = D("Member")->find($_GET['ProxyUserID']);
			if($proxyuserinfo){
				$proxyusername = $proxyuserinfo['nickname'];
				$proxyuserid = $proxyuserinfo['id'];
			}
			else{
				$proxyusername = '无';
				$proxyuserid = 0;
			}
			$this->assign('proxyusername', $proxyusername);
			$this->assign('proxyuserid', $proxyuserid);
		}

		$proxyusers = D('Member')->where('sellm="1"')->field('id,nickname')->order('id desc')->select();
		$this->assign('proxyusers', $proxyusers);

		$this->display();
	}

	public function ajaxcheckuser(){
		C('HTML_CACHE_ON',false);
		header("Content-type: text/html; charset=utf-8"); 
		$User = D("Member");
		if($_GET["roomnum"] == '')
		{
			exit;
		}
		else{
			$userinfo = $User->where('curroomnum='.$_GET["roomnum"].'')->select();
			if($userinfo){
				echo $userinfo[0]['nickname'];
			}
			else{
				exit;
			}
		}
	}

	public function chargepay(){
		C('HTML_CACHE_ON',false);
		header("Content-type: text/html; charset=utf-8"); 
		if(!$_SESSION['uid'] || $_SESSION['uid'] < 0){
			$this->assign('jumpUrl',__APP__);
			$this->error('您尚未登录');
		}

		if($_POST['c_ChargeType'] == '1'){
			$chargetouid = $_SESSION['uid'];
		}	
		else{
			$touserinfo = D("Member")->where('curroomnum='.$_POST["c_DestUserName"].'')->select();
			if($touserinfo){
				$chargetouid = $touserinfo[0]['id'];
			}
			else{
				$chargetouid = $_SESSION['uid'];
			}
		}

		if($_POST['c_PPPayID'] == '1_ICBC-NET-B2C'){
			$merchantAcctId=$this->bill_MerchantAcctID;
			$key=$this->bill_key;
			$inputCharset="1";
			$bgUrl=$this->siteurl ."/index.php/User/payreceive/";
			$version="v2.0";
			$language="1";
			$signType="1";	
			$payerName=$_SESSION['username'];
			$payerContactType="1";
			$payerContact="";
			$orderId=date('YmdHis');
			$orderAmount=$_POST['c_Money1'] * 100;
			$orderTime=date('YmdHis');
			$productName=$this->sitename."在线充值";
			$productNum="1";
			$productId="";
			$productDesc=$this->sitename."在线充值";
			$ext1="";
			$ext2="";
			$payType="00";
			$redoFlag="0";
			$pid="";

			$signMsgVal=$this->appendParam($signMsgVal,"inputCharset",$inputCharset);
			$signMsgVal=$this->appendParam($signMsgVal,"bgUrl",$bgUrl);
			$signMsgVal=$this->appendParam($signMsgVal,"version",$version);
			$signMsgVal=$this->appendParam($signMsgVal,"language",$language);
			$signMsgVal=$this->appendParam($signMsgVal,"signType",$signType);
			$signMsgVal=$this->appendParam($signMsgVal,"merchantAcctId",$merchantAcctId);
			$signMsgVal=$this->appendParam($signMsgVal,"payerName",$payerName);
			$signMsgVal=$this->appendParam($signMsgVal,"payerContactType",$payerContactType);
			$signMsgVal=$this->appendParam($signMsgVal,"payerContact",$payerContact);
			$signMsgVal=$this->appendParam($signMsgVal,"orderId",$orderId);
			$signMsgVal=$this->appendParam($signMsgVal,"orderAmount",$orderAmount);
			$signMsgVal=$this->appendParam($signMsgVal,"orderTime",$orderTime);
			$signMsgVal=$this->appendParam($signMsgVal,"productName",$productName);
			$signMsgVal=$this->appendParam($signMsgVal,"productNum",$productNum);
			$signMsgVal=$this->appendParam($signMsgVal,"productId",$productId);
			$signMsgVal=$this->appendParam($signMsgVal,"productDesc",$productDesc);
			$signMsgVal=$this->appendParam($signMsgVal,"ext1",$ext1);
			$signMsgVal=$this->appendParam($signMsgVal,"ext2",$ext2);
			$signMsgVal=$this->appendParam($signMsgVal,"payType",$payType);	
			$signMsgVal=$this->appendParam($signMsgVal,"redoFlag",$redoFlag);
			$signMsgVal=$this->appendParam($signMsgVal,"pid",$pid);
			$signMsgVal=$this->appendParam($signMsgVal,"key",$key);
			$signMsg= strtoupper(md5($signMsgVal));
		
			$Chargedetail = D("Chargedetail");
			$Chargedetail->create();
			$Chargedetail->uid = $_SESSION['uid'];
			$Chargedetail->touid = $chargetouid;
			$Chargedetail->rmb = $_POST['c_Money1'];
			$Chargedetail->coin = $_POST['c_Money1'] * $this->ratio;
			$Chargedetail->status = '订购未完成';
			$Chargedetail->addtime = time();
			$Chargedetail->orderno = $orderId;
			if($_GET['ProxyUserID'] != ''){
				$Chargedetail->proxyuid = $_GET['ProxyUserID'];
			}
			$detailId = $Chargedetail->add();
		
			echo '<form name="kqPay" method="post" action="https://www.99bill.com/gateway/recvMerchantInfoAction.htm">';
			echo '	<input type="hidden" name="inputCharset" value="'.$inputCharset.'"/>';
			echo '	<input type="hidden" name="bgUrl" value="'.$bgUrl.'"/>';
			echo '	<input type="hidden" name="version" value="'.$version.'"/>';
			echo '	<input type="hidden" name="language" value="'.$language.'"/>';
			echo '	<input type="hidden" name="signType" value="'.$signType.'"/>';

			echo '	<input type="hidden" name="signMsg" value="'.$signMsg.'"/>';
			echo '	<input type="hidden" name="merchantAcctId" value="'.$merchantAcctId.'"/>';
			echo '	<input type="hidden" name="payerName" value="'.$payerName.'"/>';
			echo '	<input type="hidden" name="payerContactType" value="'.$payerContactType.'"/>';
			echo '	<input type="hidden" name="payerContact" value="'.$payerContact.'"/>';
			echo '	<input type="hidden" name="orderId" value="'.$orderId.'"/>';
			echo '	<input type="hidden" name="orderAmount" value="'.$orderAmount.'"/>';
			echo '	<input type="hidden" name="orderTime" value="'.$orderTime.'"/>';
			echo '	<input type="hidden" name="productName" value="'.$productName.'"/>';
			echo '	<input type="hidden" name="productNum" value="'.$productNum.'"/>';
			echo '	<input type="hidden" name="productId" value="'.$productId.'"/>';
			echo '	<input type="hidden" name="productDesc" value="'.$productDesc.'"/>';
			echo '	<input type="hidden" name="ext1" value="'.$ext1.'"/>';
			echo '	<input type="hidden" name="ext2" value="'.$ext2.'"/>';
			echo '	<input type="hidden" name="payType" value="'.$payType.'"/>';
			echo '	<input type="hidden" name="redoFlag" value="'.$redoFlag.'"/>';
			echo '	<input type="hidden" name="pid" value="'.$pid.'"/>';

			echo '</form>';

			echo '<script type="text/javascript">';
			echo "	document.forms['kqPay'].submit();";
			echo '</script>';
		}

		if($_POST['c_PPPayID'] == '14_SZX-NET' || $_POST['c_PPPayID'] == '17_JIUYOU-NET'){
			if($_POST['c_PPPayID'] == '14_SZX-NET'){
				if($_POST['paycardType'] == 'chinamobile'){
					$merchantAcctId="1002225194002";
					$key="J6B5GECXJTK7CJFS";
				}
				if($_POST['paycardType'] == 'chinaunion'){
					$merchantAcctId="1002225194003";
					$key="5CD8UKG7I8LGRWCM";
				}
				if($_POST['paycardType'] == 'chinatelecom'){
					$merchantAcctId="1002225194004";
					$key="LH4RAD7NXSDNYF5B";
				}
			}
			if($_POST['c_PPPayID'] == '17_JIUYOU-NET'){
				if($_POST['gamecardType'] == 'zongyou'){
					$merchantAcctId="1002225194010";
					$key="54HHYTGSII9ZW2HW";
				}
				if($_POST['gamecardType'] == 'netease'){
					$merchantAcctId="1002225194009";
					$key="YF6MWZW4Q35EXEQX";
				}
				if($_POST['gamecardType'] == 'sohu'){
					$merchantAcctId="1002225194008";
					$key="YDI8US7J97FSKR7F";
				}
				if($_POST['gamecardType'] == 'wanmei'){
					$merchantAcctId="1002225194007";
					$key="7S94QYTU4EXWUUF8";
				}
				if($_POST['gamecardType'] == 'snda'){
					$merchantAcctId="1002225194006";
					$key="Z2HYNHZYR4GRFMNS";
				}
				if($_POST['gamecardType'] == 'junnet'){
					$merchantAcctId="1002225194005";
					$key="SDD9JIUHJFNQJK7J";
				}
			}
			$inputCharset="1";
			$bgUrl=$this->siteurl ."/index.php/User/card_payreceive/";
			$pageUrl="";
			$version="v2.0";
			$language="1";
			$signType="1";	
			$payerName=$_SESSION['username'];
			$payerContactType="1";
			$payerContact="";
			$orderId=date('YmdHis');
			$orderAmount=$_POST['c_Money1'] * 100;
			$payType="42";
			//$cardNumber=$this->encrypt($_POST['paycard_num'],$key);
			//$cardPwd=$this->encrypt($_POST['paycard_psw'],$key);
			$cardNumber="";
			$cardPwd="";
			$fullAmountFlag="0";
			$orderTime=date('YmdHis');
			$productName=urlencode($this->sitename.'在线充值');
			$productNum="1";
			$productId="";
			$productDesc=urlencode($this->sitename.'在线充值');
			$ext1="";
			$ext2="";
			if($_POST['c_PPPayID'] == '14_SZX-NET'){
				if($_POST['paycardType'] == 'chinamobile'){
					$bossType="0";
				}
				if($_POST['paycardType'] == 'chinaunion'){
					$bossType="1";
				}
				if($_POST['paycardType'] == 'chinatelecom'){
					$bossType="3";
				}
			}
			if($_POST['c_PPPayID'] == '17_JIUYOU-NET'){
				if($_POST['gamecardType'] == 'zongyou'){
					$bossType="15";
				}
				if($_POST['gamecardType'] == 'netease'){
					$bossType="14";
				}
				if($_POST['gamecardType'] == 'sohu'){
					$bossType="13";
				}
				if($_POST['gamecardType'] == 'wanmei'){
					$bossType="12";
				}
				if($_POST['gamecardType'] == 'snda'){
					$bossType="10";
				}
				if($_POST['gamecardType'] == 'junnet'){
					$bossType="4";
				}
			}
			//echo $merchantAcctId.'|'.$key.'|'.$bossType.'|'.$_POST['gamecardType'];
			//exit;
			//$bossType="9";

			$signMsgVal=$this->appendParam($signMsgVal,"inputCharset",$inputCharset);
			$signMsgVal=$this->appendParam($signMsgVal,"bgUrl",$bgUrl);
			$signMsgVal=$this->appendParam($signMsgVal,"pageUrl",$pageUrl);
			$signMsgVal=$this->appendParam($signMsgVal,"version",$version);
			$signMsgVal=$this->appendParam($signMsgVal,"language",$language);
			$signMsgVal=$this->appendParam($signMsgVal,"signType",$signType);
			$signMsgVal=$this->appendParam($signMsgVal,"merchantAcctId",$merchantAcctId);
			$signMsgVal=$this->appendParam($signMsgVal,"payerName",$payerName);
			$signMsgVal=$this->appendParam($signMsgVal,"payerContactType",$payerContactType);
			$signMsgVal=$this->appendParam($signMsgVal,"payerContact",$payerContact);
			$signMsgVal=$this->appendParam($signMsgVal,"orderId",$orderId);
			$signMsgVal=$this->appendParam($signMsgVal,"orderAmount",$orderAmount);
			$signMsgVal=$this->appendParam($signMsgVal,"payType",$payType);
			$signMsgVal=$this->appendParam($signMsgVal,"cardNumber",$cardNumber);
			$signMsgVal=$this->appendParam($signMsgVal,"cardPwd",$cardPwd);
			$signMsgVal=$this->appendParam($signMsgVal,"fullAmountFlag",$fullAmountFlag);
			$signMsgVal=$this->appendParam($signMsgVal,"orderTime",$orderTime);
			$signMsgVal=$this->appendParam($signMsgVal,"productName",$productName);
			$signMsgVal=$this->appendParam($signMsgVal,"productNum",$productNum);
			$signMsgVal=$this->appendParam($signMsgVal,"productId",$productId);
			$signMsgVal=$this->appendParam($signMsgVal,"productDesc",$productDesc);
			$signMsgVal=$this->appendParam($signMsgVal,"ext1",$ext1);
			$signMsgVal=$this->appendParam($signMsgVal,"ext2",$ext2);
			$signMsgVal=$this->appendParam($signMsgVal,"bossType",$bossType);
			$signMsgVal=$this->appendParam($signMsgVal,"key",$key);
			//echo $signMsgVal;
			//exit;
			$signMsg= strtoupper(md5($signMsgVal));
		
			$Chargedetail = D("Chargedetail");
			$Chargedetail->create();
			$Chargedetail->uid = $_SESSION['uid'];
			$Chargedetail->touid = $chargetouid;
			$Chargedetail->rmb = $_POST['c_Money1'];
			$Chargedetail->coin = $_POST['c_Money1'] * $this->ratio;
			$Chargedetail->status = '订购未完成';
			$Chargedetail->addtime = time();
			$Chargedetail->orderno = $orderId;
			if($_GET['ProxyUserID'] != ''){
				$Chargedetail->proxyuid = $_GET['ProxyUserID'];
			}
			$detailId = $Chargedetail->add();
		
			echo '<form name="kqPay" method="post" action="http://www.99bill.com/szxgateway/recvMerchantInfoAction.htm">';
			echo '	<input type="hidden" name="inputCharset" value="'.$inputCharset.'"/>';
			echo '	<input type="hidden" name="bgUrl" value="'.$bgUrl.'"/>';
			echo '	<input type="hidden" name="pageUrl" value="'.$pageUrl.'">';
			echo '	<input type="hidden" name="version" value="'.$version.'"/>';
			echo '	<input type="hidden" name="language" value="'.$language.'"/>';
			echo '	<input type="hidden" name="signType" value="'.$signType.'"/>';
			echo '	<input type="hidden" name="merchantAcctId" value="'.$merchantAcctId.'"/>';
			echo '	<input type="hidden" name="payerName" value="'.$payerName.'"/>';
			echo '	<input type="hidden" name="payerContactType" value="'.$payerContactType.'"/>';
			echo '	<input type="hidden" name="payerContact" value="'.$payerContact.'"/>';
			echo '	<input type="hidden" name="orderId" value="'.$orderId.'"/>';
			echo '	<input type="hidden" name="orderAmount" value="'.$orderAmount.'"/>';
			echo '	<input type="hidden" name="payType" value="'.$payType.'"/>';
			echo '	<input type="hidden" name="cardNumber" value="'.$cardNumber.'">';
			echo '	<input type="hidden" name="cardPwd" value="'.$cardPwd.'">';
			echo '	<input type="hidden" name="fullAmountFlag" value="'.$fullAmountFlag.'">';
			echo '	<input type="hidden" name="orderTime" value="'.$orderTime.'"/>';
			echo '	<input type="hidden" name="productName" value="'.$productName.'"/>';
			echo '	<input type="hidden" name="productNum" value="'.$productNum.'"/>';
			echo '	<input type="hidden" name="productId" value="'.$productId.'"/>';
			echo '	<input type="hidden" name="productDesc" value="'.$productDesc.'"/>';
			echo '	<input type="hidden" name="ext1" value="'.$ext1.'"/>';
			echo '	<input type="hidden" name="ext2" value="'.$ext2.'"/>';
			echo '	<input type="hidden" name="bossType" value="'.$bossType.'"/>';
			echo '	<input type="hidden" name="signMsg" value="'.$signMsg.'"/>';

			echo '</form>';

			echo '<script type="text/javascript">';
			echo "	document.forms['kqPay'].submit();";
			echo '</script>';
		}

	}

	public function dumppost(){
		dump($_POST);
	}

	public function payreceive(){
		C('HTML_CACHE_ON',false);

		$merchantAcctId=trim($_REQUEST['merchantAcctId']);
		$key=$this->bill_key;
		$version=trim($_REQUEST['version']);
		$language=trim($_REQUEST['language']);
		$signType=trim($_REQUEST['signType']);
		$payType=trim($_REQUEST['payType']);
		$bankId=trim($_REQUEST['bankId']);
		$orderId=trim($_REQUEST['orderId']);
		$orderTime=trim($_REQUEST['orderTime']);
		$orderAmount=trim($_REQUEST['orderAmount']);
		$dealId=trim($_REQUEST['dealId']);
		$bankDealId=trim($_REQUEST['bankDealId']);
		$dealTime=trim($_REQUEST['dealTime']);
		$payAmount=trim($_REQUEST['payAmount']);
		$fee=trim($_REQUEST['fee']);
		$ext1=trim($_REQUEST['ext1']);
		$ext2=trim($_REQUEST['ext2']);
		$payResult=trim($_REQUEST['payResult']);
		$errCode=trim($_REQUEST['errCode']);
		$signMsg=trim($_REQUEST['signMsg']);

		$merchantSignMsgVal=$this->appendParam($merchantSignMsgVal,"merchantAcctId",$merchantAcctId);
		$merchantSignMsgVal=$this->appendParam($merchantSignMsgVal,"version",$version);
		$merchantSignMsgVal=$this->appendParam($merchantSignMsgVal,"language",$language);
		$merchantSignMsgVal=$this->appendParam($merchantSignMsgVal,"signType",$signType);
		$merchantSignMsgVal=$this->appendParam($merchantSignMsgVal,"payType",$payType);
		$merchantSignMsgVal=$this->appendParam($merchantSignMsgVal,"bankId",$bankId);
		$merchantSignMsgVal=$this->appendParam($merchantSignMsgVal,"orderId",$orderId);
		$merchantSignMsgVal=$this->appendParam($merchantSignMsgVal,"orderTime",$orderTime);
		$merchantSignMsgVal=$this->appendParam($merchantSignMsgVal,"orderAmount",$orderAmount);
		$merchantSignMsgVal=$this->appendParam($merchantSignMsgVal,"dealId",$dealId);
		$merchantSignMsgVal=$this->appendParam($merchantSignMsgVal,"bankDealId",$bankDealId);
		$merchantSignMsgVal=$this->appendParam($merchantSignMsgVal,"dealTime",$dealTime);
		$merchantSignMsgVal=$this->appendParam($merchantSignMsgVal,"payAmount",$payAmount);
		$merchantSignMsgVal=$this->appendParam($merchantSignMsgVal,"fee",$fee);
		$merchantSignMsgVal=$this->appendParam($merchantSignMsgVal,"ext1",$ext1);
		$merchantSignMsgVal=$this->appendParam($merchantSignMsgVal,"ext2",$ext2);
		$merchantSignMsgVal=$this->appendParam($merchantSignMsgVal,"payResult",$payResult);
		$merchantSignMsgVal=$this->appendParam($merchantSignMsgVal,"errCode",$errCode);
		$merchantSignMsgVal=$this->appendParam($merchantSignMsgVal,"key",$key);
		$merchantSignMsg= md5($merchantSignMsgVal);

		$rtnOk=0;
		$rtnUrl="";

		if(strtoupper($signMsg)==strtoupper($merchantSignMsg)){
			switch($payResult){
				case "10":
					$chargeinfo = D("Chargedetail")->where('orderno="'.$orderId.'"')->select();
					if($chargeinfo && $chargeinfo[0]['status'] == '订购未完成'){
						D("Chargedetail")->execute('update ss_chargedetail set dealId="'.$dealId.'",status="订购完成" where orderno="'.$orderId.'"');
						D("Member")->execute('update ss_member set coinbalance=coinbalance+'.(($payAmount/100)*$this->ratio).' where id='.$chargeinfo[0]['touid']);
						if($chargeinfo[0]['touid'] != $chargeinfo[0]['uid']){
							$Giveaway = D("Giveaway");
							$Giveaway->create();
							$Giveaway->uid = $chargeinfo[0]['uid'];
							$Giveaway->touid = $chargeinfo[0]['touid'];
							$Giveaway->content = (($payAmount/100)*$this->ratio).'梦想币';
							$Giveaway->objectIcon = '/Public/images/coin.png';
							$giveId = $Giveaway->add();
						}
						//充值代理
						if($chargeinfo[0]['proxyuid'] != 0){
							$beannum = ceil((($payAmount/100)*$this->ratio) * ($this->payagentdeduct / 100));
							//D("Member")->execute('update ss_member set earnbean=earnbean+'.$beannum.',beanbalance=beanbalance+'.$beannum.' where id='.$chargeinfo[0]['proxyuid']);
							D("Member")->execute('update ss_member set beanbalance3=beanbalance3+'.$beannum.' where id='.$chargeinfo[0]['proxyuid']);
							$Payagentbeandetail = D("Payagentbeandetail");
							$Payagentbeandetail->create();
							$Payagentbeandetail->type = 'income';
							$Payagentbeandetail->action = 'charge';
							$Payagentbeandetail->uid = $chargeinfo[0]['proxyuid'];
							$Payagentbeandetail->content = '充值代理收入';
							$Payagentbeandetail->bean = $beannum;
							$Payagentbeandetail->addtime = time();
							$detailId = $Payagentbeandetail->add();
						}
					}
					
					$rtnOk=1;
					$rtnUrl=$this->siteurl."/index.php/User/payresult/type/success/";
					break;
				default:
					$rtnOk=1;
					$rtnUrl=$this->siteurl."/index.php/User/payresult/type/error/";
					break;
			}
		}
		else{
			$rtnOk=1;
			$rtnUrl=$this->siteurl."/index.php/User/payresult/type/error/";
		}

		echo '<result>'.$rtnOk.'</result><redirecturl>'.$rtnUrl.'</redirecturl>';
		exit;
	}

	public function card_payreceive(){
		C('HTML_CACHE_ON',false);

		$merchantAcctId=trim($_REQUEST['merchantAcctId']);
		if($_REQUEST['merchantAcctId'] == '1002225194010'){
			$key='54HHYTGSII9ZW2HW';
		}
		if($_REQUEST['merchantAcctId'] == '1002225194009'){
			$key='YF6MWZW4Q35EXEQX';
		}
		if($_REQUEST['merchantAcctId'] == '1002225194008'){
			$key='YDI8US7J97FSKR7F';
		}
		if($_REQUEST['merchantAcctId'] == '1002225194007'){
			$key='7S94QYTU4EXWUUF8';
		}
		if($_REQUEST['merchantAcctId'] == '1002225194006'){
			$key='Z2HYNHZYR4GRFMNS';
		}
		if($_REQUEST['merchantAcctId'] == '1002225194004'){
			$key='LH4RAD7NXSDNYF5B';
		}
		if($_REQUEST['merchantAcctId'] == '1002225194005'){
			$key='SDD9JIUHJFNQJK7J';
		}
		if($_REQUEST['merchantAcctId'] == '1002225194003'){
			$key='5CD8UKG7I8LGRWCM';
		}
		if($_REQUEST['merchantAcctId'] == '1002225194002'){
			$key='J6B5GECXJTK7CJFS';
		}
		$version=trim($_REQUEST['version']);
		$language=trim($_REQUEST['language']);
		$payType=trim($_REQUEST['payType']);
		$cardNumber=trim($_REQUEST['cardNumber']);
		$cardPwd=trim($_REQUEST['cardPwd']);
		$orderId=trim($_REQUEST['orderId']);
		$orderAmount=trim($_REQUEST['orderAmount']);
		$dealId=trim($_REQUEST['dealId']);
		$orderTime=trim($_REQUEST['orderTime']);
		$ext1=trim($_REQUEST['ext1']);
		$ext2=trim($_REQUEST['ext2']);
		$payAmount=trim($_REQUEST['payAmount']);
		$billOrderTime=trim($_REQUEST['billOrderTime']);
		$payResult=trim($_REQUEST['payResult']);
		$bossType=trim($_REQUEST['bossType']);
		$receiveBossType=trim($_REQUEST['receiveBossType']);
		$receiverAcctId=trim($_REQUEST['receiverAcctId']);
		$signType=trim($_REQUEST['signType']);
		$signMsg=trim($_REQUEST['signMsg']);

		$merchantSignMsgVal=$this->appendParam($merchantSignMsgVal,"merchantAcctId",$merchantAcctId);
		$merchantSignMsgVal=$this->appendParam($merchantSignMsgVal,"version",$version);
		$merchantSignMsgVal=$this->appendParam($merchantSignMsgVal,"language",$language);
		$merchantSignMsgVal=$this->appendParam($merchantSignMsgVal,"payType",$payType);
		$merchantSignMsgVal=$this->appendParam($merchantSignMsgVal,"cardNumber",$cardNumber);
		$merchantSignMsgVal=$this->appendParam($merchantSignMsgVal,"cardPwd",$cardPwd);
		$merchantSignMsgVal=$this->appendParam($merchantSignMsgVal,"orderId",$orderId);
		$merchantSignMsgVal=$this->appendParam($merchantSignMsgVal,"orderAmount",$orderAmount);
		$merchantSignMsgVal=$this->appendParam($merchantSignMsgVal,"dealId",$dealId);
		$merchantSignMsgVal=$this->appendParam($merchantSignMsgVal,"orderTime",$orderTime);
		$merchantSignMsgVal=$this->appendParam($merchantSignMsgVal,"ext1",$ext1);
		$merchantSignMsgVal=$this->appendParam($merchantSignMsgVal,"ext2",$ext2);
		$merchantSignMsgVal=$this->appendParam($merchantSignMsgVal,"payAmount",$payAmount);
		$merchantSignMsgVal=$this->appendParam($merchantSignMsgVal,"billOrderTime",$billOrderTime);
		$merchantSignMsgVal=$this->appendParam($merchantSignMsgVal,"payResult",$payResult);
		$merchantSignMsgVal=$this->appendParam($merchantSignMsgVal,"signType",$signType);
		$merchantSignMsgVal=$this->appendParam($merchantSignMsgVal,"bossType",$bossType);
		$merchantSignMsgVal=$this->appendParam($merchantSignMsgVal,"receiveBossType",$receiveBossType);
		$merchantSignMsgVal=$this->appendParam($merchantSignMsgVal,"receiverAcctId",$receiverAcctId);
		$merchantSignMsgVal=$this->appendParam($merchantSignMsgVal,"key",$key);
		
		$merchantSignMsg= md5($merchantSignMsgVal);

		$rtnOk=0;
		$rtnUrl="";

		if(strtoupper($signMsg)==strtoupper($merchantSignMsg)){
			switch($payResult){
				case "10":
					$chargeinfo = D("Chargedetail")->where('orderno="'.$orderId.'"')->select();
					if($chargeinfo && $chargeinfo[0]['status'] == '订购未完成'){
						D("Chargedetail")->execute('update ss_chargedetail set dealId="'.$dealId.'",status="订购完成" where orderno="'.$orderId.'"');
						D("Member")->execute('update ss_member set coinbalance=coinbalance+'.(($payAmount/100)*$this->ratio).' where id='.$chargeinfo[0]['touid']);
						if($chargeinfo[0]['touid'] != $chargeinfo[0]['uid']){
							$Giveaway = D("Giveaway");
							$Giveaway->create();
							$Giveaway->uid = $chargeinfo[0]['uid'];
							$Giveaway->touid = $chargeinfo[0]['touid'];
							$Giveaway->content = (($payAmount/100)*$this->ratio).'梦想币';
							$Giveaway->objectIcon = '/Public/images/coin.png';
							$giveId = $Giveaway->add();
						}
						//充值代理
						if($chargeinfo[0]['proxyuid'] != 0){
							$beannum = ceil((($payAmount/100)*$this->ratio) * ($this->payagentdeduct / 100));
							//D("Member")->execute('update ss_member set earnbean=earnbean+'.$beannum.',beanbalance=beanbalance+'.$beannum.' where id='.$chargeinfo[0]['proxyuid']);
							D("Member")->execute('update ss_member set beanbalance3=beanbalance3+'.$beannum.' where id='.$chargeinfo[0]['proxyuid']);
							$Payagentbeandetail = D("Payagentbeandetail");
							$Payagentbeandetail->create();
							$Payagentbeandetail->type = 'income';
							$Payagentbeandetail->action = 'charge';
							$Payagentbeandetail->uid = $chargeinfo[0]['proxyuid'];
							$Payagentbeandetail->content = '充值代理收入';
							$Payagentbeandetail->bean = $beannum;
							$Payagentbeandetail->addtime = time();
							$detailId = $Payagentbeandetail->add();
						}
					}
					
					$rtnOk=1;
					$rtnUrl=$this->siteurl."/index.php/User/payresult/type/success/";
					break;
				default:
					$rtnOk=1;
					$rtnUrl=$this->siteurl."/index.php/User/payresult/type/error/";
					break;
			}
		}
		else{
			$rtnOk=1;
			$rtnUrl=$this->siteurl."/index.php/User/payresult/type/error/";
		}

		echo '<result>'.$rtnOk.'</result><redirecturl>'.$rtnUrl.'</redirecturl>';
		exit;
	}

	public function payresult(){
		C('HTML_CACHE_ON',false);
		header("Content-type: text/html; charset=utf-8");
		if($_GET['type'] == 'success'){
			echo '充值成功 <a href="'.__URL__.'/chargelist/">返回</a>';
		}
		else{
			echo '充值失败 <a href="'.__URL__.'/chargelist/">返回</a>';
		}
	}

	private function appendParam($returnStr,$paramId,$paramValue){
		C('HTML_CACHE_ON',false);

		if($returnStr!=""){
			if($paramValue!=""){	
				$returnStr.="&".$paramId."=".$paramValue;
			}
		}else{
			If($paramValue!=""){
				$returnStr=$paramId."=".$paramValue;
			}
		}
		
		return $returnStr;
	}

	public function encrypt($encrypt,$key="") {
		$iv = mcrypt_create_iv ( mcrypt_get_iv_size ( MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB ), MCRYPT_RAND );
		$passcrypt = mcrypt_encrypt ( MCRYPT_RIJNDAEL_256, $key, $encrypt, MCRYPT_MODE_ECB, $iv );
		$encode = base64_encode ( $passcrypt );
		return $encode;
	}

	public function decrypt($decrypt,$key="") {
		$decoded = base64_decode ( $decrypt );
		$iv = mcrypt_create_iv ( mcrypt_get_iv_size ( MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB ), MCRYPT_RAND );
		$decrypted = mcrypt_decrypt ( MCRYPT_RIJNDAEL_256, $key, $decoded, MCRYPT_MODE_ECB, $iv );
		return $decrypted;
	}

	public function userbalance(){
		C('HTML_CACHE_ON',false);
		if(!$_SESSION['uid'] || $_SESSION['uid'] < 0){
			$this->assign('jumpUrl',__APP__);
			$this->error('您尚未登录');
		}

		$this->display();
	}

	public function chargelist(){
		C('HTML_CACHE_ON',false);
		if(!$_SESSION['uid'] || $_SESSION['uid'] < 0){
			$this->assign('jumpUrl',__APP__);
			$this->error('您尚未登录');
		}

		$Chargedetail = D("Chargedetail");
		$condition = "uid=".$_SESSION['uid'];
		if($_GET['c_StartTime'] != ''){
			$timeArr = explode("-", $_GET['c_StartTime']);
			$unixtime = mktime(0,0,0,$timeArr[1],$timeArr[2],$timeArr[0]);
			$condition .= ' and addtime>='.$unixtime;
		}
		if($_GET['c_EndTime'] != ''){
			$timeArr = explode("-", $_GET['c_EndTime']);
			$unixtime = mktime(23,59,59,$timeArr[1],$timeArr[2],$timeArr[0]);
			$condition .= ' and addtime<='.$unixtime;
		}

		$count = $Chargedetail->where($condition)->count();
		$listRows = 20;
		import("@.ORG.Page");
		$p = new Page($count,$listRows,$linkFront);
		$charges = $Chargedetail->where($condition)->limit($p->firstRow.",".$p->listRows)->order('addtime desc')->select();
		foreach($charges as $n=> $val){
			$charges[$n]['voo']=D("Member")->where('id='.$val['touid'])->select();
		}
		$page = $p->show();
		$this->assign('charges',$charges);
		$this->assign('count',$count);
		$pagecount = ceil($count/$listRows);
		if($pagecount == 0){$pagecount = 1;}
		$this->assign('pagecount',$pagecount);
		$this->assign('page',$page);

		$totalcharge = D("Chargedetail")->query('select sum(rmb) as total from ss_chargedetail where uid='.$_SESSION['uid'].' and status="订购完成"');
		if($totalcharge[0]['total'] != ''){
			$totalpay = $totalcharge[0]['total'];
		}
		else{
			$totalpay = 0;
		}
		$this->assign('totalpay',$totalpay);

		$this->display();
	}

	public function securityset(){
		C('HTML_CACHE_ON',false);
		if(!$_SESSION['uid'] || $_SESSION['uid'] < 0){
			$this->assign('jumpUrl',__APP__);
			$this->error('您尚未登录');
		}

		$this->display();
	}

	public function securitypassbind(){
		C('HTML_CACHE_ON',false);
		if(!$_SESSION['uid'] || $_SESSION['uid'] < 0){
			$this->assign('jumpUrl',__APP__);
			$this->error('您尚未登录');
		}

		$this->display();
	}

	public function securityfindpassbind(){
		C('HTML_CACHE_ON',false);
		if(!$_SESSION['uid'] || $_SESSION['uid'] < 0){
			$this->assign('jumpUrl',__APP__);
			$this->error('您尚未登录');
		}

		$this->display();
	}

	public function securityemailbind(){
		C('HTML_CACHE_ON',false);
		if(!$_SESSION['uid'] || $_SESSION['uid'] < 0){
			$this->assign('jumpUrl',__APP__);
			$this->error('您尚未登录');
		}

		$this->display();
	}

	public function securityqabind(){
		C('HTML_CACHE_ON',false);
		if(!$_SESSION['uid'] || $_SESSION['uid'] < 0){
			$this->assign('jumpUrl',__APP__);
			$this->error('您尚未登录');
		}

		$this->display();
	}

	public function helplist(){
		C('HTML_CACHE_ON',false);
		if(!$_SESSION['uid'] || $_SESSION['uid'] < 0){
			$this->assign('jumpUrl',__APP__);
			$this->error('您尚未登录');
		}

		$this->display();
	}

	public function helpview(){
		C('HTML_CACHE_ON',false);
		if(!$_SESSION['uid'] || $_SESSION['uid'] < 0){
			$this->assign('jumpUrl',__APP__);
			$this->error('您尚未登录');
		}

		$this->display();
	}

	public function exchange(){
		C('HTML_CACHE_ON',false);
		if(!$_SESSION['uid'] || $_SESSION['uid'] < 0){
			$this->assign('jumpUrl',__APP__);
			$this->error('您尚未登录');
		}

		$userinfo = D("Member")->find($_SESSION['uid']);
		$this->assign('userinfo', $userinfo);

		$exchanges = D("Beandetail")->where("uid=".$_SESSION['uid'].' and type="expend" and action="exchange"')->order('addtime desc')->select();
		$this->assign('exchanges', $exchanges);

		$this->display();
	}
	
	public function doExchange(){
		C('HTML_CACHE_ON',false);
		if(!$_SESSION['uid'] || $_SESSION['uid'] < 0){
			echo 'notlogin';
			exit;
		}

		$userinfo = D("Member")->find($_SESSION['uid']);
		if($userinfo['beanbalance'] < $_REQUEST['changelimit']){
			echo 'noenoughbean';
			exit;
		}

		D("Member")->execute('update ss_member set coinbalance=coinbalance+'.$_REQUEST['changelimit'].',beanbalance=beanbalance-'.$_REQUEST['changelimit'].' where id='.$_SESSION['uid']);
		$Beandetail = D("Beandetail");
		$Beandetail->create();
		$Beandetail->type = 'expend';
		$Beandetail->action = 'exchange';
		$Beandetail->uid = $_SESSION['uid'];
		$Beandetail->content = '兑换秀币';
		$Beandetail->bean = $_REQUEST['changelimit'];
		$Beandetail->addtime = time();
		$detailId = $Beandetail->add();

		$Coindetail = D("Coindetail");
		$Coindetail->create();
		$Coindetail->type = 'income';
		$Coindetail->action = 'exchange';
		$Coindetail->uid = $_SESSION['uid'];
		$Coindetail->content = $_REQUEST['changelimit'].'个秀豆兑换';
		$Coindetail->coin = $_REQUEST['changelimit'];
		$Coindetail->addtime = time();
		$detailId = $Coindetail->add();

		echo '000000';
		exit;
	}

	public function settlement(){
		C('HTML_CACHE_ON',false);
		if(!$_SESSION['uid'] || $_SESSION['uid'] < 0){
			$this->assign('jumpUrl',__APP__);
			$this->error('您尚未登录');
		}

		$userinfo = D("Member")->find($_SESSION['uid']);
		$this->assign('userinfo', $userinfo);

		$settlements = D("Beandetail")->where("uid=".$_SESSION['uid'].' and type="expend" and action="settlement"')->order('addtime desc')->select();
		$this->assign('settlements', $settlements);

		$this->display();
	}

	public function freezeIncome(){
		C('HTML_CACHE_ON',false);
		if(!$_SESSION['uid'] || $_SESSION['uid'] < 0){
			echo 'notlogin';
			exit;
		}

		D("Member")->execute('update ss_member set freezeincome='.$_REQUEST['freezeincome'].',freezestatus="'.$_REQUEST['freezestatus'].'" where id='.$_SESSION['uid']);

		echo '000000';
		exit;
	}

	public function zaegg(){
		C('HTML_CACHE_ON',false);
		header("Content-type: text/html; charset=utf-8"); 
		if(!$_SESSION['uid'] || $_SESSION['uid'] < 0){
			echo "echostr=nologin";
			exit;
		}

		$eggset=D('Eggset');
		$eggsetinfo=$eggset->find(1);
		if(!$eggsetinfo) {
			echo "echostr=syserror";
			exit;
		}

		$userinfo = D("Member")->find($_SESSION['uid']);

		if($userinfo['coinbalance'] < $eggsetinfo['onceneedcoin']){
			echo "echostr=coinnotenough&needcoin=".$eggsetinfo['onceneedcoin'];
			exit;
		}
		else{
			//扣费
			D("Member")->execute('update ss_member set spendcoin=spendcoin+'.$eggsetinfo['onceneedcoin'].',coinbalance=coinbalance-'.$eggsetinfo['onceneedcoin'].' where id='.$_SESSION['uid']);
			//记入虚拟币交易明细
			$Coindetail = D("Coindetail");
			$Coindetail->create();
			$Coindetail->type = 'expend';
			$Coindetail->action = 'zaegg';
			$Coindetail->uid = $_SESSION['uid'];
				
			$Coindetail->content = '砸蛋1次花费';
			$Coindetail->objectIcon = '/Public/images/fei.png';
			$Coindetail->coin = $eggsetinfo['onceneedcoin'];
				
			$Coindetail->addtime = time();
			$detailId = $Coindetail->add();

			$randKey = mt_rand(1, 100);
			if ($randKey <= $eggsetinfo['wincoin_odds']) {
				$wincoin = $eggsetinfo['wincoin'];
			} elseif ($randKey <= $eggsetinfo['wincoin_odds'] + $eggsetinfo['wincoin2_odds']) {
				$wincoin = $eggsetinfo['wincoin2'];
			} elseif ($randKey <= $eggsetinfo['wincoin_odds'] + $eggsetinfo['wincoin2_odds'] + $eggsetinfo['wincoin3_odds']) {
				$wincoin = $eggsetinfo['wincoin3'];
			} elseif ($randKey <= $eggsetinfo['wincoin_odds'] + $eggsetinfo['wincoin2_odds'] + $eggsetinfo['wincoin3_odds'] + $eggsetinfo['wincoin4_odds']) {
				$wincoin = $eggsetinfo['wincoin4'];
			} else {
				$wincoin = 0;
			}

			if($wincoin == 0){
				echo "echostr=failed";
				exit;
			}
			else{
				//给用户赠送相应奖励
				D("Member")->execute('update ss_member set coinbalance=coinbalance+'.$wincoin.' where id='.$_SESSION['uid']);

				D("Giveaway")->execute('insert into ss_giveaway(uid,touid,content,remark,objectIcon,addtime) values(0,'.$_SESSION['uid'].',"'.$wincoin.'","砸蛋奖励","/Public/images/coin.png",'.time().')');

				echo "echostr=win&wincoin=".$wincoin;
				exit;
			}

		}

	}

}