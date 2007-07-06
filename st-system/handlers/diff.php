<?php
global $Diff, $Supple; //Need this to access $Supple

//Load Revisions class
include_once ABSPATH.'/st-system/includes/PageDiff.class.php';
$Diff = new PageDiff();
$Diff->setPagename($Supple->getPagename());

//Load Show class
include_once ABSPATH.'/st-system/includes/Show.class.php';
$Show = new Show();
$Show->setPagename($Supple->getPagename());
$Show->loadPage();

$Diff->setRevisionA($_GET['a']);
$Diff->setRevisionB($_GET['b']);
$Diff->computeDifferences();


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

//Load the page.
include get_theme_system_path('diff.tpl.php');
