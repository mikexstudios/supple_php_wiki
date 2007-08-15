<?php  if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<?php admin_theme_include('header'); ?>

<?php if(!empty($this->validation->error_string)): ?>
		<?php echo $this->validation->error_string; ?>
<?php endif; ?>

<!--starting page content-->
<div class="wrap">

<h2>General Options</h2>
<form method="post" action="<?php out('admin_url', 'options/general'); ?>"> 
<p class="submit"><input type="submit" name="Submit" value="Update Options &raquo;" /></p>

<table class="optiontable"> 

<tr valign="top"> 
<th scope="row">Wiki title:</th> 
<td><input name="wikiname" type="text" id="wikiname" value="<?php if(!isset($this->validation->wikiname)) { out('setting', 'site_name'); } else { echo $this->validation->wikiname; } ?>" size="40" /></td> 
</tr> 

<tr valign="top"> 
<th scope="row">Your wiki address (URL):</th> 
<td>
	<p class="config_static_value"><?php echo $this->config->item('base_url'); ?></p><br />
	Note: To change your wiki address (URL), you must edit the <code>/st-external/st-config.php</code> file.   
</td>
</tr> 

<tr valign="top"> 
<th scope="row">Default wiki page:</th> 
<td>
	<input name="defaultwikipage" type="text" id="wikiname" value="<?php if($this->validation->defaultwikipage == '') { out('setting', 'root_page'); } else { echo $this->validation->defaultwikipage; } ?>" size="40" /><br />
	This is the page that is displayed when your root URL is visited (when no page is selected).
</td> 
</tr> 

</table> 

<p class="submit"><input type="submit" name="Submit" value="Update Options &raquo;" /></p>
</form>

</div>
<!--closing page content-->

<?php admin_theme_include('footer'); ?>
