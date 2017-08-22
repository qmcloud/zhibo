<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><meta http-equiv="X-UA-Compatible" content="IE=7" /><title>后台管理中心</title><link href="__PUBLIC__/statics/css/reset.css" rel="stylesheet" type="text/css" /><link href="__PUBLIC__/statics/css/system.css" rel="stylesheet" type="text/css" /><link href="__PUBLIC__/statics/css/table_form.css" rel="stylesheet" type="text/css" /><link rel="stylesheet" type="text/css" href="__PUBLIC__/statics/css/style/styles1.css" title="styles1" media="screen" /><link rel="alternate stylesheet" type="text/css" href="__PUBLIC__/statics/css/style/styles2.css" title="styles2" media="screen" /><link rel="alternate stylesheet" type="text/css" href="__PUBLIC__/statics/css/style/styles3.css" title="styles3" media="screen" /><link rel="alternate stylesheet" type="text/css" href="__PUBLIC__/statics/css/style/styles4.css" title="styles4" media="screen" /><script language="javascript" type="text/javascript" src="__PUBLIC__/statics/js/jquery.min.js"></script><script language="javascript" type="text/javascript" src="__PUBLIC__/statics/js/admin_common.js"></script><script language="javascript" type="text/javascript" src="__PUBLIC__/statics/js/styleswitch.js"></script><script language="javascript" type="text/javascript" src="__PUBLIC__/statics/js/formvalidator.js" charset="UTF-8"></script><script language="javascript" type="text/javascript" src="__PUBLIC__/statics/js/formvalidatorregex.js" charset="UTF-8"></script><script type="text/javascript">	window.focus();
</script></head><body><div class="subnav"><div class="content-menu ib-a blue line-x"></div></div><style type="text/css">	html{_overflow-y:scroll}
</style><script type="text/javascript">  $(document).ready(function() {
	$.formValidator.initConfig({autotip:true,formid:"myform",onerror:function(msg){}});
	$("#old_password").formValidator({empty:true,onshow:"不修改密码请留空。",onfocus:"密码应该为6-20位之间",oncorrect:"旧密码输入正确"}).inputValidator({min:6,max:20,onerror:"密码应该为6-20位之间"}).ajaxValidator({
	    type : "get",
		url : "",
		data :"action=public_password_ajx",
		datatype : "html",
		async:'false',
		success : function(data){	
            if( data == "1" )
			{
                return true;
			}
            else
			{
                return false;
			}
		},
		buttons: $("#dosubmit"),
		onerror : "旧密码输入错误",
		onwait : "请稍候..."
	});
	$("#new_password").formValidator({empty:true,onshow:"不修改密码请留空。",onfocus:"密码应该为6-20位之间"}).inputValidator({min:6,max:20,onerror:"密码应该为6-20位之间"});
	$("#new_pwdconfirm").formValidator({empty:true,onshow:"不修改密码请留空。",onfocus:"请输入两次密码不同。",oncorrect:"密码输入一致"}).compareValidator({desid:"new_password",operateor:"=",onerror:"请输入两次密码不同。"});
  })
</script><div class="pad_10"><div class="common-form"><form name="myform" action="__URL__/do_edit_pwd/" method="post" id="myform"><input type="hidden" name="id" value="<?php echo ($_SESSION['agentid']); ?>"></input><table width="100%" class="table_form contentWrap"><tr><td width="80">用户名</td><td><?php echo ($_SESSION['username']); ?></td></tr><tr><td>旧密码</td><td><input type="password" name="old_password" id="old_password" class="input-text"></input></td></tr><tr><td>新密码</td><td><input type="password" name="new_password" id="new_password" class="input-text"></input></td></tr><tr><td>重复新密码</td><td><input type="password" name="new_pwdconfirm" id="new_pwdconfirm" class="input-text"></input></td></tr></table><div class="bk15"></div><input name="dosubmit" type="submit" value="提交" class="button" id="dosubmit"></form></div></div></body></html>