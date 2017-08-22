<?php if(!defined('UC_ROOT')) exit('Access Denied');?>
<?php include $this->gettpl('header');?>

<script src="js/common.js" type="text/javascript"></script>
<script src="js/calendar.js" type="text/javascript"></script>

<div class="container">
	<div class="hastabmenu" style="height: 200px;">
		<ul class="tabmenu">
			<li class="tabcurrent"><a href="javascript:;" class="tabcurrent">短消息搜索</a></li>
			<li><a href="admin.php?m=pm&a=clear">清空短消息</a></li>
		</ul>
		<div id="searchpmdiv" class="tabcontentcur">
			<form action="admin.php?m=pm&a=ls" method="post">
				<input type="hidden" name="formhash" value="<?php echo FORMHASH;?>">
				<table class="dbtb">
					<tr>
						<th class="tbtitle">发件人:</th>
						<td><input type="text" name="srchauthor" class="txt" value="<?php echo $srchauthor;?>" /></td>
					</tr>
					<tr>
						<th class="tbtitle">消息内容:</th>
						<td><input type="text" name="srchmessage" class="txt" value="<?php echo $srchmessage;?>" /></td>
					</tr>
					<tr>
						<th class="tbtitle">时间范围:</th>
						<td><input type="text" name="srchstarttime" class="txt" style="margin-right: 0;" value="<?php echo $srchstarttime;?>" onclick="showcalendar();" /> - <input type="text" name="srchendtime" class="txt" value="<?php echo $srchendtime;?>" onclick="showcalendar();" /></td>
					</tr>
					<tr>
						<th class="tbtitle">选择分表:</th>
						<td>
							<select name="srchtablename">
								<option value="0"<?php if($srchtablename === 0) { ?> selected="selected"<?php } ?>>pm_messages_0</option>
								<option value="1"<?php if($srchtablename == 1) { ?> selected="selected"<?php } ?>>pm_messages_1</option>
								<option value="2"<?php if($srchtablename == 2) { ?> selected="selected"<?php } ?>>pm_messages_2</option>
								<option value="3"<?php if($srchtablename == 3) { ?> selected="selected"<?php } ?>>pm_messages_3</option>
								<option value="4"<?php if($srchtablename == 4) { ?> selected="selected"<?php } ?>>pm_messages_4</option>
								<option value="5"<?php if($srchtablename == 5) { ?> selected="selected"<?php } ?>>pm_messages_5</option>
								<option value="6"<?php if($srchtablename == 6) { ?> selected="selected"<?php } ?>>pm_messages_6</option>
								<option value="7"<?php if($srchtablename == 7) { ?> selected="selected"<?php } ?>>pm_messages_7</option>
								<option value="8"<?php if($srchtablename == 8) { ?> selected="selected"<?php } ?>>pm_messages_8</option>
								<option value="9"<?php if($srchtablename == 9) { ?> selected="selected"<?php } ?>>pm_messages_9</option>
							</select>
							 短消息中心消息总数: <?php echo $pmnum;?>
						</td>
					</tr>
					<tr>
						<th></th>
						<td><input type="submit" value="提 交" class="btn" name="searchpmsubmit" /></td>
					</tr>
				</table>
			</form>
		</div>
	</div>

	<h3>短消息列表</h3>
	<div class="mainbox">
		<?php if($pmlist) { ?>
			<form action="admin.php?m=pm&a=delete" onsubmit="return confirm('该操作不可恢复，您确认要删除这些短消息吗？');" method="post">
			<input type="hidden" name="formhash" value="<?php echo FORMHASH;?>">
			<input type="hidden" name="srchtablename" value="<?php echo $srchtablename;?>">
			<input type="hidden" name="srchauthor" value="<?php echo $srchauthor;?>">
			<input type="hidden" name="srchstarttime" value="<?php echo $srchstarttime;?>">
			<input type="hidden" name="srchendtime" value="<?php echo $srchendtime;?>">
			<input type="hidden" name="srchmessage" value="<?php echo $srchmessage;?>">
			<table class="datalist fixwidth" onmouseover="addMouseEvent(this);">
				<tr>
					<th width="60"><input type="checkbox" name="chkall" id="chkall" onclick="checkall('deletepmid[]')" class="checkbox" /><label for="chkall">删除</label></th>
					<th width="150">发起者</th>
					<th width="120">时间</th>
					<th>消息内容</th>
				</tr>
				<?php foreach((array)$pmlist as $pm) {?>
					<tr>
						<td><input type="checkbox" name="deletepmid[]" value="<?php echo $pm['pmid'];?>" class="checkbox" /></td>
						<td><img src="avatar.php?uid=<?php echo $pm['authorid'];?>&size=small" align="absmiddle" width="20" /> <strong><?php echo $pm['author'];?></strong></td>
						<td><?php echo $pm['dateline'];?></td>
						<td><?php echo $pm['message'];?></td>
					</tr>
				<?php } ?>
				<tr class="nobg">
					<td><input type="submit" value="提 交" class="btn" /></td>
					<td class="tdpage" colspan="3"><?php echo $multipage;?></td>
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

<?php include $this->gettpl('footer');?>