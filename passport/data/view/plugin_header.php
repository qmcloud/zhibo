<?php if(!defined('UC_ROOT')) exit('Access Denied');?>
<?php include $this->gettpl('header');?>
<style>

.tabhead {width: 100%; clear: both; background: url(images/bg_tab_line.gif) repeat-x bottom;}
.tabhead li{line-height: 1.2em; display:block; padding:5px 7px 2px 7px; border:1px solid #CCC; border-bottom:0px solid #B5CFD9; color:#666;  float:left; margin-right:5px;}
.tabhead li.checked{background:#F2F9FD; font-weight: 800}
.tabbody {padding: 1em; clear: both; border:1px solid #B5CFD9; border-top: 0px; background:#F2F9FD; }

</style>
<script src="js/common.js" type="text/javascript"></script>
<div class="container">
<?php if($plugin['tips']) { ?>
	<div class="note fixwidthdec"><p class="i"><?php echo $plugin['tips'];?></p></div>
<?php } ?>
	<ul class="tabhead">
		<?php foreach((array)$plugins as $v) {?>
			<li id="nav_action_<?php echo $v['dir'];?>"><a href="admin.php?m=plugin&a=<?php echo $v['dir'];?>"><?php echo $v['name'];?></a></li>
		<?php } ?>
		<script type="text/javascript">document.getElementById('nav_action_<?php echo $_GET['a'];?>').className = 'checked';</script>
	</ul>
	<div class="tabbody">