<?php
class ActivityAction extends BaseAction {
	//文章内容详情页
	public function huodonginfo(){
		$wzid=$_GET['info'];
		//var_dump($wzid);
		//查询出活动具体内容
	   $info=M("announce")->where("id=".$wzid)->getField("content");
	  // var_dump($info);
	  $this->assign("info",$info);
		$this->display();
	}
	
	
	public function index()
	{
		$flid=$_GET['fenlei'];
	        	//var_dump($flid);
		if($flid==null){
			$flid=0;
		}
		//var_dump($flid);
		
	
		//根据活动分类查询出相应的分类id得到相应的文正列表
		//查询出所特有的活动文章带分页
		//$announce = M("announce")->order("addtime")->select();
		if($flid==0){
		import("ORG.Util.Page");
		$count = M("announce")->order("addtime")->count();
		$page = new Page($count,10);
		
		$show = $page->show();
		
		$list = M("announce")->order("addtime desc")->limit($page->firstRow.','.$page->listRows)->select();
		$nums=count($list);
		//var_dump($nums);
		$a=0;
		foreach($list as $k=>$v){
			$zhuangtai=$v["zhuangtai"];
			//var_dump($zhuangtai);
			if($zhuangtai=='未开始'){
				$list[$a]['num']=2;
			}elseif($zhuangtai=='正在进行'){
				$list[$a]['num']=1;
			}elseif($zhuangtai=='已结束'){
				$list[$a]['num']=3;
			}
			$a++;
		}
		//var_dump($list);
	}else{
		import("ORG.Util.Page");
		$count = M("announce")->where("fid=".$flid)->order("addtime")->count();
		$page = new Page($count,10);
		
		$show = $page->show();
		
		$list = M("announce")->where("fid=".$flid)->order("addtime desc")->limit($page->firstRow.','.$page->listRows)->select();
		$nums=count($list);
		//var_dump($nums);
		$a=0;
		foreach($list as $k=>$v){
			$zhuangtai=$v["zhuangtai"];
			//var_dump($zhuangtai);
			if($zhuangtai=='未开始'){
				$list[$a]['num']=2;
			}elseif($zhuangtai=='正在进行'){
				$list[$a]['num']=1;
			}elseif($zhuangtai=='已结束'){
				$list[$a]['num']=3;
			}
			$a++;
		
		
	}
		
	}
        $this->assign("flid",$flid);	
		$this->assign("list",$list);
		$this->assign("page",$show);
		
	
		
		    //获取图片轮播的全部图片
	     $rollpic=M("huodongrollpic")->select();
		 //var_dump($rollpic);
		 $this->assign("rollpic",$rollpic);
		//获取全部活动文章文类列表
		$fenlei=M("huodongfenlei")->select();
		$this->assign("fenlei",$fenlei);
		 /*<!--
        	作者：364751598@qq.com
        	时间：2015-07-03
        	描述：明星日排行
        -->*/ 
        	$emceeRank_day1 = D('Beandetail')->query('SELECT uid,sum(bean) as total FROM `ss_beandetail` where type="income" and action="getgift" and date_format(FROM_UNIXTIME(addtime),"%m-%d-%Y")=date_format(now(),"%m-%d-%Y") group by uid order by total desc LIMIT 1');
		$a=0;
		foreach($emceeRank_day1 as $k=>$vo){
		
		$userinfo = D("Member")->find($vo['uid']);
		$$emceeRank_day1[$a]['userinfo']=$userinfo;
		$emceelevel = getEmceelevel($userinfo['earnbean']);
		$$emceeRank_day1[$a]['emceelevel']=$emceelevel;
		$a++;
		}
		$this->assign("emceeRank_day1",$emceeRank_day1);
		
		$emceeRank_day4 = D('Beandetail')->query('SELECT uid,sum(bean) as total FROM `ss_beandetail` where type="income" and action="getgift" and date_format(FROM_UNIXTIME(addtime),"%m-%d-%Y")=date_format(now(),"%m-%d-%Y") group by uid order by total desc LIMIT 1,4');
		$a=0;
		$b=1;
		foreach($emceeRank_day4 as $k=>$vo){
		
		$userinfo = D("Member")->find($vo['uid']);
		$emceeRank_day4[$a]['userinfo']=$userinfo;
		$emceelevel = getEmceelevel($userinfo['earnbean']);
		$emceeRank_day4[$a]['emceelevel']=$emceelevel;
		$emceeRank_day4[$a]['xuhao']=($b+1);
		$b++;
		$a++;
		}
		
		$this->assign("emceeRank_day4",$emceeRank_day4);
		 /*<!--
        	作者：364751598@qq.com
        	时间：2015-07-03
        	描述：明星周排行
        -->*/ 
        	$emceeRank_week1 = D('Beandetail')->query('SELECT uid,sum(bean) as total FROM `ss_beandetail` where type="income" and action="getgift" and date_format(FROM_UNIXTIME(addtime),"%Y")=date_format(now(),"%Y") and date_format(FROM_UNIXTIME(addtime),"%u")=date_format(now(),"%u") group by uid order by total desc LIMIT 1');
		$a=0;
		foreach($emceeRank_week1 as $k=>$vo){
		
		$userinfo = D("Member")->find($vo['uid']);
		$emceeRank_week1[$a]['userinfo']=$userinfo;
		$emceelevel = getEmceelevel($userinfo['earnbean']);
		$emceeRank_week1[$a]['emceelevel']=$emceelevel;
		$a++;
		}
		$this->assign("emceeRank_week1",$emceeRank_week1);
		
		$emceeRank_week4 = D('Beandetail')->query('SELECT uid,sum(bean) as total FROM `ss_beandetail` where type="income" and action="getgift" and date_format(FROM_UNIXTIME(addtime),"%Y")=date_format(now(),"%Y") and date_format(FROM_UNIXTIME(addtime),"%u")=date_format(now(),"%u") group by uid order by total desc LIMIT 1，4');
		$a=0;
		$b=1;
		foreach($emceeRank_week4 as $k=>$vo){
		
		$userinfo = D("Member")->find($vo['uid']);
		$emceeRank_week4[$a]['userinfo']=$userinfo;
		$emceelevel = getEmceelevel($userinfo['earnbean']);
		$emceeRank_week4[$a]['emceelevel']=$emceelevel;
		$emceeRank_week4[$a]['xuhao']=($b+1);
		$b++;
		$a++;
		}
		
		$this->assign("emceeRank_week4",$emceeRank_week4);
 
 /*<!--
        	作者：364751598@qq.com
        	时间：2015-07-03
        	描述：明星月排行
        -->*/
    	//当前家族下的明星榜
	$emceeRank_month1 = D('Beandetail')->query('SELECT uid,sum(bean) as total FROM `ss_beandetail` where type="income" and action="getgift" and date_format(FROM_UNIXTIME(addtime),"%m-%Y")=date_format(now(),"%m-%Y") group by uid order by total desc LIMIT 1');
		$a=0;
		foreach($emceeRank_month1 as $k=>$vo){
		
		$userinfo = D("Member")->find($vo['uid']);
		$emceeRank_month1[$a]['userinfo']=$userinfo;
		$emceelevel = getEmceelevel($userinfo['earnbean']);
		$emceeRank_month1[$a]['emceelevel']=$emceelevel;
		$a++;
		}
		$this->assign("emceeRank_month1",$emceeRank_month1);
		
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
		 /*<!--
        	作者：364751598@qq.com
        	时间：2015-07-03
        	描述：明星总排行
        -->*/
	$emceeRank_all1 = D('Beandetail')->query('SELECT uid,sum(bean) as total FROM `ss_beandetail` where type="income" and action="getgift" group by uid order by total desc LIMIT 1');
		$a=0;
		foreach($emceeRank_all1 as $k=>$vo){
		
		$userinfo = D("Member")->find($vo['uid']);
		$emceeRank_all1[$a]['userinfo']=$userinfo;
		$emceelevel = getEmceelevel($userinfo['earnbean']);
		$emceeRank_all1[$a]['emceelevel']=$emceelevel;
		$a++;
		}
		//var_dump($emceeRank_all);
		$this->assign("emceeRank_all1",$emceeRank_all1);
		//var_dump($emceeRank_all1);
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
	
	//富豪榜	
	     /*<!--
        	作者：364751598@qq.com
        	时间：2015-07-03
        	描述：富豪日榜
        -->*/
	$richRank_day1 = D('Coindetail')->query('SELECT uid,sum(coin) as total FROM `ss_coindetail` where type="expend" and date_format(FROM_UNIXTIME(addtime),"%m-%d-%Y")=date_format(now(),"%m-%d-%Y") group by uid order by total desc LIMIT 1');
	$a=0;
		foreach($richRank_day1 as $k=>$vo){
		
		$userinfo = D("Member")->find($vo['uid']);
		$richRank_day1[$a]['userinfo']=$userinfo;
		$richlevel = getRichlevel($userinfo['spendcoin']);
		$richRank_day1[$a]['richlecel']=$richlevel;
		$a++;
		}

		$this->assign("richRank_day1",$richRank_day1);
		$richRank_day4 = D('Coindetail')->query('SELECT uid,sum(coin) as total FROM `ss_coindetail` where type="expend" and date_format(FROM_UNIXTIME(addtime),"%m-%d-%Y")=date_format(now(),"%m-%d-%Y") group by uid order by total desc LIMIT 1,4');
		$a=0;
		$b=1;
		foreach($richRank_day4 as $k=>$vo){
		
		$userinfo = D("Member")->find($vo['uid']);
		$richRank_day4[$a]['userinfo']=$userinfo;
		$richlevel = getRichlevel($userinfo['spendcoin']);
		$richRank_day4[$a]['richlecel']=$richlevel;
		$richRank_day4[$a]['xuhao']=($b+1);
		$b++;
		$a++;
		}
		$this->assign("richRank_day4",$richRank_day4);
			     /*<!--
        	作者：364751598@qq.com
        	时间：2015-07-03
        	描述：富豪周榜
        -->*/
        $richRank_week1 = D('Coindetail')->query('SELECT uid,sum(coin) as total FROM `ss_coindetail` where type="expend" and date_format(FROM_UNIXTIME(addtime),"%Y")=date_format(now(),"%Y") and date_format(FROM_UNIXTIME(addtime),"%u")=date_format(now(),"%u") group by uid order by total desc LIMIT 1');
		$a=0;
		foreach($richRank_week1 as $k=>$vo){
		
		$userinfo = D("Member")->find($vo['uid']);
		$richRank_week1[$a]['userinfo']=$userinfo;
		$richlevel = getRichlevel($userinfo['spendcoin']);
		$richRank_week1[$a]['richlecel']=$richlevel;
		$a++;
		}

		$this->assign("$richRank_week1",$richRank_week1);
		   $richRank_week4 = D('Coindetail')->query('SELECT uid,sum(coin) as total FROM `ss_coindetail` where type="expend" and date_format(FROM_UNIXTIME(addtime),"%Y")=date_format(now(),"%Y") and date_format(FROM_UNIXTIME(addtime),"%u")=date_format(now(),"%u") group by uid order by total desc LIMIT 1,4');
		$a=0;
		$b=1;
		foreach( $richRank_week4 as $k=>$vo){
		
		$userinfo = D("Member")->find($vo['uid']);
		 $richRank_week4[$a]['userinfo']=$userinfo;
		$richlevel = getRichlevel($userinfo['spendcoin']);
		 $richRank_week4[$a]['richlecel']=$richlevel;
		 $richRank_week4[$a]['xuhao']=($b+1);
		$b++;
		$a++;
		}
		$this->assign("richRank_week4", $richRank_week4);
		
			  	//查询出富豪月榜的前5条
	$richRank_month1 = D('Coindetail')->query('SELECT uid,sum(coin) as total FROM `ss_coindetail` where type="expend" and date_format(FROM_UNIXTIME(addtime),"%m-%Y")=date_format(now(),"%m-%Y") group by uid order by total desc LIMIT 1');
		$a=0;
		foreach($richRank_month1 as $k=>$vo){
		
		$userinfo = D("Member")->find($vo['uid']);
		$richRank_month1[$a]['userinfo']=$userinfo;
		$richlevel = getRichlevel($userinfo['spendcoin']);
		$richRank_month1[$a]['richlecel']=$richlevel;
		$a++;
		}

		$this->assign("richRank_month1",$richRank_month1);
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
		$richRank_all1 = D('Coindetail')->query('SELECT uid,sum(coin) as total FROM `ss_coindetail` where type="expend" group by uid order by total desc LIMIT 1');
		$a=0;
		foreach($richRank_all1 as $k=>$vo){
		
		$userinfo = D("Member")->find($vo['uid']);
		$richRank_all1[$a]['userinfo']=$userinfo;
		$richlevel = getRichlevel($userinfo['spendcoin']);
		$richRank_all1[$a]['richlecel']=$richlevel;
		$a++;
		}

		$this->assign("richRank_all1",$richRank_all1);	
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
}



?>