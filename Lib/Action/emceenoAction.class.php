<?php
class emceenoAction extends BaseAction {
    public function index(){
		
        $this->display();
    }

	public function emceeno_refreshSaleNoList(){
		C('HTML_CACHE_ON',false);

		$goodnums = D('Goodnum')->where('length='.$_GET['length'].' and issale="n"')->order('rand()')->limit(4)->select();
		echo '[';
		if($goodnums){
			$i = 1;
			foreach($goodnums as $val){
				echo '{"num":'.$val['num'].',"price":'.$val['price'].'}';
				if($i != count($goodnums)){echo ',';}
				$i++;
			}
		}
		echo ']';
	}

	public function emceeno_search_emceeno(){
		C('HTML_CACHE_ON',false);

		$goodnums = D('Goodnum')->where('num='.$_GET['num'])->limit(1)->select();
		if($goodnums){
			if($goodnums[0]['issale'] == 'y'){
				$emceeinfo = D("Member")->find($goodnums[0]['owneruid']);
				echo '{"state":1,"nick":"'.$emceeinfo['nickname'].'","url":"'.$_GET['num'].'"}';
				exit;
			}
			else{
				echo '{"state":0,"price":'.$goodnums[0]['price'].'}';
				exit;
			}
		}
		else{
			echo '{"state":2}';
			exit;
		}
	}

	public function emceeno_refreshEmceeNoList(){
		C('HTML_CACHE_ON',false);

		$goodnum_users = D('Goodnum')->where('issale="y"')->order('rand()')->group('owneruid')->limit(10)->select();
		echo '[';
		if($goodnum_users){
			$i = 1;
			foreach($goodnum_users as $val){
				echo '{"logo":"'.$this->ucurl.'avatar.php?uid='.$val['ucuid'].'&size=middle","num":'.$val['num'].'}';
				if($i != count($goodnum_users)){echo ',';}
				$i++;
			}
		}
		echo ']';
	}

	public function emceeno_buyEmceeNo(){
		C('HTML_CACHE_ON',false);

		if(!$_SESSION['uid'] || $_SESSION['uid'] < 0){
			echo '{"state":2}';
			exit;
		}

		$goodnums = D('Goodnum')->where('num='.$_GET['emceeno'])->limit(1)->select();
		if($goodnums){
			if($goodnums[0]['issale'] == 'y'){
				echo '{"state":4}';
				exit;
			}
			else{
				$userinfo = D("Member")->find($_SESSION['uid']);

				if($userinfo['coinbalance'] < $goodnums[0]['price']){
					echo '{"state":1}';
					exit;
				}
				else{
					//扣费
					D("Member")->execute('update ss_member set spendcoin=spendcoin+'.$goodnums[0]['price'].',coinbalance=coinbalance-'.$goodnums[0]['price'].' where id='.$_SESSION['uid']);
					//记入虚拟币交易明细
					$Coindetail = D("Coindetail");
					$Coindetail->create();
					$Coindetail->type = 'expend';
					$Coindetail->action = 'buy';
					$Coindetail->uid = $_SESSION['uid'];
					$Coindetail->giftcount = 1;
					$Coindetail->content = '您购买了靓号'.$goodnums[0]['num'];
					$Coindetail->objectIcon = '/Public/images/gnum.png';
					$Coindetail->coin = $goodnums[0]['price'];
					$Coindetail->addtime = time();
					$detailId = $Coindetail->add();
					
					D("Roomnum")->execute('insert into ss_roomnum(uid,num,addtime,expiretime,original) values('.$_SESSION['uid'].','.$goodnums[0]['num'].','.time().',0,"n")');
					D('Goodnum')->execute('update ss_goodnum set issale="y",owneruid='.$_SESSION['uid'].' where id='.$goodnums[0]['id']);

					echo '{"state":0}';
					exit;
				}
			}
		}
		else{
			echo '{"state":5}';
			exit;
		}
	}
}