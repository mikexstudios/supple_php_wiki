<?php
global $Diff, $Supple; //Need this to access $Supple

//Load Revisions class
include_once ABSPATH.'/st-system/includes/Revisions.class.php';
$Revisions = new Revisions();
$Revisions->setPagename($Supple->getPagename());

//Load PageDiff class
include_once ABSPATH.'/st-system/includes/PageDiff.class.php';
$Diff = new PageDiff();
$Diff->setPagename($Supple->getPagename());

//Load Show class
include_once ABSPATH.'/st-system/includes/Show.class.php';
$Show = new Show();
$Show->setPagename($Supple->getPagename());
$Show->loadPage();

include_once ABSPATH.'/st-system/formatters/creole.php';

//Get list of revisions
$revisions = $Revisions->getRevisionList();
for($i=0; $i < count($revisions); $i++)
{
	if(isset($revisions[$i+1])) //if the next revision exists
	{
		//echo $revisions[$i]['id'].' ';
		$Diff->setRevisionA($revisions[$i]['id']);
		$Diff->setRevisionB($revisions[$i+1]['id']);
		$Diff->computeDifferences();
		$revision_a_data[$i] = $Diff->getRevisionAData();
		$added[$i] = $Diff->getAdded();
				$Supple->SyntaxParser->setText($added[$i]);
				$Supple->SyntaxParser->applyAll();
				$added[$i] = $Supple->SyntaxParser->getText();
		$revision_b_data[$i] = $Diff->getRevisionBData();
		$deleted[$i] = $Diff->getDeleted();
				$Supple->SyntaxParser->setText($deleted[$i]);
				$Supple->SyntaxParser->applyAll();
				$deleted[$i] = $Supple->SyntaxParser->getText();
	}
}
$num_revisions = $i;

/*
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
*/

//Load the page.
include get_theme_system_path('history.tpl.php');

?>