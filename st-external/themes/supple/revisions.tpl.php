<?php theme_include('header.tpl.php'); ?>

<!--starting page content-->
<div id="page">

<form action="<?php out('page_url', get('page_tag'), 'diff'); ?>" method="get">
	<input type="hidden" name="wiki" value="<?php out('page_tag'); ?>/diff" />
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
		<td><a href="<?php out('page_url', get('page_tag'), 'show', 'time='.urlencode($each_revision['time'])); ?>"><?php echo  htmlspecialchars_ent($each_revision['time']); ?></a> by <span class="user"><?php echo  htmlspecialchars_ent($each_revision['user']); ?></span> <span class="pagenote smaller"><?php htmlspecialchars_ent($each_revision['note']); ?></span></td>
		</tr>
		<?php endforeach; ?>
	</table>
	</fieldset>
</form>
	
</div>
<!--closing page content-->

<?php theme_include('footer.tpl.php'); ?>