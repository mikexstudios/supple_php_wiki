<?php  if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<?php theme_include('header'); ?>

<!--starting page content-->
<div class="wrap">

<?php if(!empty($this->validation->error_string)): ?>
		<?php echo $this->validation->error_string; ?>
<?php endif; ?>

<?php out('message'); ?>

<h2>Plugin Management</h2>
<p>Plugins extend and expand the functionality of suppleText. Once a plugin is installed, you may activate it or deactivate it here.</p>
<table class="widefat plugins">
	<thead>
	<tr>
		<th>Plugin</th>
		<th style="text-align: center">Version</th>

		<th>Description</th>
		<th style="text-align: center" colspan="2">Action</th>
	</tr>
	</thead>
<tr>
	<td colspan="3">&nbsp;</td>
	<td colspan="2" style="width:12em;"><a href="" class="delete">Deactivate All Plugins</a></td>
</tr>

</table>

<p>If something goes wrong with a plugin and you can&#8217;t use suppleText, delete or rename that file in the <code>st-external/plugins</code> directory and it will be automatically deactivated.</p>

<br />

<h2>Get More Plugins</h2>
<p>You can find additional plugins for your site in the <a href="http://www.suppletext.org/Plugins/">suppleText plugins directory</a>.</p>
<p>To install a plugin you generally just need to upload the plugin file into your <code>st-external/plugins</code> directory. Once a plugin is uploaded, you may activate it here.</p>

</div>
<!--closing page content-->

<?php theme_include('footer'); ?>
