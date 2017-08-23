<?php

class LoginAction extends Action{

	public function index() {
		$this->display();
	}

	public function verify (){

		import('ORG.Util.Image');
		Image::buildImageVerify(4,1,'png');
	}

	public function login () {

		if(!IS_POST) halt('页面不存在');
		if(I('session.verify') != I('post.code','',md5)){
			$this->error('验证码错误');
		}
        $user = M('user')->where(array('username' =>I('post.username')))->find();

        if(!$user || $user['password'] != I('post.password','',md5)){
        	$this->error('账号或密码错误');
        }
        if($user['lock'])$this->error('账户被锁定');

        $data = array(
        	'id' => $user['id'],
        	'logintime' => time(),
        	'loginip' =>get_client_ip(),
        	);
        M('user')->save($data);

        session('uid',$user['id']);
        session('username',$user['username']);
        session('logintime',date('Y-m-d H:i:s',$user['logintime']));
        session('loginip',$user['loginip']);
        
        $this->redirect('Index/index');

	}
}
?>