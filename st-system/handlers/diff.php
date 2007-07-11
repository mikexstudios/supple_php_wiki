<?php
global $Diff, $Supple; //Need this to access $Supple

//Load Revisions class
include_once ABSPATH.'/st-system/includes/PageDiff.class.php';
$Diff = new PageDiff();
$Diff->setPagename($Supple->getPagename());

//Load Show class
include_once ABSPATH.'/st-system/includes/Show.class.php';
$Show = new Show();
$Show->registerActions();
$Show->setPagename($Supple->getPagename());
$Show->loadPage();

//Secure input
$a = $Supple->Input->get('a', true);
$b = $Supple->Input->get('b', true);
if(!$Supple->Validation->numeric($a) || !$Supple->Validation->numeric($b)) 
{
	die("Specified page id's are invalid."); //TODO: Make error messages pretty.
}

$Diff->setRevisionA($a);
$Diff->setRevisionB($b);
$Diff->computeDifferences();
global $revision_a_data, $revision_b_data;
$revision_a_data = $Diff->getRevisionAData();
$revision_b_data = $Diff->getRevisionBData();


$Supple->registerAction('diff_added', 'getAddedFormatted');
$Supple->registerAction('diff_deleted', 'getDeletedFormatted');
function getAddedFormatted() {
	global $Diff, $Supple;
	
	include_once ABSPATH.'/st-system/formatters/creole.php';
	$Supple->SyntaxParser->setText($Diff->getAdded());
	$Supple->SyntaxParser->applyAll();
	return $Supple->SyntaxParser->getText();
}
function getDeletedFormatted() {
	global $Diff, $Supple;
	
	include_once ABSPATH.'/st-system/formatters/creole.php';
	$Supple->SyntaxParser->setText($Diff->getDeleted());
	$Supple->SyntaxParser->applyAll();
	return $Supple->SyntaxParser->getText();
}

$Supple->registerAction('revision_a_time', 'getATime');
$Supple->registerAction('revision_b_time', 'getBTime');
function getATime() {
	global $revision_a_data;
	
	return $revision_a_data['time'];
}
function getBTime() {
	global $revision_b_data;
	
	return $revision_b_data['time'];
}

//Load the page.
include get_theme_system_path('diff.tpl.php');

?>