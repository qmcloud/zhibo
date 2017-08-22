<?php
class findEmceesAction extends BaseAction {
    public function index(){
		$recusers = D('Member')->where('bigpic<>"" and recommend="y" and broadcasting="y" and isdelete="n"')->field('nickname,curroomnum,bigpic,online,virtualguest')->order('rectime desc')->limit(9)->select();
		$this->assign('recusers', $recusers);

        $this->display();
    }
}