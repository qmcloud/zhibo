<?php

class GuardAction extends Action{

	public function index(){
		if(!$_SESSION['uid'] || $_SESSION['uid'] < 0){
			$this->assign('jumpUrl',__APP__);
			$this->error('您没有登录，请登录');

		}else{
			$this->assign('showid',$_GET['id']);
			$this->display();
		}
    }

	public function buTool(){
		C('HTML_CACHE_ON',false);
		if(!$_SESSION['uid'] || $_SESSION['uid'] < 0){
			echo '{"msg":"请重新登录"}';
			exit;
		}
        $userinfo = M('Member')->find($_SESSION['uid']);
        if($_GET['toolsubid'] == 1){
        	$needcoin = 20000;
        	$duration = 3600*24*30*1;
        	$duration1 = "1个月";
        }elseif($_GET['toolsubid'] == 2) {
        $needcoin = 40000;
        $duration = 3600*24*30*3;
        $duration1 = "3个月";
        }elseif($_GET['toolsubid'] == 3){
        	$needcoin = 70000;
        	$duration = 3600*24*30*6;
        	$duration1 = "6个月";
        }elseif($_GET['toolsubid'] == 4){
        	$needcoin = 130000;
        	$duration = 3600*24*30*12;
        	$duration1 = "12个月";
        }
        if($userinfo['coinbalance'] < $needcoin){
        	echo '{"msg":"余额不足,请充值"}';
        	exit;
        }else{
            // $data['']
        	D("Member")->execute('update ss_member set spendcoin=spendcoin+'.$needcoin.',coinbalance=coinbalance-'.$needcoin.' where id='.$_SESSION['uid']);
            $userinfo=M('Member')->where('id='.$_SESSION['uid'])->find();
            if($userinfo['Daoju9expire'] < time()){
                D('Member')->execute('update ss_member set Daoju9="y",Daoju9expire='.(time()+$duration).' where id='.$_SESSION['uid']);
            }else{
                D('Member')->execute('update ss_member set Daoju9="y",Daoju9expire=Daoju9expire+'.$duration.' where id='.$_SESSION['uid']);
            }
            D('Member')->execute('update ss_member set beanbalance=beanbalance+'.($needcoin/2).' where curroomnum='.$_GET['toolid']);
           
            $anchorinfo =  D('Member')->where('curroomnum='.$_GET['toolid'])->find();

        	 $condition['anchorid'] = $anchorinfo['id'];
        	 $condition['userid'] = $_SESSION['uid'];
        	 $condition['_logic'] = "and";
        	 if($guardinfo=M('guard')->where($condition)->select()){
        	 	if(time() < $guardinfo[0]['maturitytime']){
        	 	D('guard')->execute('update ss_guard set maturitytime=maturitytime+'.$duration. ' where anchorid='.$anchorinfo['id']);
        	 	//写入消费明细
        	 	$Coindetail = D("Coindetail");
        	 	$Coindetail->create();
        	 	$Coindetail->type = 'expend';
        	 	$Coindetail->action = 'buy';
        	 	$Coindetail->uid = $_SESSION['uid'];
        	 	$Coindetail->giftcount = 1;
        	 	$Coindetail->content = '您购买了 '.$duration1.' 守护';
        	 	$Coindetail->objectIcon = '/Public/images/shou.png';
        	 	$Coindetail->coin = $needcoin;
        	 	$Coindetail->addtime = time();
        	 	$detailId = $Coindetail->add();
        	 	 echo '{"msg":"购买成功"}';
        	 	}else{
        	 		D('guard')->execute('update ss_guard set maturitytime='.time()+$duration. ' where anchorid='.$_GET['toolid']);
        	 		//写入消费明细
        	 		$Coindetail = D("Coindetail");
        	 		$Coindetail->create();
        	 		$Coindetail->type = 'expend';
        	 		$Coindetail->action = 'buy';
        	 		$Coindetail->uid = $_SESSION['uid'];
        	 		$Coindetail->giftcount = 1;
        	 		$Coindetail->content = '您购买了 '.$duration1.' 守护';
        	 		$Coindetail->objectIcon = '/Public/images/shou.png';
        	 		$Coindetail->coin = $needcoin;
        	 		$Coindetail->addtime = time();
        	 		$detailId = $Coindetail->add();
        	 		 echo '{"msg":"购买成功"}';
        	 	}
        	 }else{
        	 	// echo '{"msg":"xx44444xxx"}';
        	 	$data['cleartime'] = time();
                $data['maturitytime'] = time()+$duration;
                $data['anchorid'] = $anchorinfo['id'];
                $data['userid'] = $_SESSION['uid'];
                D('guard')->add($data);
                //写入消费明细
                $Coindetail = D("Coindetail");
                $Coindetail->create();
                $Coindetail->type = 'expend';
                $Coindetail->action = 'buy';
                $Coindetail->uid = $_SESSION['uid'];
                $Coindetail->giftcount = 1;
                $Coindetail->content = '您购买了 '.$duration1.'守护';
                $Coindetail->objectIcon = '/Public/images/shou.png';
                $Coindetail->coin = $needcoin;
                $Coindetail->addtime = time();
                $detailId = $Coindetail->add();
                echo '{"msg":"购买成功"}';
        	 }
        }
	}
}
?>