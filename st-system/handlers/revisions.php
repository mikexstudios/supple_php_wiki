<?php
global $Supple; //Need this to access $Supple

//Load Revisions class
include_once ABSPATH.'/st-system/includes/Revisions.class.php';
$Revisions = new Revisions();
$Revisions->setPagename($Supple->getPagename());

/*
$temp = $Revisions->getRevisionList();
foreach($temp as $each) 
{
	echo $each['id']."\n";
}
*/

//Load Show class
include_once ABSPATH.'/st-system/includes/Show.class.php';
$Show = new Show();
$Show->setPagename($Supple->getPagename());
$Show->loadPage();

//Load the page.
include get_theme_system_path('revisions.tpl.php');
