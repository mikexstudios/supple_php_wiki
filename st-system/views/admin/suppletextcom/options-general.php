<?php  if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<?php theme_include('header'); ?>

<!--starting page content-->
<div class="wrap">

<?php if(!empty($this->validation->error_string)): ?>
		<?php echo $this->validation->error_string; ?>
<?php endif; ?>

<?php out('message'); ?>

<h2>General Options</h2>
<form method="post" action="<?php out('admin_url', 'options/general'); ?>"> 
<p class="submit"><input type="submit" name="Submit" value="Update Options &raquo;" /></p>

<table class="optiontable"> 

<tr valign="top"> 
<th scope="row">Wiki title:</th> 
<td><input name="site_name" type="text" id="site_name" value="<?php if(empty($this->validation->site_name)) { out('setting', 'site_name'); } else { echo $this->validation->site_name; } ?>" size="40" /></td> 
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
	<input name="root_page" type="text" id="root_page" value="<?php if(empty($this->validation->root_page)) { out('setting', 'root_page'); } else { echo $this->validation->root_page; } ?>" size="40" /><br />
	This is the page that is displayed when your root URL is visited (when no page is selected).
</td> 
</tr>

<tr valign="top"> 
<th scope="row">Default read role(s):</th> 
<td>
	<input name="default_read_permission" type="text" id="default_read_permission" value="<?php if(empty($this->validation->default_read_permission)) { out('setting', 'default_read_permission'); } else { echo $this->validation->default_read_permission; } ?>" size="40" /><br />
	Enter roles separated by a comma allowed to view pages (ie. Anonymous, Registered, Editor). <em>These roles can be overridden on specific pages</em>.
</td> 
</tr>

<tr valign="top"> 
<th scope="row">Default write role(s):</th> 
<td>
	<input name="default_write_permission" type="text" id="default_write_permission" value="<?php if(empty($this->validation->default_write_permission)) { out('setting', 'default_write_permission'); } else { echo $this->validation->default_write_permission; } ?>" size="40" /><br />
	Enter roles separated by a comma allowed to edit pages (ie. Registered, Editor). <em>These roles can be overridden on specific pages</em>.
</td> 
</tr>

</table> 

<p class="submit"><input type="submit" name="Submit" value="Update Options &raquo;" /></p>
</form>

</div>
<!--closing page content-->

<?php theme_include('footer'); ?>
