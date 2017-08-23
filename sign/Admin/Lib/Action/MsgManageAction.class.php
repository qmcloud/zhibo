<?php

class MsgManageAction extends CommonAction {

	public function index () {
		import('ORG.Util.Page');

		$count = M('attendance')->count();
		$page  = new Page($count,5);
		$limit = $page->firstRow.','.$page->listRows;
         $teams=I('get.team');
        // echo $teams;die;
		$wish  = M('attendance')->where(array('team'=>$teams))->order('id DESC')->limit($limit)->select();

		for($i=0;$i<count($wish);$i++){
        if($wish[$i]['team']=='早班'){

            $wish[$i]['isLate']= strtotime($wish[$i]['data'].' 9:00:00')<strtotime($wish[$i]['data'].' '.$wish[$i]['amtime'])?1:0;
            $wish[$i]['isEarly']= strtotime($wish[$i]['data'].' 18:00:00')>strtotime($wish[$i]['data'].' '.$wish[$i]['amtime'])?1:0;

        }elseif($wish[$i]['team']=='中班'){

             $wish[$i]['isLate']= strtotime($wish[$i]['data'].' 13:00:00')<strtotime($wish[$i]['data'].' '.$wish[$i]['amtime'])?1:0;
            $wish[$i]['isEarly']= strtotime($wish[$i]['data'].' 22:00:00')>strtotime($wish[$i]['data'].' '.$wish[$i]['amtime'])?1:0;           
        }else{
             $wish[$i]['isLate']= strtotime($wish[$i]['data'].' 18:00:00')<strtotime($wish[$i]['data'].' '.$wish[$i]['amtime'])?1:0;
            $wish[$i]['isEarly']= strtotime($wish[$i]['data'].' 1:00:00')>strtotime($wish[$i]['data'].' '.$wish[$i]['amtime'])?1:0;           
        }
		}
		$this->wish =$wish;

		$this->page = $page->show();
		$this->assign('wish',$wish);
		$this->display();
	}

	// public function student () {
         

	// } 中班

	public function delete () {
        
      $id=I('get.id');
        $data['state']=0;
		if(M('attendance')->where(array('id'=> $id))->save($data)){
			$this->success('操作成功');
		}
	}
}
?>