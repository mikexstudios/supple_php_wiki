<?php
global $Supple; //Need this to access $Supple

//Load Edit class
include_once ABSPATH.'/st-system/includes/Edit.class.php';
$Edit = new Edit();
$Edit->setPagename($Supple->getPagename());

//Load Show class
include_once ABSPATH.'/st-system/includes/Show.class.php';
$Show = new Show();
$Show->registerActions();
$Show->setPagename($Supple->getPagename());
$Show->loadPage();

//Maybe we should to include the time parameter to show here so
//we have the ability to edit old pages.


//Get info from POST. We should secure this:
//Also see edit.php (Wikka's) for more info how to better do this.
if(strcmp($_POST['submit'], 'Store') == 0)
{
	// strip CRLF line endings down to LF to achieve consistency ... plus it saves database space.
	// Note: these codes must remain enclosed in double-quotes to work!
	$body = str_replace("\r\n", "\n", $Supple->Input->post('body')); //We don't check for XSS here yet.
	$body = preg_replace("/\n[ ]{4}/", "\n\t", $body);	// @@@ FIXME: misses first line and multiple sets of four spaces

	// we don't need to escape here, we do that just before display (i.e., treat note just like body!)
	$note = trim($Supple->Input->post('note', true));
	
	//Should check for overwriting.

	//DO THIS LATER: only save if new body differs from old body
	$Edit->setAuthor($Supple->UserManagement->getUsername());
	
	$Edit->setContent($body);
	$Edit->setEditnote($note);
	
	$Edit->storeChanges();
	redirect_page($Edit->pagename);
}

//Otherwise, we load the page.
include get_theme_system_path('edit.tpl.php');

?>