<?php
class AnnAction extends BaseAction {
    public function view(){
		if($_REQUEST['_URL_'][2] == ''){
			$this->error('参数错误');
		}
		else{
			$anninfo = D("Announce")->getById($_REQUEST['_URL_'][2]);
			if($anninfo){
				$this->assign('anninfo',$anninfo);
			}
			else{
				$this->error('找不到该公告');
			}
		}
		
        $this->display();
    }
}