<?php if(!defined('UC_ROOT')) exit('Access Denied');?>
<?php include $this->gettpl('header');?>

<?php if($a == 'ls') { ?>

	<script src="js/common.js" type="text/javascript"></script>
	<script src="js/calendar.js" type="text/javascript"></script>
	<script type="text/javascript">
		function switchbtn(btn) {
			$('addadmindiv').className = btn == 'addadmin' ? 'tabcontentcur' : '' ;
			$('editpwdiv').className = btn == 'addadmin' ? '' : 'tabcontentcur';

			$('addadmin').className = btn == 'addadmin' ? 'tabcurrent' : '';
			$('editpw').className = btn == 'addadmin' ? '' : 'tabcurrent';

			$('addadmindiv').style.display = btn == 'addadmin' ? '' : 'none';
			$('editpwdiv').style.display = btn == 'addadmin' ? 'none' : '';
		}
		function chkeditpw(theform) {
			if(theform.oldpw.value == '') {
				alert('请输入原创始人密码');
				theform.oldpw.focus();
				return false;
			}
			if(theform.newpw.value == '') {
				alert('请输入新密码');
				theform.newpw.focus();
				return false;
			}
			if(theform.newpw2.value == '') {
				alert('请重复输入新密码');
				theform.newpw2.focus();
				return false;
			}
			if(theform.newpw.value != theform.newpw2.value) {
				alert('两次输入的密码不一致');
				theform.newpw2.focus();
				return false;
			}
			if(theform.newpw.value.length < 6 && !confirm('您的密码太短，可能会不安全，您确定设定此密码吗？')) {
				theform.newpw.focus();
				return false;
			}
			return true;
		}
	</script>

	<div class="container">
		<?php if($status) { ?>
			<div class="<?php if($status > 0) { ?>correctmsg<?php } else { ?>errormsg<?php } ?>">
				<p>
				<?php if($status == 1) { ?> 添加 <?php echo $addname;?> 为管理员成功
				<?php } elseif($status == -1) { ?> 添加 <?php echo $addname;?> 为管理员成功
				<?php } elseif($status == -2) { ?> 添加 <?php echo $addname;?> 为管理员失败
				<?php } elseif($status == -3) { ?>无此用户: <?php echo $addname;?>
				<?php } elseif($status == -4) { ?> /data/config.inc.php 文件不可写
				<?php } elseif($status == -5) { ?> 创始人账号密码输入错误
				<?php } elseif($status == -6) { ?> 两次输入的密码不一致
				<?php } elseif($status == 2) { ?> 创始人账号密码修改成功
				<?php } ?>
				</p>
			</div>
		<?php } ?>
		<div class="hastabmenu" style="height:175px;">
			<ul class="tabmenu">
				<li id="addadmin" class="tabcurrent"><a href="#" onclick="switchbtn('addadmin');">添加管理员</a></li>
				<?php if($user['isfounder']) { ?><li id="editpw"><a href="#" onclick="switchbtn('editpw');">修改创始人密码</a></li><?php } ?>
			</ul>
			<div id="addadmindiv" class="tabcontentcur">
				<form action="admin.php?m=admin&a=ls" method="post">
				<input type="hidden" name="formhash" value="<?php echo FORMHASH;?>">
				<table class="dbtb">
					<tr>
						<td class="tbtitle">用户名:</td>
						<td><input type="text" name="addname" class="txt" /></td>
					</tr>
					<tr>
						<td valign="top" class="tbtitle">权　限:</td>
						<td>
							<ul class="dblist">
								<li><input type="checkbox" name="allowadminsetting" value="1" class="checkbox" checked="checked" />允许改变设置</li>
								<li><input type="checkbox" name="allowadminapp" value="1" class="checkbox" />允许管理应用</li>
								<li><input type="checkbox" name="allowadminuser" value="1" class="checkbox" />允许管理用户</li>
								<li><input type="checkbox" name="allowadminbadword" value="1" class="checkbox" checked="checked" />允许管理词语过滤</li>
								<li><input type="checkbox" name="allowadmintag" value="1" class="checkbox" checked="checked" />允许管理TAG</li>
								<li><input type="checkbox" name="allowadminpm" value="1" class="checkbox" checked="checked" />允许管理短消息</li>
								<li><input type="checkbox" name="allowadmincredits" value="1" class="checkbox" checked="checked" />允许管理积分</li>
								<li><input type="checkbox" name="allowadmindomain" value="1" class="checkbox" checked="checked" />允许管理域名解析</li>
								<li><input type="checkbox" name="allowadmindb" value="1" class="checkbox" />允许管理数据</li>
								<li><input type="checkbox" name="allowadminnote" value="1" class="checkbox" checked="checked" />允许管理数据列表</li>
								<li><input type="checkbox" name="allowadmincache" value="1" class="checkbox" checked="checked" />允许管理缓存</li>
								<li><input type="checkbox" name="allowadminlog" value="1" class="checkbox" checked="checked" />允许查看日志</li>
							</ul>
						</td>
					</tr>
					<tr>
						<td></td>
						<td>
							<input type="submit" name="addadmin" value="提 交" class="btn" />
						</td>
					</tr>
				</table>
				</form>
			</div>
			<?php if($user['isfounder']) { ?>
			<div id="editpwdiv" class="tabcontent" style="display:none;">
				<form action="admin.php?m=admin&a=ls" onsubmit="return chkeditpw(this)" method="post">
				<input type="hidden" name="formhash" value="<?php echo FORMHASH;?>">
				<table class="dbtb" style="height:123px;">
					<tr>
						<td class="tbtitle">旧密码:</td>
						<td><input type="password" name="oldpw" class="txt" /></td>
					</tr>
					<tr>
						<td class="tbtitle">新密码:</td>
						<td><input type="password" name="newpw" class="txt" /></td>
					</tr>
					<tr>
						<td class="tbtitle">重复新密码:</td>
						<td><input type="password" name="newpw2" class="txt" /></td>
					</tr>
					<tr>
						<td></td>
						<td>
							<input type="submit" name="editpwsubmit" value="提 交" class="btn" />
						</td>
					</tr>
				</table>
				</form>
			</div>
			<?php } ?>
		</div>
		<h3>管理员列表</h3>
		<div class="mainbox">
			<?php if($userlist) { ?>
				<form action="admin.php?m=admin&a=ls" onsubmit="return confirm('您确定删除吗？');" method="post">
				<input type="hidden" name="formhash" value="<?php echo FORMHASH;?>">
				<table class="datalist fixwidth" onmouseover="addMouseEvent(this);">
					<tr>
						<th><input type="checkbox" name="chkall" id="chkall" onclick="checkall('delete[]')" value="1" class="checkbox" /><label for="chkall">删除</label></th>
						<th>用户名</th>
						<th>Email</th>
						<th>注册日期</th>
						<th>注册IP</th>
						<th>资料</th>
						<th>权限</th>
					</tr>
					<?php foreach((array)$userlist as $user) {?>
						<tr>
							<td class="option"><input type="checkbox" name="delete[]" value="<?php echo $user['uid'];?>" value="1" class="checkbox" /></td>
							<td class="username"><?php echo $user['username'];?></td>
							<td><?php echo $user['email'];?></td>
							<td class="date"><?php echo $user['regdate'];?></td>
							<td class="ip"><?php echo $user['regip'];?></td>
							<td class="ip"><a href="admin.php?m=user&a=edit&uid=<?php echo $user['uid'];?>&fromadmin=yes">资料</a></td>
							<td class="ip"><a href="admin.php?m=admin&a=edit&uid=<?php echo $user['uid'];?>">权限</a></td>
						</tr>
					<?php } ?>
					<tr class="nobg">
						<td><input type="submit" value="提 交" class="btn" /></td>
						<td class="tdpage" colspan="4"><?php echo $multipage;?></td>
					</tr>
				</table>
				</form>
			<?php } else { ?>
				<div class="note">
					<p class="i">目前没有相关记录!</p>
				</div>
			<?php } ?>
		</div>
	</div>
	<?php if($_POST['editpwsubmit']) { ?>
		<script type="text/javascript">
		switchbtn('editpw');
		</script>
	<?php } else { ?>
		<script type="text/javascript">
		switchbtn('addadmin');
		</script>
	<?php } ?>

<?php } else { ?>
	<div class="container">
		<h3 class="marginbot">编辑管理员权限<a href="admin.php?m=admin&a=ls" class="sgbtn">返回管理员列表</a></h3>
		<?php if($status == 1) { ?>
			<div class="correctmsg"><p>编辑管理员权限成功</p></div>
		<?php } elseif($status == -1) { ?>
			<div class="correctmsg"><p>编辑管理员权限失败</p></div>
		<?php } else { ?>
			<div class="note">请谨慎开放“管理应用”，“管理用户”、“管理数据”权限</div>
		<?php } ?>
		<div class="mainbox">
			<form action="admin.php?m=admin&a=edit&uid=<?php echo $uid;?>" method="post">
			<input type="hidden" name="formhash" value="<?php echo FORMHASH;?>">
				<table class="opt">
					<tr>
						<th>管理员 <?php echo $admin['username'];?>:</th>
					</tr>
					<tr>
						<td>
							<ul>
								<li><input type="checkbox" name="allowadminsetting" value="1" class="checkbox" <?php if($admin['allowadminsetting']) { ?> checked="checked" <?php } ?>/>允许改变设置</li>
								<li><input type="checkbox" name="allowadminapp" value="1" class="checkbox" <?php if($admin['allowadminapp']) { ?> checked="checked" <?php } ?>/>允许管理应用</li>
								<li><input type="checkbox" name="allowadminuser" value="1" class="checkbox" <?php if($admin['allowadminuser']) { ?> checked="checked" <?php } ?>/>允许管理用户</li>
								<li><input type="checkbox" name="allowadminbadword" value="1" class="checkbox" <?php if($admin['allowadminbadword']) { ?> checked="checked" <?php } ?>/>允许管理词语过滤</li>
								<li><input type="checkbox" name="allowadmintag" value="1" class="checkbox" <?php if($admin['allowadmintag']) { ?> checked="checked" <?php } ?>/>允许管理TAG</li>
								<li><input type="checkbox" name="allowadminpm" value="1" class="checkbox" <?php if($admin['allowadminpm']) { ?> checked="checked" <?php } ?>/>允许管理短消息</li>
								<li><input type="checkbox" name="allowadmincredits" value="1" class="checkbox" <?php if($admin['allowadmincredits']) { ?> checked="checked" <?php } ?>/>允许管理积分</li>
								<li><input type="checkbox" name="allowadmindomain" value="1" class="checkbox" <?php if($admin['allowadmindomain']) { ?> checked="checked" <?php } ?>/>允许管理域名解析</li>
								<li><input type="checkbox" name="allowadmindb" value="1" class="checkbox" <?php if($admin['allowadmindb']) { ?> checked="checked" <?php } ?>/>允许管理数据</li>
								<li><input type="checkbox" name="allowadminnote" value="1" class="checkbox" <?php if($admin['allowadminnote']) { ?> checked="checked" <?php } ?>/>允许管理数据列表</li>
								<li><input type="checkbox" name="allowadmincache" value="1" class="checkbox" <?php if($admin['allowadmincache']) { ?> checked="checked" <?php } ?>/>允许管理缓存</li>
								<li><input type="checkbox" name="allowadminlog" value="1" class="checkbox" <?php if($admin['allowadminlog']) { ?> checked="checked" <?php } ?>/>允许查看日志</li>
							</ul>
						</td>
					</tr>
				</table>
				<div class="opt"><input type="submit" name="submit" value=" 提 交 " class="btn" tabindex="3" /></div>
			</form>
		</div>
	</div>

<?php } ?>

<?php include $this->gettpl('footer');?>