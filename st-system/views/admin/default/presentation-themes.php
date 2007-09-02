<?php  if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<?php theme_include('header'); ?>

<?php out('message'); ?>

<!--starting page content-->
<div class="wrap">

<h2>Current Theme</h2>
<div id="currenttheme">
	<img src="<?php out('theme_url', 'screenshot.png'); ?>" alt="Current theme preview" />
	<?php $theme_data = get('theme_data', get('setting', 'use_theme')); ?>
	<h3><a href="<?php echo $theme_data['url'] ?>"><?php echo $theme_data['name'] ?> <?php echo $theme_data['version'] ?></a> by <a href="<?php echo $theme_data['author_url'] ?>" title="Visit author homepage"><?php echo $theme_data['author'] ?></a></h3>
	
	<p><?php echo $theme_data['description'] ?></p>
	<p>All of this theme&#8217;s files are located in <code>st-external/themes/<?php out('setting', 'use_theme') ?></code>.</p>
</div>

<h2>Available Themes</h2>
<?php 
	$avaliable_themes = get('avaliable_themes'); 
	if(!empty($avaliable_themes)):
	foreach($avaliable_themes as $each_theme): //themes.php?action=activate&amp;template=classic&amp;stylesheet=classic&#038;_wpnonce=23364fbf1f
		$each_theme_data = get('theme_data', $each_theme);
?>
<div class="available-theme">
<h3><a href="<?php out('admin_url', 'presentation/themes/activate/'.$each_theme); ?>"><?php echo $each_theme_data['name'] ?> <?php echo $each_theme_data['version'] ?></a></h3>

<a href="<?php out('admin_url', 'presentation/themes/activate/'.$each_theme); ?>" class="screenshot">
<img src="<?php out('theme_url', 'screenshot.png', $each_theme); ?>" alt="<?php echo $each_theme_data['name'] ?> Theme" />
</a>

<p><?php echo $theme_data['description'] ?></p>
</div>
<?php 
	endforeach;
	else:
?>
<p>There are currently no avaliable themes.</p>
<br />
<?php endif; ?>


<h2>Get More Themes</h2>
<p>You can find additional themes for your site in the <a href="http://www.suppletext.org/Themes">suppleText theme directory</a>. To install a theme you generally just need to upload the theme folder into your <code>st-external/themes</code> directory. Once a theme is uploaded, you should see it on this page.</p>

</div>
<!--closing page content-->

<?php theme_include('footer'); ?>
