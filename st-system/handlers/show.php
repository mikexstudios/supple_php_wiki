<?php
global $Supple; //Need this to access $Supple

//Include the Show class
include_once ABSPATH.'/st-system/includes/Show.class.php';
if(strcmp($Supple->Settings->getSetting('use_cache'), 'true')==0)
{
	//Include the Cache class
	include_once ABSPATH.'/st-system/includes/Cache.class.php';
	$Show = new Cache();
}
else //Use the regular Show class
{
	$Show = new Show();
}
$Show->registerActions();

$Show->setPagename($Supple->getPagename());
//Set the time if specified in GET
if(!empty($_GET['time']))
{
	$Show->setTime($Supple->Input->get('time', true));
}
$Show->loadPage();

if(strcmp($Supple->Settings->getSetting('use_cache'), 'true')==0)
{
	//Check if cached version exists.
	if($Show->haveCachedVersion())
	{
		//No Syntax Parsing
		//echo 'Using Cached Version';
	}	
	else
	{
		
		//Syntax formatting. 
		$Show->page['body'] = format_text($Show->page['body']);
		$Show->storeCached();
	}
	
}
else
{
	//Syntax formatting. 
	$Show->page['body'] = format_text($Show->page['body']);
}

//Do anything that needs to be finalized before displaying the page.
include_once ABSPATH.'/st-system/includes/finalize.php';

//Load template:
include get_theme_system_path('show.tpl.php');

?>
