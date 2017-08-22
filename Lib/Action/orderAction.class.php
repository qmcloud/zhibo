<?php
class orderAction extends BaseAction {
    public function index(){
		//明星日 周 月 总榜
		$emceeRank_day = D('Beandetail')->query('SELECT uid,sum(bean) as total FROM `ss_beandetail` where type="income" and action="getgift" and date_format(FROM_UNIXTIME(addtime),"%m-%d-%Y")=date_format(now(),"%m-%d-%Y") group by uid order by total desc LIMIT 20');
		
		$this->assign('emceeRank_day', $emceeRank_day);
		//查询出明星日榜的前三条 并得到用户的相关信息
		$emceeRank_dayq3 = D('Beandetail')->query('SELECT uid,sum(bean) as total FROM `ss_beandetail` where type="income" and action="getgift" and date_format(FROM_UNIXTIME(addtime),"%m-%d-%Y")=date_format(now(),"%m-%d-%Y") group by uid order by total desc LIMIT 3');
		$a=0;
		foreach($emceeRank_dayq3 as $k=>$vo){
		
		$userinfo = D("Member")->find($vo['uid']);
		$emceeRank_dayq3[$a]['userinfo']=$userinfo;
		$emceelevel = getEmceelevel($userinfo['earnbean']);
		$emceeRank_dayq3[$a]['emceelevel']=$emceelevel;
		$a++;
		}
	
		$this->assign("emceeRank_dayq3",$emceeRank_dayq3);
		//查询出明星日榜的4-9条 并得到用户的相关信息中间6条
			$emceeRank_dayz6 = D('Beandetail')->query('SELECT uid,sum(bean) as total FROM `ss_beandetail` where type="income" and action="getgift" and date_format(FROM_UNIXTIME(addtime),"%m-%d-%Y")=date_format(now(),"%m-%d-%Y") group by uid order by total desc LIMIT 3,6');
		$a=0;
		$b=3;
		foreach($emceeRank_dayz6 as $k=>$vo){
		
		$userinfo = D("Member")->find($vo['uid']);
		$emceeRank_dayz6[$a]['userinfo']=$userinfo;
		$emceelevel = getEmceelevel($userinfo['earnbean']);
		$emceeRank_dayz6[$a]['emceelevel']=$emceelevel;
		$emceeRank_dayz6[$a]['xuhao']=($b+1);
		$b++;
		$a++;
		}
		//var_dump($emceeRank_dayz6);
		$this->assign("emceeRank_dayz6",$emceeRank_dayz6);
		//查询出明星日榜的10-15条 并得到用户的相关信息hou6条
		$emceeRank_dayh6 = D('Beandetail')->query('SELECT uid,sum(bean) as total FROM `ss_beandetail` where type="income" and action="getgift" and date_format(FROM_UNIXTIME(addtime),"%m-%d-%Y")=date_format(now(),"%m-%d-%Y") group by uid order by total desc LIMIT 9,6');
		$a=0;
		$b=9;
		foreach($emceeRank_dayh6 as $k=>$vo){
		
		$userinfo = D("Member")->find($vo['uid']);
		$emceeRank_dayh6[$a]['userinfo']=$userinfo;
		$emceelevel = getEmceelevel($userinfo['earnbean']);
		$emceeRank_dayh6[$a]['emceelevel']=$emceelevel;
		$emceeRank_dayh6[$a]['xuhao']=($b+1);
		$b++;
		$a++;
		}
		$this->assign("emceeRank_dayh6",$emceeRank_dayh6);
		
		
		//var_dump($emceeRank_day);
		$emceeRank_week = D('Beandetail')->query('SELECT uid,sum(bean) as total FROM `ss_beandetail` where type="income" and action="getgift" and date_format(FROM_UNIXTIME(addtime),"%Y")=date_format(now(),"%Y") and date_format(FROM_UNIXTIME(addtime),"%u")=date_format(now(),"%u") group by uid order by total desc LIMIT 20');
		//var_dump($emceeRank_week);
		$this->assign('emceeRank_week', $emceeRank_week);
		//查询出明星周榜的前三条 并得到用户的相关信息
		$emceeRank_weekq3 = D('Beandetail')->query('SELECT uid,sum(bean) as total FROM `ss_beandetail` where type="income" and action="getgift" and date_format(FROM_UNIXTIME(addtime),"%Y")=date_format(now(),"%Y") and date_format(FROM_UNIXTIME(addtime),"%u")=date_format(now(),"%u") group by uid order by total desc LIMIT 3');
		$a=0;
		foreach($emceeRank_weekq3 as $k=>$vo){
		
		$userinfo = D("Member")->find($vo['uid']);
		$emceeRank_weekq3[$a]['userinfo']=$userinfo;
		$emceelevel = getEmceelevel($userinfo['earnbean']);
		$emceeRank_weekq3[$a]['emceelevel']=$emceelevel;
		$a++;
		}
		$this->assign("emceeRank_weekq3",$emceeRank_weekq3);
		//查询出明星周榜的4-9条 并得到用户的相关信息中间6条
			$emceeRank_weekz6 = D('Beandetail')->query('SELECT uid,sum(bean) as total FROM `ss_beandetail` where type="income" and action="getgift" and date_format(FROM_UNIXTIME(addtime),"%Y")=date_format(now(),"%Y") and date_format(FROM_UNIXTIME(addtime),"%u")=date_format(now(),"%u") group by uid order by total desc LIMIT 3,6');
		$a=0;
		$b=3;
		foreach($emceeRank_weekz6 as $k=>$vo){
		
		$userinfo = D("Member")->find($vo['uid']);
		$emceeRank_weekz6[$a]['userinfo']=$userinfo;
		$emceelevel = getEmceelevel($userinfo['earnbean']);
		$emceeRank_weekz6[$a]['emceelevel']=$emceelevel;
		$emceeRank_weekz6[$a]['xuhao']=($b+1);
		$b++;
		$a++;
		}
		//var_dump($emceeRank_dayz6);
		$this->assign("emceeRank_weekz6",$emceeRank_weekz6);
		//查询出明星周榜的10-15条 并得到用户的相关信息hou6条
		$emceeRank_weekh6 = D('Beandetail')->query('SELECT uid,sum(bean) as total FROM `ss_beandetail` where type="income" and action="getgift" and date_format(FROM_UNIXTIME(addtime),"%Y")=date_format(now(),"%Y") and date_format(FROM_UNIXTIME(addtime),"%u")=date_format(now(),"%u") group by uid order by total desc LIMIT 9,6');
		$a=0;
		$b=9;
		foreach($emceeRank_weekh6 as $k=>$vo){
		
		$userinfo = D("Member")->find($vo['uid']);
		$emceeRank_weekh6[$a]['userinfo']=$userinfo;
		$emceelevel = getEmceelevel($userinfo['earnbean']);
		$emceeRank_weekh6[$a]['emceelevel']=$emceelevel;
		$emceeRank_weekh6[$a]['xuhao']=($b+1);
		$b++;
		$a++;
		}
		$this->assign("emceeRank_weekh6",$emceeRank_weekh6);
		
		
		$emceeRank_month = D('Beandetail')->query('SELECT uid,sum(bean) as total FROM `ss_beandetail` where type="income" and action="getgift" and date_format(FROM_UNIXTIME(addtime),"%m-%Y")=date_format(now(),"%m-%Y") group by uid order by total desc LIMIT 20');
		//var_dump($emceeRank_motnth);
		$this->assign('emceeRank_month', $emceeRank_month);
		
			//查询出明星月榜的前三条 并得到用户的相关信息
		$emceeRank_monthq3 = D('Beandetail')->query('SELECT uid,sum(bean) as total FROM `ss_beandetail` where type="income" and action="getgift" and date_format(FROM_UNIXTIME(addtime),"%m-%Y")=date_format(now(),"%m-%Y") group by uid order by total desc LIMIT 3');
		$a=0;
		foreach($emceeRank_monthq3 as $k=>$vo){
		
		$userinfo = D("Member")->find($vo['uid']);
		$emceeRank_monthq3[$a]['userinfo']=$userinfo;
		$emceelevel = getEmceelevel($userinfo['earnbean']);
		$emceeRank_monthq3[$a]['emceelevel']=$emceelevel;
		$a++;
		}
		$this->assign("emceeRank_monthq3",$emceeRank_monthq3);
		//查询出明星月榜的4-9条 并得到用户的相关信息中间6条
			$emceeRank_monthz6 = D('Beandetail')->query('SELECT uid,sum(bean) as total FROM `ss_beandetail` where type="income" and action="getgift" and date_format(FROM_UNIXTIME(addtime),"%m-%Y")=date_format(now(),"%m-%Y") group by uid order by total desc LIMIT 3,6');
		$a=0;
		$b=3;
		foreach($emceeRank_monthz6 as $k=>$vo){
		
		$userinfo = D("Member")->find($vo['uid']);
		$emceeRank_monthz6[$a]['userinfo']=$userinfo;
		$emceelevel = getEmceelevel($userinfo['earnbean']);
		$emceeRank_monthz6[$a]['emceelevel']=$emceelevel;
		$emceeRank_monthz6[$a]['xuhao']=($b+1);
		$b++;
		$a++;
		}
		//var_dump($emceeRank_dayz6);
		$this->assign("emceeRank_monthz6",$emceeRank_monthz6);
		//查询出明星月榜的10-15条 并得到用户的相关信息hou6条
		$emceeRank_monthh6 = D('Beandetail')->query('SELECT uid,sum(bean) as total FROM `ss_beandetail` where type="income" and action="getgift" and date_format(FROM_UNIXTIME(addtime),"%m-%Y")=date_format(now(),"%m-%Y") group by uid order by total desc LIMIT 9,6');
		$a=0;
		$b=9;
		foreach($emceeRank_monthh6 as $k=>$vo){
		
		$userinfo = D("Member")->find($vo['uid']);
		$emceeRank_monthh6[$a]['userinfo']=$userinfo;
		$emceelevel = getEmceelevel($userinfo['earnbean']);
		$emceeRank_monthh6[$a]['emceelevel']=$emceelevel;
		$emceeRank_monthh6[$a]['xuhao']=($b+1);
		$b++;
		$a++;
		}
		$this->assign("emceeRank_monthh6",emceeRank_monthh6);
		
		
		
		
		
		
		
		$emceeRank_all = D('Beandetail')->query('SELECT uid,sum(bean) as total FROM `ss_beandetail` where type="income" and action="getgift" group by uid order by total desc LIMIT 20');
		//查询出明星总榜的前三条 并得到用户的相关信息
		$emceeRank_allq3 = D('Beandetail')->query('SELECT uid,sum(bean) as total FROM `ss_beandetail` where type="income" and action="getgift" group by uid order by total desc LIMIT 3');
		$a=0;
		foreach($emceeRank_allq3 as $k=>$vo){
		
		$userinfo = D("Member")->find($vo['uid']);
		$emceeRank_allq3[$a]['userinfo']=$userinfo;
		$emceelevel = getEmceelevel($userinfo['earnbean']);
		$emceeRank_allq3[$a]['emceelevel']=$emceelevel;
		$a++;
		}
		$this->assign("emceeRank_allq3",$emceeRank_allq3);
		$this->assign('emceeRank_all', $emceeRank_all);
		//查询出明星总榜的4-9条 并得到用户的相关信息中间6条
		$emceeRank_allz6= D('Beandetail')->query('SELECT uid,sum(bean) as total FROM `ss_beandetail` where type="income" and action="getgift" group by uid order by total desc LIMIT 3,6');
		$a=0;
		$b=3;
		foreach($emceeRank_allz6 as $k=>$vo){
		
		$userinfo = D("Member")->find($vo['uid']);
		$emceeRank_allz6[$a]['userinfo']=$userinfo;
		$emceelevel = getEmceelevel($userinfo['earnbean']);
		$emceeRank_allz6[$a]['emceelevel']=$emceelevel;
		$emceeRank_allz6[$a]['xuhao']=($b+1);
		$b++;
		$a++;
		}
		$this->assign("emceeRank_allz6",$emceeRank_allz6);
		//var_dump($emceeRank_allz6);
		
		//查询出明星总榜的10-15条 并得到用户的相关信息hou6条
		$emceeRank_allh6= D('Beandetail')->query('SELECT uid,sum(bean) as total FROM `ss_beandetail` where type="income" and action="getgift" group by uid order by total desc LIMIT 9,6');
		$a=0;
		$b=9;
		foreach($emceeRank_allh6 as $k=>$vo){
		
		$userinfo = D("Member")->find($vo['uid']);
		$emceeRank_allh6[$a]['userinfo']=$userinfo;
		$emceelevel = getEmceelevel($userinfo['earnbean']);
		$emceeRank_allh6[$a]['emceelevel']=$emceelevel;
		$emceeRank_allh6[$a]['xuhao']=($b+1);
		$b++;
		$a++;
		}
		$this->assign("emceeRank_allh6",$emceeRank_allh6);
		//var_dump($emceeRank_allh6);
		
		
		
        //var_dump($emceeRank_allq3);
		//富豪日 周 月 总榜
		$richRank_day = D('Coindetail')->query('SELECT uid,sum(coin) as total FROM `ss_coindetail` where type="expend" and date_format(FROM_UNIXTIME(addtime),"%m-%d-%Y")=date_format(now(),"%m-%d-%Y") group by uid order by total desc LIMIT 20');
		$this->assign('richRank_day', $richRank_day);
		//查询出富豪日榜的前三条
		$richRank_dayq3 = D('Coindetail')->query('SELECT uid,sum(coin) as total FROM `ss_coindetail` where type="expend" and date_format(FROM_UNIXTIME(addtime),"%m-%d-%Y")=date_format(now(),"%m-%d-%Y") group by uid order by total desc LIMIT 3');
		$a=0;
		foreach($richRank_dayq3 as $k=>$vo){
		
		$userinfo = D("Member")->find($vo['uid']);
		$richRank_dayq3[$a]['userinfo']=$userinfo;
		$richlevel = getRichlevel($userinfo['spendcoin']);
		$richRank_dayq3[$a]['richlecel']=$richlevel;
		$a++;
		}

		$this->assign("richRank_dayq3",$richRank_dayq3);
			//查询出富豪日榜的4-9条 并得到用户的相关信息中间6条
		$richRank_dayz6 = D('Coindetail')->query('SELECT uid,sum(coin) as total FROM `ss_coindetail` where type="expend" and date_format(FROM_UNIXTIME(addtime),"%m-%d-%Y")=date_format(now(),"%m-%d-%Y") group by uid order by total desc LIMIT 3,6');
		$a=0;
		$b=3;
		foreach($richRank_dayz6 as $k=>$vo){
		
		$userinfo = D("Member")->find($vo['uid']);
		$richRank_dayz6[$a]['userinfo']=$userinfo;
		$richlevel = getRichlevel($userinfo['spendcoin']);
		$richRank_dayz6[$a]['richlecel']=$richlevel;
		$richRank_dayz6[$a]['xuhao']=($b+1);
		$b++;
		$a++;
		}
		$this->assign("richRank_dayz6",$richRank_dayz6);
		//var_dump($emceeRank_allz6);	
		
			//查询出富豪日榜的10-15条 并得到用户的相关信息中间6条
		$richRank_dayh6 = D('Coindetail')->query('SELECT uid,sum(coin) as total FROM `ss_coindetail` where type="expend" and date_format(FROM_UNIXTIME(addtime),"%m-%d-%Y")=date_format(now(),"%m-%d-%Y") group by uid order by total desc LIMIT 9,6');
		$a=0;
		$b=9;
		foreach($richRank_dayh6 as $k=>$vo){
		
		$userinfo = D("Member")->find($vo['uid']);
		$richRank_dayh6[$a]['userinfo']=$userinfo;
		$richlevel = getRichlevel($userinfo['spendcoin']);
		$richRank_dayh6[$a]['richlevel']=$richlevel;
		$richRank_dayh6[$a]['xuhao']=($b+1);
		$b++;
		$a++;
		}
		$this->assign("richRank_dayh6",$richRank_dayh6);
		
		
		
		$richRank_week = D('Coindetail')->query('SELECT uid,sum(coin) as total FROM `ss_coindetail` where type="expend" and date_format(FROM_UNIXTIME(addtime),"%Y")=date_format(now(),"%Y") and date_format(FROM_UNIXTIME(addtime),"%u")=date_format(now(),"%u") group by uid order by total desc LIMIT 20');
		$this->assign('richRank_week', $richRank_week);
			//查询出富豪周榜的前三条
			$richRank_weekq3 = D('Coindetail')->query('SELECT uid,sum(coin) as total FROM `ss_coindetail` where type="expend" and date_format(FROM_UNIXTIME(addtime),"%Y")=date_format(now(),"%Y") and date_format(FROM_UNIXTIME(addtime),"%u")=date_format(now(),"%u") group by uid order by total desc LIMIT 3');
		$a=0;
		foreach($richRank_weekq3 as $k=>$vo){
		
		$userinfo = D("Member")->find($vo['uid']);
		$richRank_weekq3[$a]['userinfo']=$userinfo;
		$richlevel = getRichlevel($userinfo['spendcoin']);
		$richRank_weekq3[$a]['richlecel']=$richlevel;
		$a++;
		}

		$this->assign("richRank_weekq3",$richRank_weekq3);
			//查询出富豪周榜的4-9条 并得到用户的相关信息中间6条
		$richRank_weekz6 = D('Coindetail')->query('SELECT uid,sum(coin) as total FROM `ss_coindetail` where type="expend" and date_format(FROM_UNIXTIME(addtime),"%Y")=date_format(now(),"%Y") and date_format(FROM_UNIXTIME(addtime),"%u")=date_format(now(),"%u") group by uid order by total desc LIMIT 3,6');
		$a=0;
		$b=3;
		foreach($richRank_weekz6 as $k=>$vo){
		
		$userinfo = D("Member")->find($vo['uid']);
		$richRank_weekz6[$a]['userinfo']=$userinfo;
		$richlevel = getRichlevel($userinfo['spendcoin']);
		$richRank_weekz6[$a]['richlecel']=$richlevel;
		$richRank_weekz6[$a]['xuhao']=($b+1);
		$b++;
		$a++;
		}
		$this->assign("richRank_weekz6",$richRank_weekz6);
		//var_dump($emceeRank_allz6);	
		
			//查询出富豪周榜的10-15条 并得到用户的相关信息中间6条
		$richRank_weekh6 = D('Coindetail')->query('SELECT uid,sum(coin) as total FROM `ss_coindetail` where type="expend" and date_format(FROM_UNIXTIME(addtime),"%Y")=date_format(now(),"%Y") and date_format(FROM_UNIXTIME(addtime),"%u")=date_format(now(),"%u") group by uid order by total desc LIMIT 9,6');
		$a=0;
		$b=9;
		foreach($richRank_weekh6 as $k=>$vo){
		
		$userinfo = D("Member")->find($vo['uid']);
		$richRank_weekh6[$a]['userinfo']=$userinfo;
		$richlevel = getRichlevel($userinfo['spendcoin']);
		$richRank_weekh6[$a]['richlevel']=$richlevel;
		$richRank_weekh6[$a]['xuhao']=($b+1);
		$b++;
		$a++;
		}
		$this->assign("richRank_weekh6",$richRank_weekh6);
		
		
		$richRank_month = D('Coindetail')->query('SELECT uid,sum(coin) as total FROM `ss_coindetail` where type="expend" and date_format(FROM_UNIXTIME(addtime),"%m-%Y")=date_format(now(),"%m-%Y") group by uid order by total desc LIMIT 20');
		$this->assign('richRank_month', $richRank_month);
		  	//查询出富豪月榜的前三条
	$richRank_monthq3 = D('Coindetail')->query('SELECT uid,sum(coin) as total FROM `ss_coindetail` where type="expend" and date_format(FROM_UNIXTIME(addtime),"%m-%Y")=date_format(now(),"%m-%Y") group by uid order by total desc LIMIT 3');
		$a=0;
		foreach($richRank_monthq3 as $k=>$vo){
		
		$userinfo = D("Member")->find($vo['uid']);
		$richRank_monthq3[$a]['userinfo']=$userinfo;
		$richlevel = getRichlevel($userinfo['spendcoin']);
		$richRank_monthq3[$a]['richlecel']=$richlevel;
		$a++;
		}

		$this->assign("richRank_monthq3",$richRank_monthq3);
			//查询出富豪月榜的4-9条 并得到用户的相关信息中间6条
	$richRank_monthz6 = D('Coindetail')->query('SELECT uid,sum(coin) as total FROM `ss_coindetail` where type="expend" and date_format(FROM_UNIXTIME(addtime),"%m-%Y")=date_format(now(),"%m-%Y") group by uid order by total desc LIMIT 3,6');
		$a=0;
		$b=3;
		foreach($richRank_monthz6 as $k=>$vo){
		
		$userinfo = D("Member")->find($vo['uid']);
		$richRank_monthz6[$a]['userinfo']=$userinfo;
		$richlevel = getRichlevel($userinfo['spendcoin']);
		$richRank_monthz6[$a]['richlecel']=$richlevel;
		$richRank_monthz6[$a]['xuhao']=($b+1);
		$b++;
		$a++;
		}
		$this->assign("richRank_monthz6",$richRank_monthz6);
		//var_dump($emceeRank_allz6);	
		
			//查询出富豪月榜的10-15条 并得到用户的相关信息中间6条
	$richRank_monthh6 = D('Coindetail')->query('SELECT uid,sum(coin) as total FROM `ss_coindetail` where type="expend" and date_format(FROM_UNIXTIME(addtime),"%m-%Y")=date_format(now(),"%m-%Y") group by uid order by total desc LIMIT 9,6');
		$a=0;
		$b=9;
		foreach($richRank_monthh6 as $k=>$vo){
		
		$userinfo = D("Member")->find($vo['uid']);
		$richRank_monthh6[$a]['userinfo']=$userinfo;
		$richlevel = getRichlevel($userinfo['spendcoin']);
		$richRank_monthh6[$a]['richlevel']=$richlevel;
		$richRank_monthh6[$a]['xuhao']=($b+1);
		$b++;
		$a++;
		}
		$this->assign("richRank_monthh6",$richRank_monthh6);
		
		
		
		$richRank_all = D('Coindetail')->query('SELECT uid,sum(coin) as total FROM `ss_coindetail` where type="expend" group by uid order by total desc LIMIT 20');
		$this->assign('richRank_all', $richRank_all);
		//查询出富豪总榜的前三条
		$richRank_allq3 = D('Coindetail')->query('SELECT uid,sum(coin) as total FROM `ss_coindetail` where type="expend" group by uid order by total desc LIMIT 3');
		$a=0;
		foreach($richRank_allq3 as $k=>$vo){
		
		$userinfo = D("Member")->find($vo['uid']);
		$richRank_allq3[$a]['userinfo']=$userinfo;
		$richlevel = getRichlevel($userinfo['spendcoin']);
		$richRank_allq3[$a]['richlecel']=$richlevel;
		$a++;
		}

		$this->assign("richRank_allq3",$richRank_allq3);
			//查询出富豪总榜的4-9条 并得到用户的相关信息中间6条
		$richRank_allz6 = D('Coindetail')->query('SELECT uid,sum(coin) as total FROM `ss_coindetail` where type="expend" group by uid order by total desc LIMIT 3,6');
		$a=0;
		$b=3;
		foreach($richRank_allz6 as $k=>$vo){
		
		$userinfo = D("Member")->find($vo['uid']);
		$richRank_allz6[$a]['userinfo']=$userinfo;
		$richlevel = getRichlevel($userinfo['spendcoin']);
		$richRank_allz6[$a]['richlecel']=$richlevel;
		$richRank_allz6[$a]['xuhao']=($b+1);
		$b++;
		$a++;
		}
		$this->assign("richRank_allz6",$richRank_allz6);
		//var_dump($emceeRank_allz6);	
		
			//查询出富豪总榜的10-15条 并得到用户的相关信息中间6条
		$richRank_allh6 = D('Coindetail')->query('SELECT uid,sum(coin) as total FROM `ss_coindetail` where type="expend" group by uid order by total desc LIMIT 9,6');
		$a=0;
		$b=9;
		foreach($richRank_allh6 as $k=>$vo){
		
		$userinfo = D("Member")->find($vo['uid']);
		$richRank_allh6[$a]['userinfo']=$userinfo;
		$richlevel = getRichlevel($userinfo['spendcoin']);
		$richRank_allh6[$a]['richlevel']=$richlevel;
		$richRank_allh6[$a]['xuhao']=($b+1);
		$b++;
		$a++;
		}
		$this->assign("richRank_allh6",$richRank_allh6);
		//var_dump($richRank_allh6);

		//人气日 周 月 总榜
		$rqRank_day = D('Liverecord')->query('SELECT uid,sum(entercount) as total FROM `ss_liverecord` where date_format(FROM_UNIXTIME(starttime),"%m-%d-%Y")=date_format(now(),"%m-%d-%Y") group by uid order by total desc LIMIT 20');
		$this->assign('rqRank_day', $rqRank_day);
		
				//查询出人气日榜的前三条 并得到用户的相关信息
		$rqRank_dayq3 = D('Liverecord')->query('SELECT uid,sum(entercount) as total FROM `ss_liverecord` where date_format(FROM_UNIXTIME(starttime),"%m-%d-%Y")=date_format(now(),"%m-%d-%Y") group by uid order by total desc LIMIT 3');
		$a=0;
		foreach($rqRank_dayq3 as $k=>$vo){
		
		$userinfo = D("Member")->find($vo['uid']);
		$rqRank_dayq3[$a]['userinfo']=$userinfo;
		$emceelevel = getEmceelevel($userinfo['earnbean']);
		$rqRank_dayq3[$a]['emceelevel']=$emceelevel;
		$a++;
		}

		$this->assign("rqRank_dayq3",$rqRank_dayq3);
			//查询出人气日榜的4-9条 并得到用户的相关信息中间6条
		$rqRank_dayz6 = D('Liverecord')->query('SELECT uid,sum(entercount) as total FROM `ss_liverecord` where date_format(FROM_UNIXTIME(starttime),"%m-%d-%Y")=date_format(now(),"%m-%d-%Y") group by uid order by total desc LIMIT 3,6');
		$a=0;
		$b=3;
		foreach($rqRank_dayz6 as $k=>$vo){
		
		$userinfo = D("Member")->find($vo['uid']);
		$rqRank_dayz6[$a]['userinfo']=$userinfo;
		$emceelevel = getEmceelevel($userinfo['earnbean']);
		$rqRank_dayz6[$a]['emceelevel']=$emceelevel;
		$rqRank_dayz6[$a]['xuhao']=($b+1);
		$b++;
		$a++;
		}
		$this->assign("rqRank_dayz6",$rqRank_dayz6);
		//var_dump($emceeRank_allz6);	
		
			//查询出人气日榜的10-15条 并得到用户的相关信息后6条
		$rqRank_dayh6 = D('Liverecord')->query('SELECT uid,sum(entercount) as total FROM `ss_liverecord` where date_format(FROM_UNIXTIME(starttime),"%m-%d-%Y")=date_format(now(),"%m-%d-%Y") group by uid order by total desc LIMIT 9,6');
		$a=0;
		$b=9;
		foreach($rqRank_dayh6 as $k=>$vo){
		
		$userinfo = D("Member")->find($vo['uid']);
		$rqRank_dayh6[$a]['userinfo']=$userinfo;
		$emceelevel = getEmceelevel($userinfo['earnbean']);
		$rqRank_dayh6[$a]['emceelevel']=$emceelevel;
		$rqRank_dayh6[$a]['xuhao']=($b+1);
		$b++;
		$a++;
		}
		$this->assign("rqRank_dayh6",$rqRank_dayh6);
		
		
		$rqRank_week = D('Liverecord')->query('SELECT uid,sum(entercount) as total FROM `ss_liverecord` where date_format(FROM_UNIXTIME(starttime),"%Y")=date_format(now(),"%Y") and date_format(FROM_UNIXTIME(starttime),"%u")=date_format(now(),"%u") group by uid order by total desc LIMIT 20');
		$this->assign('rqRank_week', $rqRank_week);
						//查询出人气周榜的前三条 并得到用户的相关信息
		$rqRank_weekq3 = D('Liverecord')->query('SELECT uid,sum(entercount) as total FROM `ss_liverecord` where date_format(FROM_UNIXTIME(starttime),"%Y")=date_format(now(),"%Y") and date_format(FROM_UNIXTIME(starttime),"%u")=date_format(now(),"%u") group by uid order by total desc LIMIT 3');
		$a=0;
		foreach($rqRank_weekq3 as $k=>$vo){
		
		$userinfo = D("Member")->find($vo['uid']);
		$rqRank_weekq3[$a]['userinfo']=$userinfo;
		$emceelevel = getEmceelevel($userinfo['earnbean']);
		$rqRank_weekq3[$a]['emceelevel']=$emceelevel;
		$a++;
		}

		$this->assign("rqRank_weekq3",$rqRank_weekq3);
			//查询出人气周榜的4-9条 并得到用户的相关信息中间6条
		$rqRank_weekz6 = D('Liverecord')->query('SELECT uid,sum(entercount) as total FROM `ss_liverecord` where date_format(FROM_UNIXTIME(starttime),"%Y")=date_format(now(),"%Y") and date_format(FROM_UNIXTIME(starttime),"%u")=date_format(now(),"%u") group by uid order by total desc LIMIT 3,6');
		$a=0;
		$b=3;
		foreach($rqRank_weekz6 as $k=>$vo){
		
		$userinfo = D("Member")->find($vo['uid']);
		$rqRank_weekz6[$a]['userinfo']=$userinfo;
		$emceelevel = getEmceelevel($userinfo['earnbean']);
		$rqRank_weekz6[$a]['emceelevel']=$emceelevel;
		$rqRank_weekz6[$a]['xuhao']=($b+1);
		$b++;
		$a++;
		}
		$this->assign("rqRank_weekz6",$rqRank_weekz6);
		//var_dump($emceeRank_allz6);	
		
			//查询出人气日榜的10-15条 并得到用户的相关信息后6条
		$rqRank_weekh6 = D('Liverecord')->query('SELECT uid,sum(entercount) as total FROM `ss_liverecord` where date_format(FROM_UNIXTIME(starttime),"%Y")=date_format(now(),"%Y") and date_format(FROM_UNIXTIME(starttime),"%u")=date_format(now(),"%u") group by uid order by total desc LIMIT 9,6');
		$a=0;
		$b=9;
		foreach($rqRank_weekh6 as $k=>$vo){
		
		$userinfo = D("Member")->find($vo['uid']);
		$rqRank_weekh6[$a]['userinfo']=$userinfo;
		$emceelevel = getEmceelevel($userinfo['earnbean']);
		$rqRank_weekh6[$a]['emceelevel']=$emceelevel;
		$rqRank_weekh6[$a]['xuhao']=($b+1);
		$b++;
		$a++;
		}
		$this->assign("rqRank_weekh6",$rqRank_weekh6);
		
		
		
		$rqRank_month = D('Liverecord')->query('SELECT uid,sum(entercount) as total FROM `ss_liverecord` where date_format(FROM_UNIXTIME(starttime),"%m-%Y")=date_format(now(),"%m-%Y") group by uid order by total desc LIMIT 20');
		$this->assign('rqRank_month', $rqRank_month);
						//查询出人气月榜的前三条 并得到用户的相关信息
	$rqRank_monthq3 = D('Liverecord')->query('SELECT uid,sum(entercount) as total FROM `ss_liverecord` where date_format(FROM_UNIXTIME(starttime),"%m-%Y")=date_format(now(),"%m-%Y") group by uid order by total desc LIMIT 3');
		$a=0;
		foreach($rqRank_monthq3 as $k=>$vo){
		
		$userinfo = D("Member")->find($vo['uid']);
		$rqRank_monthq3[$a]['userinfo']=$userinfo;
		$emceelevel = getEmceelevel($userinfo['earnbean']);
		$rqRank_monthq3[$a]['emceelevel']=$emceelevel;
		$a++;
		}

		$this->assign("rqRank_monthq3",$rqRank_monthq3);
			//查询出人气月榜的4-9条 并得到用户的相关信息中间6条
		$rqRank_monthz6 = D('Liverecord')->query('SELECT uid,sum(entercount) as total FROM `ss_liverecord` where date_format(FROM_UNIXTIME(starttime),"%m-%Y")=date_format(now(),"%m-%Y") group by uid order by total desc LIMIT 3,6');
		$a=0;
		$b=3;
		foreach($rqRank_monthz6 as $k=>$vo){
		
		$userinfo = D("Member")->find($vo['uid']);
		$rqRank_monthz6[$a]['userinfo']=$userinfo;
		$emceelevel = getEmceelevel($userinfo['earnbean']);
		$rqRank_monthz6[$a]['emceelevel']=$emceelevel;
		$rqRank_monthz6[$a]['xuhao']=($b+1);
		$b++;
		$a++;
		}
		$this->assign("rqRank_monthz6",$rqRank_monthz6);
		//var_dump($emceeRank_allz6);	
		
			//查询出人气月榜的10-15条 并得到用户的相关信息后6条
	$rqRank_monthh6 = D('Liverecord')->query('SELECT uid,sum(entercount) as total FROM `ss_liverecord` where date_format(FROM_UNIXTIME(starttime),"%m-%Y")=date_format(now(),"%m-%Y") group by uid order by total desc LIMIT 9,6');
		$a=0;
		$b=9;
		foreach($rqRank_monthh6 as $k=>$vo){
		
		$userinfo = D("Member")->find($vo['uid']);
		$rqRank_monthh6[$a]['userinfo']=$userinfo;
		$emceelevel = getEmceelevel($userinfo['earnbean']);
		$rqRank_monthh6[$a]['emceelevel']=$emceelevel;
		$rqRank_monthh6[$a]['xuhao']=($b+1);
		$b++;
		$a++;
		}
		$this->assign("rqRank_monthh6",$rqRank_monthh6);
		
		
		
		
		
		$rqRank_all = D('Liverecord')->query('SELECT uid,sum(entercount) as total FROM `ss_liverecord` group by uid order by total desc LIMIT 20');
		//var_dump($rqRank_all);
		//查询出人气总榜的前三条 并得到用户的相关信息
		$rqRank_allq3 = D('Liverecord')->query('SELECT uid,sum(entercount) as total FROM `ss_liverecord` group by uid order by total desc LIMIT 3');
		$a=0;
		foreach($rqRank_allq3 as $k=>$vo){
		
		$userinfo = D("Member")->find($vo['uid']);
		$rqRank_allq3[$a]['userinfo']=$userinfo;
		$emceelevel = getEmceelevel($userinfo['earnbean']);
		$rqRank_allq3[$a]['emceelevel']=$emceelevel;
		$a++;
		}

		$this->assign("rqRank_allq3",$rqRank_allq3);
			//查询出人气总榜的4-9条 并得到用户的相关信息中间6条
		$rqRank_allz6 = D('Liverecord')->query('SELECT uid,sum(entercount) as total FROM `ss_liverecord` group by uid order by total desc LIMIT 3,6');
		$a=0;
		$b=3;
		foreach($rqRank_allz6 as $k=>$vo){
		
		$userinfo = D("Member")->find($vo['uid']);
		$rqRank_allz6[$a]['userinfo']=$userinfo;
		$emceelevel = getEmceelevel($userinfo['earnbean']);
		$rqRank_allz6[$a]['emceelevel']=$emceelevel;
		$rqRank_allz6[$a]['xuhao']=($b+1);
		$b++;
		$a++;
		}
		$this->assign("rqRank_allz6",$rqRank_allz6);
		//var_dump($emceeRank_allz6);	
		
			//查询出人气总榜的10-15条 并得到用户的相关信息中间6条
		$rqRank_allh6 = D('Liverecord')->query('SELECT uid,sum(entercount) as total FROM `ss_liverecord` group by uid order by total desc LIMIT 9,6');
		$a=0;
		$b=9;
		foreach($rqRank_allh6 as $k=>$vo){
		
		$userinfo = D("Member")->find($vo['uid']);
		$rqRank_allh6[$a]['userinfo']=$userinfo;
		$emceelevel = getEmceelevel($userinfo['earnbean']);
		$rqRank_allh6[$a]['emceelevel']=$emceelevel;
		$rqRank_allh6[$a]['xuhao']=($b+1);
		$b++;
		$a++;
		}
		$this->assign("rqRank_allh6",$rqRank_allh6);
		//var_dump($emceeRank_allz6);	
		
		
		
		
		$this->assign('rqRank_all', $rqRank_all);

		$gifts = D('Gift')->order('needcoin desc')->select();
		$this->assign('gifts', $gifts);
		//获得礼物最多的人z总榜 
		$gifts_toall=M("coindetail")->query("SELECT touid,giftid,sum(giftcount) as total FROM `ss_coindetail` group by giftid order by total desc ");
		//按照礼物总量排序  礼物分组 最多的人数前三条
		$gifts_toallq3=M("coindetail")->query("SELECT touid,giftid,sum(giftcount) as total FROM `ss_coindetail` group by giftid order by total desc limit 3 ");
		$a=0;
		foreach($gifts_toallq3 as $k=>$vo){
		
		$userinfo = D("Member")->find($vo['touid']);
		$gifts_toallq3[$a]['userinfo']=$userinfo;
		$emceelevel = getEmceelevel($userinfo['earnbean']);
		$gifts_toallq3[$a]['emceelevel']=$emceelevel;
		$giftinfo=D("gift")->find($vo['giftid']);
		$gifts_toallq3[$a]['giftinfo']=$giftinfo;
		$a++;
		}
         //var_dump($gifts_toallq3);
		$this->assign("gifts_toallq3",$gifts_toallq3);
		//按照礼物总量排序  礼物分组 最多的人数中间6条
		$gifts_toallz6=M("coindetail")->query("SELECT touid,giftid,sum(giftcount) as total FROM `ss_coindetail` group by giftid order by total desc limit 3,6 ");
		$a=0;
		$b=3;
		foreach($gifts_toallz6 as $k=>$vo){
		
		$userinfo = D("Member")->find($vo['touid']);
		$gifts_toallz6[$a]['userinfo']=$userinfo;
		$emceelevel = getEmceelevel($userinfo['earnbean']);
		$gifts_toallz6[$a]['emceelevel']=$emceelevel;
		$giftinfo=D("gift")->find($vo['giftid']);
		$gifts_toallz6[$a]['giftinfo']=$giftinfo;
		$gifts_toallz6[$a]['xuhao']=($b+1);
		$a++;
		$b++;
		}
              
		$this->assign("gifts_toallz6",$gifts_toallz6);
		
						//按照礼物总量排序  礼物分组 最多的人数中间6条
		$gifts_toallh6=M("coindetail")->query("SELECT touid,giftid,sum(giftcount) as total FROM `ss_coindetail` group by giftid order by total desc limit 9,6 ");
		$a=0;
		$b=9;
		foreach($gifts_toallh6 as $k=>$vo){
		
		$userinfo = D("Member")->find($vo['touid']);
		$gifts_toallh6[$a]['userinfo']=$userinfo;
		$emceelevel = getEmceelevel($userinfo['earnbean']);
		$gifts_toallh6[$a]['emceelevel']=$emceelevel;
		$giftinfo=D("gift")->find($vo['giftid']);
		$gifts_toallh6[$a]['giftinfo']=$giftinfo;
		$gifts_toallh6[$a]['xuhao']=($b+1);
		$a++;
		$b++;
		}
              
		$this->assign("gifts_toallh6",$gifts_toallh6);
		//礼物日榜		
		$gifts_day = D('coindetail')->query('SELECT touid,giftid,sum(giftcount) as total FROM `ss_coindetail` where date_format(FROM_UNIXTIME(addtime),"%m-%d-%Y")=date_format(now(),"%m-%d-%Y") group by giftid order by total desc ');
		//礼物周榜
		$gifts_week = D('coindetail')->query('SELECT touid,giftid,sum(giftcount) as total FROM `ss_coindetail` where date_format(FROM_UNIXTIME(addtime),"%Y")=date_format(now(),"%Y") and date_format(FROM_UNIXTIME(addtime),"%u")=date_format(now(),"%u") group by giftid order by total desc ');
		//礼物月榜
		$gifts_month = D('coindetail')->query('SELECT touid,giftid,sum(giftcount) as total FROM `ss_coindetail` where date_format(FROM_UNIXTIME(addtime),"%m-%Y")=date_format(now(),"%m-%Y") group by giftid order by total desc ');
		//var_dump($gifts_month);
		//礼物日榜相关数据
		//按照礼物总量排序  礼物分组 最多的人数前三条
	$gifts_dayq3 = D('coindetail')->query('SELECT touid,giftid,sum(giftcount) as total FROM `ss_coindetail` where date_format(FROM_UNIXTIME(addtime),"%m-%d-%Y")=date_format(now(),"%m-%d-%Y") group by giftid order by total desc limit 3');
		$a=0;
		foreach($gifts_dayq3 as $k=>$vo){
		
		$userinfo = D("Member")->find($vo['touid']);
		$gifts_dayq3[$a]['userinfo']=$userinfo;
		$emceelevel = getEmceelevel($userinfo['earnbean']);
		$gifts_dayq3[$a]['emceelevel']=$emceelevel;
		$giftinfo=D("gift")->find($vo['giftid']);
		$gifts_dayq3[$a]['giftinfo']=$giftinfo;
		$a++;
		}
         //var_dump($gifts_toallq3);
		$this->assign("gifts_dayq3",$gifts_dayq3);
		//按照礼物总量排序  礼物分组 最多的人数中间6条
	$gifts_dayz6 = D('coindetail')->query('SELECT touid,giftid,sum(giftcount) as total FROM `ss_coindetail` where date_format(FROM_UNIXTIME(addtime),"%m-%d-%Y")=date_format(now(),"%m-%d-%Y") group by giftid order by total desc limit 3,6');
		$a=0;
		$b=3;
		foreach($gifts_dayz6 as $k=>$vo){
		
		$userinfo = D("Member")->find($vo['touid']);
		$gifts_dayz6[$a]['userinfo']=$userinfo;
		$emceelevel = getEmceelevel($userinfo['earnbean']);
		$gifts_dayz6[$a]['emceelevel']=$emceelevel;
		$giftinfo=D("gift")->find($vo['giftid']);
		$gifts_dayz6[$a]['giftinfo']=$giftinfo;
		$gifts_dayz6[$a]['xuhao']=($b+1);
		$a++;
		$b++;
		}
              
		$this->assign("gifts_dayz6",$gifts_dayz6);
		
						//按照礼物总量排序  礼物分组 最多的人数中间6条
		$gifts_dayh6 = D('coindetail')->query('SELECT touid,giftid,sum(giftcount) as total FROM `ss_coindetail` where date_format(FROM_UNIXTIME(addtime),"%m-%d-%Y")=date_format(now(),"%m-%d-%Y") group by giftid order by total desc limit 9,6');
		$a=0;
		$b=9;
		foreach($gifts_dayh6 as $k=>$vo){
		
		$userinfo = D("Member")->find($vo['touid']);
		$gifts_dayh6[$a]['userinfo']=$userinfo;
		$emceelevel = getEmceelevel($userinfo['earnbean']);
		$gifts_dayh6[$a]['emceelevel']=$emceelevel;
		$giftinfo=D("gift")->find($vo['giftid']);
		$gifts_dayh6[$a]['giftinfo']=$giftinfo;
		$gifts_dayh6[$a]['xuhao']=($b+1);
		$a++;
		$b++;
		}
              
		$this->assign("gifts_dayh6",$gifts_dayh6);
		
		//礼物周榜相关数据
		//按照礼物总量排序  礼物分组 最多的人数前三条
	$gifts_weekq3 = D('coindetail')->query('SELECT touid,giftid,sum(giftcount) as total FROM `ss_coindetail` where date_format(FROM_UNIXTIME(addtime),"%Y")=date_format(now(),"%Y") and date_format(FROM_UNIXTIME(addtime),"%u")=date_format(now(),"%u") group by giftid order by total desc limit 3');
		$a=0;
		foreach($gifts_weekq3 as $k=>$vo){
		
		$userinfo = D("Member")->find($vo['touid']);
		$gifts_weekq3[$a]['userinfo']=$userinfo;
		$emceelevel = getEmceelevel($userinfo['earnbean']);
		$gifts_weekq3[$a]['emceelevel']=$emceelevel;
		$giftinfo=D("gift")->find($vo['giftid']);
		$gifts_weekq3[$a]['giftinfo']=$giftinfo;
		$a++;
		}
         //var_dump($gifts_toallq3);
		$this->assign("gifts_weekq3",$gifts_weekq3);
		//按照礼物总量排序  礼物分组 最多的人数中间6条
		$gifts_weekz6 = D('coindetail')->query('SELECT touid,giftid,sum(giftcount) as total FROM `ss_coindetail` where date_format(FROM_UNIXTIME(addtime),"%Y")=date_format(now(),"%Y") and date_format(FROM_UNIXTIME(addtime),"%u")=date_format(now(),"%u") group by giftid order by total desc limit 3,6');
		$a=0;
		$b=3;
		foreach($gifts_weekz6 as $k=>$vo){
		
		$userinfo = D("Member")->find($vo['touid']);
		$gifts_weekz6[$a]['userinfo']=$userinfo;
		$emceelevel = getEmceelevel($userinfo['earnbean']);
		$gifts_weekz6[$a]['emceelevel']=$emceelevel;
		$giftinfo=D("gift")->find($vo['giftid']);
		$gifts_weekz6[$a]['giftinfo']=$giftinfo;
		$gifts_weekz6[$a]['xuhao']=($b+1);
		$a++;
		$b++;
		}
              
		$this->assign("gifts_weekz6",$gifts_weekz6);
		
						//按照礼物总量排序  礼物分组 最多的人数中间6条
		$gifts_weekh6 = D('coindetail')->query('SELECT touid,giftid,sum(giftcount) as total FROM `ss_coindetail` where date_format(FROM_UNIXTIME(addtime),"%Y")=date_format(now(),"%Y") and date_format(FROM_UNIXTIME(addtime),"%u")=date_format(now(),"%u") group by giftid order by total desc limit 9,6');
		$a=0;
		$b=9;
		foreach($gifts_weekh6 as $k=>$vo){
		
		$userinfo = D("Member")->find($vo['touid']);
		$gifts_weekh6[$a]['userinfo']=$userinfo;
		$emceelevel = getEmceelevel($userinfo['earnbean']);
		$gifts_weekh6[$a]['emceelevel']=$emceelevel;
		$giftinfo=D("gift")->find($vo['giftid']);
		$gifts_weekh6[$a]['giftinfo']=$giftinfo;
		$gifts_weekh6[$a]['xuhao']=($b+1);
		$a++;
		$b++;
		}
              
		$this->assign("gifts_weekh6",$gifts_weekh6);
			//礼物月榜相关数据
		//按照礼物总量排序  礼物分组 最多的人数前三条
	$gifts_monthq3 = D('coindetail')->query('SELECT,touid,giftid,sum(giftcount) as total FROM `ss_coindetail` where date_format(FROM_UNIXTIME(addtime),"%m-%Y")=date_format(now(),"%m-%Y") group by giftid order by total desc limit 3');
	//var_dump($gifts_monthq3);
		$a=0;
		foreach($gifts_monthq3 as $k=>$vo){
		
		$userinfo = D("Member")->find($vo['touid']);
		$gifts_monthq3[$a]['userinfo']=$userinfo;
		$emceelevel = getEmceelevel($userinfo['earnbean']);
		$gifts_monthq3[$a]['emceelevel']=$emceelevel;
		$giftinfo=D("gift")->find($vo['giftid']);
		$gifts_monthq3[$a]['giftinfo']=$giftinfo;
		$a++;
		}
        //var_dump($gifts_monthq3);
		$this->assign("gifts_monthq3",$gifts_monthq3);
		//按照礼物总量排序  礼物分组 最多的人数中间6条
		$gifts_monthz6 = D('coindetail')->query('SELECT touid,giftid,sum(giftcount) as total FROM `ss_coindetail` where date_format(FROM_UNIXTIME(addtime),"%m-%Y")=date_format(now(),"%m-%Y") group by giftid order by total desc limit 3,6');
		$a=0;
		$b=3;
		foreach($gifts_monthz6 as $k=>$vo){
		
		$userinfo = D("Member")->find($vo['touid']);
		$gifts_monthz6[$a]['userinfo']=$userinfo;
		$emceelevel = getEmceelevel($userinfo['earnbean']);
		$gifts_monthz6[$a]['emceelevel']=$emceelevel;
		$giftinfo=D("gift")->find($vo['giftid']);
		$gifts_monthz6[$a]['giftinfo']=$giftinfo;
		$gifts_monthz6[$a]['xuhao']=($b+1);
		$a++;
		$b++;
		}
              
		$this->assign("gifts_monthz6",$gifts_monthz6);
		
						//按照礼物总量排序  礼物分组 最多的人数中间6条
		$gifts_monthh6 = D('coindetail')->query('SELECT touid,giftid,sum(giftcount) as total FROM `ss_coindetail` where date_format(FROM_UNIXTIME(addtime),"%m-%Y")=date_format(now(),"%m-%Y") group by giftid order by total desc limit 9,6');
		$a=0;
		$b=9;
		foreach($gifts_monthh6 as $k=>$vo){
		
		$userinfo = D("Member")->find($vo['touid']);
		$gifts_monthh6[$a]['userinfo']=$userinfo;
		$emceelevel = getEmceelevel($userinfo['earnbean']);
		$gifts_monthh6[$a]['emceelevel']=$emceelevel;
		$giftinfo=D("gift")->find($vo['giftid']);
		$gifts_monthh6[$a]['giftinfo']=$giftinfo;
		$gifts_monthh6[$a]['xuhao']=($b+1);
		$a++;
		$b++;
		}
              
		$this->assign("gifts_monthh6",$gifts_monthh6);
		
		
		//本周礼物周星
			$gifts_week10 = D('coindetail')->query('SELECT touid,uid,giftid,sum(giftcount) as total FROM `ss_coindetail` where date_format(FROM_UNIXTIME(addtime),"%Y")=date_format(now(),"%Y") and date_format(FROM_UNIXTIME(addtime),"%u")=date_format(now(),"%u") group by giftid order by total desc limit 10');
           $a=0;
		foreach($gifts_week10 as $k=>$vo){
		$fromuser=D("Member")->find($vo['uid']);
		$gifts_week10[$a]['formuser']=$formuser;
		$userinfo = D("Member")->find($vo['touid']);
		$gifts_week10[$a]['userinfo']=$userinfo;
		$emceelevel = getEmceelevel($userinfo['earnbean']);
		$gifts_week10[$a]['emceelevel']=$emceelevel;
		$giftinfo=D("gift")->find($vo['giftid']);
		$gifts_week10[$a]['giftinfo']=$giftinfo;
		$gifts_week10[$a]['xuhao']=($b+1);
		$a++;
		}
		//var_dump($gifts_week10);
		$this->assign("gifts_week10",$gifts_week10);
		
		//上周礼物周星
		$shangzhougifts = D('Coindetail')->query('SELECT touid,uid,giftid,sum(giftcount) as total FROM `ss_coindetail` where  date_format(FROM_UNIXTIME(addtime),"%Y")=date_format(now(),"%Y") and date_format(FROM_UNIXTIME(addtime),"%u")=date_format(now(),"%u")-1 group by giftid order by total desc LIMIT 10');
		 $a=0;
		foreach($shangzhougifts as $k=>$vo){
		$fromuser=D("Member")->find($vo['uid']);
		$shangzhougifts[$a]['formuser']=$formuser;
		$userinfo = D("Member")->find($vo['touid']);
		$shangzhougifts[$a]['userinfo']=$userinfo;
		$emceelevel = getEmceelevel($userinfo['earnbean']);
		$shangzhougifts[$a]['emceelevel']=$emceelevel;
		$giftinfo=D("gift")->find($vo['giftid']);
		$shangzhougifts[$a]['giftinfo']=$giftinfo;
		$shangzhougifts[$a]['xuhao']=($b+1);
		$a++;
		}
		//var_dump($gifts_week10);
		$this->assign("shangzhougifts",$shangzhougifts);
        $this->display();
    }
}