<?php
class GiveawayModel extends Model
{
	//自动字段填充
	protected $_auto = array(
		array('addtime','time',1,'function'),
	);
}
?>