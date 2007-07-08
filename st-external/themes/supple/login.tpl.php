<form action="<?php out('page_url', 'Login'); ?>" method="post">
<input type="hidden" name="wiki" value="Login" />
<input type="hidden" name="action" value="login" />
	<fieldset id="register" class="usersettings"><legend>Login/Register</legend>
	<em class="usersettings_info">If you already have a login, sign in here:</em>

	<br />
	<label for="name">Your <abbr title="A WikiName is formed by two or more capitalized words without space, e.g. JohnDoe">WikiName</abbr>:</label>
	<input id="name" type="text"  name="name" size="40" value="" />
	<br />
	<label for="password">Password (5+ chars):</label>
	<input id="password"  type="password" name="password" size="40" />
	<br />
	<input id="loginsubmit" type="submit" value="Login" size="40" />
	</fieldset>
</form>