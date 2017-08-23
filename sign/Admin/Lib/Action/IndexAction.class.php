<?php
// 本类由系统自动生成，仅供测试用途
class IndexAction extends Action {
	public function index () {
		// import('ORG.Util.Page');

		// $count = M('attendance')->count();
		// $page  = new Page($count,2);
		// $limit = $page->firstRow.','.$page->listRows;

		// $attendance  = M('attendance')->order('id DESC')->limit($limit)->find();
		// for($i=0;i<=count($attendance);$i++){
		// 	$nowtime=$attendance['$']['data'].' 9:00:00';
		// 	$attendance[$i]['isLate']=($attendance['amtime']>strtotime($nowtime))?1:0;
		// }
		// $this->wish =$attendance;
		// $this->page = $page->show();
		$this->display();
	}

	public function delete () {

		$id = I('id','','intval');

		if(M('wish')->delete($id)){
			$this->success('删除成功',U('Admin/MsgManage/index'));
		}else{ $this->error('删除失败');}
	}
}
?>