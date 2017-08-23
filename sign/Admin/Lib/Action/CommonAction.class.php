<?php

class CommonAction extends Action {

	 public function _initialize () {
	 	if($_SESSION['username'] != 'admin'){
	 		$this->redirect('Login/index');
	 	}
	 }
}

?>