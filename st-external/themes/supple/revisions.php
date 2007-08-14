<?php  if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>
<?php theme_include('header'); ?>

<!--starting page content-->
<div id="page">

<form action="<?php out('page_url', get('page_tag'), 'diff'); ?>" method="get">
	<fieldset>
	<legend>Revisions for <a href="<?php out('page_url', get('page_tag')); ?>"><?php out('page_tag'); ?></a></legend>		
	<table border="0" cellspacing="0" cellpadding="1">
		<tr>
		<td><input type="submit" value="Show Differences" /></td>
		<!-- <td><input value="1" type="checkbox" checked="checked" name="fastdiff" id="fastdiff" /><label for="fastdiff">Simple Diff</label></td> -->
		</tr>
	</table>
	<table border="0" cellspacing="0" cellpadding="1">
		<?php
		$revision_list = get('revision_list');
		$c = 0;
		foreach ($revision_list as $each_revision):
			$c++;
		?>
		<tr>
		<td><input type="radio" name="a" value="<?php echo $each_revision['id']; ?>" <?php echo ($c == 1 ? 'checked="checked"' : ''); ?> /></td>
		<td><input type="radio" name="b" value="<?php echo $each_revision['id']; ?>" <?php echo ($c == 2 ? 'checked="checked"' : ''); ?> /></td>
		<td><a href="<?php out('page_url', get('page_tag'), 'show', $each_revision['id']); ?>"><?php echo unix_to_human($each_revision['time']); ?></a> by <span class="user"><?php echo $each_revision['user']; ?></span> <span class="pagenote smaller">[<?php echo $each_revision['note']; ?>]</span></td>
		</tr>
		<?php endforeach; ?>
	</table>
	</fieldset>
</form>
	
</div>
<!--closing page content-->

<?php theme_include('footer'); ?>
