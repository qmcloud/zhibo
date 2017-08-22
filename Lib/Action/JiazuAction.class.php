<?php
class JiazuAction extends BaseAction {
			public function sqjoinfamily(){
	    $familyid=$_GET['familyid'];
				var_dump($familyid);
		$uid=$_SESSION['uid'];
		//var_dump($uid);
	   //判断用户是否登录
	   if(!$_SESSION['uid'] || $_SESSION['uid'] < 0){
			
			$this->error('您尚未登录',"__URL__/index");
		}
			
		//根据用户ID查询出相关的信息
		$res=M("member")->where("id=".$uid)->getField("emceeagent");
		if($res=='y'){
			$this->error("您拥有自己的家族，不允许加入其它家族","__URL__/index");
		
		}
		//判断用户是否加入过家族
		$agentuid=M("member")->where("id=".$uid)->getField("agentuid");
		if($agentuid!='0'){
			$this->error("您已经加入过家族。。","__URL__/index");
		
		}
	//判断用户是否已经提交过申请
	    $sqinfo=M("sqjoinfamily")->where("uid=".$uid)->order("sqtime desc")->limit(1)->select();
	       var_dump($sqinfo);
	    $zhuangtai=$sqinfo[0]["zhuangtai"];
           var_dump($zhuangtai);
//0:未审核;1:已通过；2：未通过；
	    if($zhuangtai=="0"){
			$this->error("您有一条申请记录正在审核中，请等待审核");
			 
		}
	
		//符合条件  插入申请记录
		 $model=M("sqjoinfamily");
		$model->uid=$uid;
		$model->familyid=$familyid;
		$model->sqtime=time();
		if($model->add()){
			$this->success("申请成功，请等待审核","__URL__/index");
		}else{
			$this->error("申请失败，请再次申请","__URL__/index");
		}
		
		
		
		}
	//申请成为代理城建家族
	public function sqagent(){
		$uid=$_SESSION['uid'];
	   //判断用户是否登录
	   if(!$_SESSION['uid'] || $_SESSION['uid'] < 0){
			
			$this->error('您尚未登录',"__URL__/index");
		}
			
		//根据用户ID查询出相关的信息
		$res=M("member")->where("id=".$uid)->getField("emceeagent");
		if($res=='y'){
			$this->error("您已经创建过家族","__URL__/index");
		
		}
	//根据用户id判断用户是否提交过申请
	$sqinfo=M("agentfamily")->where("uid=".$uid)->order("sqtime desc")->limit(1)->select();

	$zhuangtai=$sqinfo[0]["zhuangtai"];
  
   if($zhuangtai=="未审核"){
	   $this->error("您提交的申请，正在等待系统审核。。。","__URL__/index");
   }
   
   if($zhuangtai=="未通过"){
	 $this->assign("zhuangtai","对不起您的上次申请未通过，请认真填写！");
   }
	
			
		
			
			
		
		
		
		
		$fmodel=M("agentfamily");
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
		      
		   //var_dump($savename);
		  
		
		
		
		$vo = $fmodel->create();
		
		$fmodel->familyimg=$savename;
        $fmodel->sqtime=time();
		$fmodel->uid=$uid;

	
		if(!$vo) {
			$this->error($fmodel->getError());
		}else{
			$annId = $fmodel->add();
			if($annId){
				$this->success("添加成功！");
			}else{
				$this->error("添加失败！");
			}
			
		}
		  
		}
		
		
		
		
		$this->display();
	}
	
	
	
	
	public function jiazunei(){
		$agentid=$_GET['agent'];
		$this->assign("agentid",$agentid);
        //var_dump($agentid);
		//得到家族信息
		$familyinfo=M("agentfamily")->where("uid='$agentid' && zhuangtai='已通过'")->select();
		//var_dump($familyinfo);
		$this->assign("familyinfo",$familyinfo);
		//最新加入家族的主播列表5人
		  $new=M("sqjoinfamily")->where("familyid='$agentid' && zhuangtai='1'")->order("shtime desc")->limit(5)->select();
		  $fix= C('DB_PREFIX');
		$field="m.curroomnum,m.ucuid,sq.uid,sq.shtime";
		$newjoin = M('sqjoinfamily sq')->field($field)->join("{$fix}member m ON sq.uid=m.id")->where("familyid='$agentid' && zhuangtai='1'")->order("shtime desc")->limit(5)->select();
		$this->assign("newjoin",$newjoin); 
		  
		  
		  
		  
		  //var_dump($new);
		//根据得到的id 来得到指定代理人的信息
		$agentinfo=M("member")->where("id=$agentid")->select();
		$this->assign("agentinfo",$agentinfo);
		//得到当前主播的等级
		$agentlevel = getEmceelevel($agentinfo[0]['earnbean']);
		$this->assign("agentlevel",$agentlevel);
		//当前代理下的 主播人数
		$total=M("member")->where("agentuid=$agentid")->count();
		$this->assign("total",$total);
		$User = M('User'); // 实例化User对象
        import('ORG.Util.Page');// 导入分页类
       
        $Page       = new Page($total,20);// 实例化分页类 传入总记录数和每页显示的记录数
        $show       = $Page->show();// 分页显示输出
		//当前代理下的所有主播列表
	$emceeinfo=M("member")->where("agentuid=$agentid")->limit($Page->firstRow.','.$Page->listRows)->select();
		
		//得到主播的等级信息
		$a=0;
		foreach($emceeinfo as $k=>$v){
			$emceelevel = getEmceelevel($v['earnbean']);
			$emceeinfo[$a]['emceelevel']=$emceelevel;
			$a++;
		}
		//var_dump($emceeinfo);
			$this->assign("emceeinfo",$emceeinfo);
		    $this->assign("page",$show);
		//var_dump($show);
		
		
	
		//人气
			$rq = D('Liverecord')->query("SELECT sum(entercount) as total FROM `ss_liverecord` where uid=$agentid");
		
			$rqtotal=$rq[0]["total"];
		
		$zbid=M("member")->field("id")->where("agentuid=$agentid")->select();
		//var_dump($zbid);
		$a=0;
		$uid=array();
		foreach($zbid as $k=>$v){
			$uid[$a]=$v['id'];
			$a++;
		}
		//var_dump($uid);
		
		$a=0;
			foreach($zbid as $k=>$v){
				$emceeid=$v['id'];
				$emceerq = D('Liverecord')->query("SELECT sum(entercount) as total FROM `ss_liverecord` where uid=$emceeid");
				//var_dump($emceerq);
				$rqtotal=$rqtotal+$emceerq[0][$total];
				$a++;
				
			}
			$this->assign("rqtotal",$rqtotal);
			//var_dump($rqtotal);
			
		
	//当前代理下的在线人气主播主播
			$olrqzb = D('Member')->where(" broadcasting='y' and isdelete='n' and agentuid=$agentid")->field('nickname,curroomnum,bigpic,online,virtualguest,agentuid,online')->order('online desc')->limit(4)->select();
			//var_dump($olrqzb);
			$this->assign("olrqzb",$olrqzb);
	//当前家族下的明星榜
	$emceeRank_month = D('Beandetail')->query('SELECT uid,sum(bean) as total FROM `ss_beandetail` where type="income" and action="getgift" and date_format(FROM_UNIXTIME(addtime),"%m-%Y")=date_format(now(),"%m-%Y") group by uid order by total desc LIMIT 1');
		$a=0;
		foreach($emceeRank_month as $k=>$vo){
		
		$userinfo = D("Member")->find($vo['uid']);
		$emceeRank_month[$a]['userinfo']=$userinfo;
		$emceelevel = getEmceelevel($userinfo['earnbean']);
		$emceeRank_month[$a]['emceelevel']=$emceelevel;
		$a++;
		}
		$this->assign("emceeRank_month",$emceeRank_month);
		
		$emceeRank_month4 = D('Beandetail')->query('SELECT uid,sum(bean) as total FROM `ss_beandetail` where type="income" and action="getgift" and date_format(FROM_UNIXTIME(addtime),"%m-%Y")=date_format(now(),"%m-%Y") group by uid order by total desc LIMIT 1,4');
		$a=0;
		$b=1;
		foreach($emceeRank_month4 as $k=>$vo){
		
		$userinfo = D("Member")->find($vo['uid']);
		$emceeRank_month4[$a]['userinfo']=$userinfo;
		$emceelevel = getEmceelevel($userinfo['earnbean']);
		$emceeRank_month4[$a]['emceelevel']=$emceelevel;
		$emceeRank_month4[$a]['xuhao']=($b+1);
		$b++;
		$a++;
		}
		
		$this->assign("emceeRank_month4",$emceeRank_month4);
	$emceeRank_all = D('Beandetail')->query('SELECT uid,sum(bean) as total FROM `ss_beandetail` where type="income" and action="getgift" group by uid order by total desc LIMIT 1');
		$a=0;
		foreach($emceeRank_all as $k=>$vo){
	
		$userinfo = D("Member")->find($vo['uid']);
		$emceeRank_all[$a]['userinfo']=$userinfo;
		$emceelevel = getEmceelevel($userinfo['earnbean']);
		$emceeRank_all[$a]['emceelevel']=$emceelevel;
		$a++;
		}
		//var_dump($emceeRank_all);
		$this->assign("emceeRank_all",$emceeRank_all);
		$emceeRank_all4= D('Beandetail')->query('SELECT uid,sum(bean) as total FROM `ss_beandetail` where type="income" and action="getgift" group by uid order by total desc LIMIT 1,4');
		$a=0;
		$b=1;
		foreach($emceeRank_all4 as $k=>$vo){
		
		$userinfo = D("Member")->find($vo['uid']);
		$emceeRank_all4[$a]['userinfo']=$userinfo;
		$emceelevel = getEmceelevel($userinfo['earnbean']);
		$emceeRank_all4[$a]['emceelevel']=$emceelevel;
		$emceeRank_all4[$a]['xuhao']=($b+1);
		$b++;
		$a++;
		}
//var_dump($emceeRank_all4);
		$this->assign("emceeRank_all4",$emceeRank_all4);
	
	//当前家族下的富豪榜		
			  	//查询出富豪月榜的前5条
	$richRank_month = D('Coindetail')->query('SELECT uid,sum(coin) as total FROM `ss_coindetail` where type="expend" and date_format(FROM_UNIXTIME(addtime),"%m-%Y")=date_format(now(),"%m-%Y") group by uid order by total desc LIMIT 1');
		$a=0;
		foreach($richRank_month as $k=>$vo){
		
		$userinfo = D("Member")->find($vo['uid']);
		$richRank_month[$a]['userinfo']=$userinfo;
		$richlevel = getRichlevel($userinfo['spendcoin']);
		$richRank_month[$a]['richlecel']=$richlevel;
		$a++;
		}

		$this->assign("richRank_month",$richRank_month);
		$richRank_month4 = D('Coindetail')->query('SELECT uid,sum(coin) as total FROM `ss_coindetail` where type="expend" and date_format(FROM_UNIXTIME(addtime),"%m-%Y")=date_format(now(),"%m-%Y") group by uid order by total desc LIMIT 1,4');
		$a=0;
		$b=1;
		foreach($richRank_month4 as $k=>$vo){
		
		$userinfo = D("Member")->find($vo['uid']);
		$richRank_month4[$a]['userinfo']=$userinfo;
		$richlevel = getRichlevel($userinfo['spendcoin']);
		$richRank_month4[$a]['richlecel']=$richlevel;
		$richRank_month4[$a]['xuhao']=($b+1);
		$b++;
		$a++;
		}
		$this->assign("richRank_month4",$richRank_month4);
		$richRank_all = D('Coindetail')->query('SELECT uid,sum(coin) as total FROM `ss_coindetail` where type="expend" group by uid order by total desc LIMIT 1');
		$a=0;
		foreach($richRank_all as $k=>$vo){
		
		$userinfo = D("Member")->find($vo['uid']);
		$richRank_all[$a]['userinfo']=$userinfo;
		$richlevel = getRichlevel($userinfo['spendcoin']);
		$richRank_all[$a]['richlecel']=$richlevel;
		$a++;
		}

		$this->assign("richRank_all",$richRank_all);	
		$richRank_all4 = D('Coindetail')->query('SELECT uid,sum(coin) as total FROM `ss_coindetail` where type="expend" group by uid order by total desc LIMIT 1,4');
		$a=0;
		$b=1;
		foreach($richRank_all4 as $k=>$vo){
		
		$userinfo = D("Member")->find($vo['uid']);
		$richRank_all4[$a]['userinfo']=$userinfo;
		$richlevel = getRichlevel($userinfo['spendcoin']);
		$richRank_all4[$a]['richlecel']=$richlevel;
		$richRank_all4[$a]['xuhao']=($b+1);
		$b++;
		$a++;
		}
		$this->assign("richRank_all4",$richRank_all4);
			
		$this->display();
	}
	
	
	
	public function index(){
		
		/*$classid=$_GET['class'];
		
		if($classid==null){
			$classid=1;
		}
		$this->assign("classid",$classid);*/
	
		$zbcount=M("member")->query('select count(*) as total,agentuid from `ss_member` where agentuid>0 group by agentuid order by total desc');
		//实现分页
		import("ORG.Util.Page");
		$total=count($zbcount);
		$page = new Page($total,25);
		
		$show = $page->show();
		
		//var_dump($total);
		
		$data=array();
		$a=0;
		foreach($zbcount as $k=>$v){
			$aid=$v['agentuid'];
	     
			$agentinfo=M("member")->where("id=$aid")->select();	
			$zbcounts=M("member")->query("select count(*) as total from `ss_member` where agentuid=$aid ");
			
	         $data[$a]=$agentinfo;
			 $data[$a]['zbtotal']=$zbcounts;
			 $a++;
		
		}
          
		$this->assign("data",$data);
	
		$this->assign("zbcount",$zbcount);
		
		
		
		//最新主播代理三人
	$res=M("member")->where("emceeagent='y'")->order("emceeagenttime desc")->limit(3)->select();	
	$this->assign("newagent",$res);	
		//var_dump($res);
		$a=0;
		foreach($res as $k=>$v){
			$agentuid=$v['id'];
			$zbcount=M("member")->where("agentuid=$agentuid")->count();
			//var_dump($zbcount);
	    $emceelevel = getEmceelevel($v['earnbean']);
			//var_dump($emceelevel);
		$res[$a]['emceelevel']=$emceelevel;
		$res[$a]['zbtotal']=$zbcount;
		$a++;
		}	
		$this->assign("newagent",$res);
		//查询出在线家族主播 被推荐的
		$recusers = D('Member')->where('bigpic<>"" and broadcasting="y" and isdelete="n" and agentuid!=0 ')->field('nickname,curroomnum,bigpic,online,virtualguest,agentuid')->order('rectime desc')->limit(10)->select();
		//根据agentuid得到相应的家族信息
		$a=0;
		foreach($recusers as $k=>$v){
			$uid=$v['agentuid'];
			
			$agentinfo=M("member")->where("id='$uid'")->getField("nickname");
			$recusers[$a]['agentinfo']=$agentinfo;
			$a++;
		}
		
		
		$this->assign("onlinezb",$recusers);
		//$richRank_weekq3 = D('Coindetail')->query('SELECT uid,sum(coin) as total FROM `ss_coindetail` where type="expend" and date_format(FROM_UNIXTIME(addtime),"%Y")=date_format(now(),"%Y") and date_format(FROM_UNIXTIME(addtime),"%u")=date_format(now(),"%u") group by uid order by total desc LIMIT 3');
		
		
	  $this->display();
	}
	
}