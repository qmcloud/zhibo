<?php
class MemberModel extends Model
{
	// 自动验证设置
	protected $_validate	 =	 array(
		array('username','require','用户名不能为空！'),
		array('username','','用户名已经存在',0,'unique','add'),
		array('email','require','电子邮件不能为空！'),
		array('email','email','邮箱格式错误！',2),
		array('email','','电子邮件已经存在',0,'unique','add'),
		array('password','require','密码不能为空！'),
	);
	//自动字段填充
	protected $_auto = array(
		array('regtime','time',1,'function'),
		array('lastlogip','get_client_ip',1,'function'),
	);
}
?>