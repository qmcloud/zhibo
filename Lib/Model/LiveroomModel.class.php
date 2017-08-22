<?php
class LiveroomModel extends Model
{
	//自动字段填充
	protected $_auto = array(
		array('starttime','time',1,'function'),
	);
}
?>