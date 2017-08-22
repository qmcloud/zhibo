<?php
class AdminModel extends Model
{
	// 自动验证设置
	protected $_validate	 =	 array(
		array('adminname','','用户名已经存在',0,'unique',1),
	);
	//自动字段填充
	protected $_auto = array(
		array('addtime','time',1,'function'),
	);
}
?>