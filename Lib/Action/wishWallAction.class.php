<?php
class wishWallAction extends BaseAction {
    public function index(){
		$wishs = D('Wish')->where('date_format(FROM_UNIXTIME(wishtime),"%m-%d-%Y")=date_format(now(),"%m-%d-%Y")')->order('wishtime desc')->select();
		$this->assign('wishs', $wishs);

        $this->display();
    }
}