<?php
// 本类由系统自动生成，仅供测试用途
class IndexAction extends CommonAction {
    public function index(){

	$this->display();
    }

    public function sign () {
    	
$dates=date('Y-m-d',time());

        $names=I('session.username');

        if(I('post.name1')=='AM'){

            $user=M('attendance')->where(array('data' => $dates,'username'=>$names))->select();
            if($user)$this->error('打卡失败');
                $data=array(
                    'data' => $dates,
                    'team' => I('post.name2'),
                    'username' => I('session.username'),
                    'amtime' => date('H:i:s',time()),
                    'loginip' => get_client_ip(),
                    );
                M('attendance')->add($data);
        }else{

            $pmuser=M('attendance')->where(array('data' => $dates,'username'=>$names))->find();
            if(!$pmuser)$this->error('下班打卡失败');
            if(!$pmuser['pmtime']){
            $pmtime['pmtime']=date('H:i:s',time());
            // dump($pmuser);die;
            M('attendance')->where(array('username'=>I('session.username'),'id'=>$pmuser['id']))->save($pmtime);
        }
        }
        $this->redirect("Index/show");
    }

    public function show () {
        $userName = I('session.username');
        $re = M('attendance')->order('id DESC')->where(array('username' =>$userName))->limit(7)->select();

        for ($i=0; $i <count($re) ; $i++) { 
        if($re[$i]['team']=='早班'){
            if($re[$i]['state']){
            $re[$i]['isLate']= strtotime($re[$i]['data'].' 9:00:00')<strtotime($re[$i]['data'].' '.$re[$i]['amtime'])?1:0;
            $re[$i]['isEarly']= strtotime($re[$i]['data'].' 18:00:00')>strtotime($re[$i]['data'].' '.$re[$i]['pmtime'])?1:0;
        }
        }elseif($re[$i]['team']=='中班'){
             $re[$i]['isLate']= strtotime($re[$i]['data'].' 13:00:00')<strtotime($re[$i]['data'].' '.$re[$i]['amtime'])?1:0;
            $re[$i]['isEarly']= strtotime($re[$i]['data'].' 22:00:00')>strtotime($re[$i]['data'].' '.$re[$i]['pmtime'])?1:0;           
        }else{
             $re[$i]['isLate']= strtotime($re[$i]['data'].' 18:00:00')<strtotime($re[$i]['data'].' '.$re[$i]['amtime'])?1:0;
            $re[$i]['isEarly']= strtotime($re[$i]['data'].' 1:00:00')>strtotime($re[$i]['data'].' '.$re[$i]['pmtime'])?1:0;           
        }

    }
    // var_dump($re);
    // die;
        $this->assign('re',$re);
        $this->display();
    }

    public function complain () {
     //echo I('get.id');die;
    $data=array('complain'=>I('post.appeal'));      
    if(M('attendance')->where('id='.I('get.id'))->save($data)){
        $this->success('申诉成功');
    }
    }
}