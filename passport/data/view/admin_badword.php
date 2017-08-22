<?php if(!defined('UC_ROOT')) exit('Access Denied');?>
<?php include $this->gettpl('header');?>

<script src="js/common.js" type="text/javascript"></script>
<script type="text/javascript">
	function switchbtn(btn) {
		$('srchuserdiv').style.display = btn == 'srch' ? '' : 'none';
		$('srchuserdiv').className = btn == 'srch' ? 'tabcontentcur' : '' ;
		$('srchuserbtn').className = btn == 'srch' ? 'tabcurrent' : '';
		$('adduserdiv').style.display = btn == 'srch' ? 'none' : '';
		$('adduserdiv').className = btn == 'srch' ? '' : 'tabcontentcur';
		$('adduserbtn').className = btn == 'srch' ? '' : 'tabcurrent';
		$('tmenu').style.height = btn == 'srch' ? '80'+'px' : '280'+'px';
	}
</script>
<div class="container">
	<?php if($status) { ?>
		<div class="correctmsg"><p><?php if($status == 2) { ?>词语过滤成功更新。<?php } elseif($status == 1) { ?>词语过滤添加成功。<?php } ?></p></div>
	<?php } ?>
	<div id="tmenu" class="hastabmenu">
		<ul class="tabmenu">
			<li id="srchuserbtn" class="tabcurrent"><a href="#" onclick="switchbtn('srch');">添加词语过滤</a></li>
			<li id="adduserbtn"><a href="#" onclick="switchbtn('add');">批量添加</a></li>
		</ul>
		<div id="adduserdiv" class="tabcontent" style="display:none;">
			<form action="admin.php?m=badword&a=ls" method="post">
				<ul class="tiplist">
					<li>每行一组，不良词语和替换词语之间使用“=”进行分割。</li>
					<li>如果想将某个词语直接替换成 **，只输入词语即可。</li>
					<li><strong>例如：</strong></li>
					<li>toobad</li>
					<li>badword=good</li>
				</ul>
				<textarea name="badwords" class="bigarea"></textarea>
				<ul class="optlist">
					<li><input type="radio" name="type" value="2" id="badwordsopt2" class="radio" checked="checked" /><label for="badwordsopt2">当冲突时，跳过原来的词表</label></li>
					<li><input type="radio" name="type" value="1" id="badwordsopt1" class="radio" /><label for="badwordsopt1">当冲突时，覆盖原来的词表</label></li>
					<li><input type="radio" name="type" value="0" id="badwordsopt0" class="radio" /><label for="badwordsopt0">清空当前词表，后导入新词语（此操作不可恢复，建议首先<a href="admin.php?m=badword&a=export" target="_blanks">导出词表</a>，做好备份）</label></li>
				</ul>
				<input type="submit" name="multisubmit" value="提 交" class="btn" />
			</form>

		</div>
		<div id="srchuserdiv" class="tabcontentcur">
			<form action="admin.php?m=badword&a=ls" method="post">
			<input type="hidden" name="formhash" value="<?php echo FORMHASH;?>">
			<table>
				<tr>
					<td>不良词语:</td>
					<td><input type="text" name="findnew" class="txt" /></td>
					<td>替换为:</td>
					<td><input type="text" name="replacementnew" class="txt" /></td>
					<td><input type="submit" value="提 交"  class="btn" /></td>
				</tr>
			</table>
			</form>
		</div>
	</div>
	<br />
	<h3>词语过滤</h3>
	<div class="mainbox">
		<?php if($badwordlist) { ?>
			<form action="admin.php?m=badword&a=ls" method="post">
				<table class="datalist fixwidth">
					<tr>
						<th><input type="checkbox" name="chkall" id="chkall" onclick="checkall('delete[]')" class="checkbox" /><label for="chkall">删除</label></th>
						<th style="text-align:right;padding-right:11px;">不良词语</th>
						<th></th>
						<th>替换为</th>
						<th>操作人</th>
					</tr>
					<?php foreach((array)$badwordlist as $badword) {?>
						<tr>
							<td class="option"><input type="checkbox" name="delete[]" value="<?php echo $badword['id'];?>" class="checkbox" /></td>
							<td class="tdinput"><input type="text" name="find[<?php echo $badword['id'];?>]" value="<?php echo $badword['find'];?>" title="点击编辑，提交后保存" class="txtnobd" onblur="this.className='txtnobd'" onfocus="this.className='txt'" /></td>
							<td class="tdarrow">&gt;</td>
							<td class="tdinput"><input type="text" name="replacement[<?php echo $badword['id'];?>]" value="<?php echo $badword['replacement'];?>" title="点击编辑，提交后保存" class="txtnobd"  onblur="this.className='txtnobd'" onfocus="this.className='txt'" style="text-align:left;" /></td>
							<td><?php echo $badword['admin'];?></td>
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

<?php include $this->gettpl('footer');?>