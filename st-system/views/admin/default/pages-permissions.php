<?php  if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<?php theme_include('header'); ?>

<!--starting page content-->
<div class="wrap">

<?php if(!empty($this->validation->error_string)): ?>
		<?php echo $this->validation->error_string; ?>
<?php endif; ?>

<?php out('message'); ?>

<h2>Page Permissions</h2>
<form method="post" action="<?php out('admin_url', 'pages/permissions'); ?>"> 

<table class="optiontable"> 

<tr valign="top"> 
<th scope="row">Page name:</th> 
<td>
	<input name="page_name" type="text" id="page_name" value="<?php echo $this->validation->page_name; ?>" size="40" /><br />
	Enter the page name (ie. <em>SandBox</em>) for which you want to edit permissions.
	You can also change the page permissions by clicking the change permissions link
	when editing the page.	
</td> 
</tr> 

</table> 

<p class="submit"><input type="submit" name="Submit" value="Change Permissions &raquo;" /></p>
</form>

</div>
<!--closing page content-->

<?php theme_include('footer'); ?>
