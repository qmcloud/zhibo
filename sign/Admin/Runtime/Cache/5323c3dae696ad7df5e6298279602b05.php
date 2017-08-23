<?php if (!defined('THINK_PATH')) exit();?><table border="1" width="100%" cellspacing="0" cellpadding="0">
	<tr bgcolor="#DBEAF9">
		<th width="10%">ID</th>
		<th width="10%">上班</th>
		<th width="10%">下班</th>
		<th width="10%">日期</th>
		<th width="10%">班</th>
		<th width="10%">申诉内容</th>
		<th>操作</th>
	</tr>
	<?php if(is_array($wish)): $i = 0; $__LIST__ = $wish;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$d): $mod = ($i % 2 );++$i;?><tr>
		    <td width="10%"><?php echo ($d["username"]); ?></td>
			<td width="10%"<?php if(($d["isLate"]) == "1"): ?>style="color:#FF0000"<?php endif; ?>><?php echo ($d['amtime']); ?> </td>
			<td width="10%"<?php if(($d["isLat1e"]) == "1"): ?>strle="color:#FF0000"<?php endif; ?>><?php echo ($d['pmtime']); ?> </td>
			<td width="10%"><?php echo ($d["data"]); ?></td>
			<td width="10%"><?php echo ($d["team"]); ?></td>
			<td width="10%"><?php echo ($d["complain"]); ?></td>
			<td width="10%"><?php if(!empty($d["complain"])): ?><a href="<?php echo U('MsgManage/delete?id='.$d['id']);?>">点击同意</a><?php endif; ?></td>
		</tr><?php endforeach; endif; else: echo "" ;endif; ?>
	<tr>
		<td colspan="7" align="center"><?php echo ($page); ?></td>
	</tr>
</table>