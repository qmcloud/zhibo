<?php if(!defined('UC_ROOT')) exit('Access Denied');?>
<?php include $this->gettpl('plugin_header');?>
<?php if($modifiedfiles > 0) { ?>
	<a href="###" onclick="showresult('modify')"><em class="edited">被修改: <?php echo $modifiedfiles;?></em></a>&nbsp;&nbsp;
<?php } ?>
<?php if($deletedfiles > 0) { ?>
	<a href="###" onclick="showresult('del')"><em class="edited">被删除: <?php echo $deletedfiles;?></em></a>&nbsp;&nbsp;
<?php } ?>
<?php if($unknownfiles > 0) { ?>
	<a href="###" onclick="showresult('add')"><em class="edited">未知: <?php echo $unknownfiles;?></em></a>&nbsp;&nbsp;
<?php } ?>
<?php if($doubt > 0) { ?>
	<a href="###" onclick="showresult('doubt')"><em class="edited">一周内被修改: <?php echo $doubt;?></em></a>&nbsp;&nbsp;
<?php } ?>

<?php echo $result;?>

<?php include $this->gettpl('plugin_footer');?>