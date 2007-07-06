<?php
global $Supple; //Need this to access $Supple

//Include the Show class
include_once ABSPATH.'/st-system/includes/Show.class.php';
$Show = new Show();

$Show->setPagename($Supple->getPagename());
//Set the time if specified in GET
if(!empty($_GET['time']))
{
	$Show->setTime($_GET['time']);
}
$Show->loadPage();

//Syntax formatting. Include syntax file:
include_once(ABSPATH.'/st-system/formatters/creole.php');
$Supple->SyntaxParser->setText($Show->page['body']);
$Supple->SyntaxParser->applyAll();
$Show->page['body'] = $Supple->SyntaxParser->getText();

//Load template:
include get_theme_system_path('show.tpl.php');

?>