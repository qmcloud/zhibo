<?php
class UsersongModel extends Model
{
	//自动字段填充
	protected $_auto = array(
		array('createTime','time',1,'function'),
	);
}
?>