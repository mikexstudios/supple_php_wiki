<?php

global $Supple, $Show;
global $args; //Need this so we can use it.

$page = $args;
//$_included_page = $page;
$page = strtolower($page);

if ($page != $Supple->getPagename()) 
{
	//Include the Show class. Show class must exist since we are being included from a page.
	//include_once ABSPATH.'/st-system/includes/Show.class.php';
	$ShowInclude = new Show(); //Create new instance so not to conflict with old one.
	$ShowInclude->setPagename($page);
	$ShowInclude->loadPage();
	echo $ShowInclude->getPageContent();
	unset($ShowInclude);
} 
else print '<em class="error">Circular reference.</em>';

?>
