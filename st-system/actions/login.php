<?php
global $Supple;

//If logging in.
if (isset($_POST['action']) && ($_POST['action'] == 'login'))
{
	//Form field variables
	$formUsername = 'name';
	$formPassword = 'password';

	//Get input from form
	$inUsername=$_POST[$formUsername];
	$inPassword=$_POST[$formPassword];

	//Set them in User Management
	$Supple->UserManagement->setUsername($inUsername);
	$Supple->UserManagement->setPassword($inPassword);	
	$Supple->UserManagement->getUid();

	//Process login information
	if ($Supple->UserManagement->processLogin('remember')) //If we have logged in successfully
	{
		//redirect_page('Login');
		echo 'Login successful!';
		return;
	}
	
	//Replace with something better.
	echo 'Login failed.';
}
else
{
	//Otherwise, show login page
	include get_theme_system_path('login.tpl.php');
}

?>