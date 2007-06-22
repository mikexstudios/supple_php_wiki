<?php
/**
 * Display a page if the user has read access or is an admin.
 * 
 * This is the default page handler used by Wikka when no other handler is specified.
 * Depending on user privileges, it displays the page body or an error message. It also
 * displays footer comments and a form to post comments, depending on ACL and general 
 * config settings.
 * 
 * @package		Handlers
 * @subpackage	Page
 * @version		$Id: show.php 559 2007-06-17 07:38:19Z BrianKoontz $
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @filesource
 * 
 * @uses		Wakka::Format()
 * @uses		Wakka::FormClose()
 * @uses		Wakka::FormOpen()
 * @uses		Wakka::GetConfigValue()
 * @uses		Wakka::GetPageTag()
 * @uses		Wakka::GetUser()
 * @uses		Wakka::GetUserName()
 * @uses		Wakka::HasAccess()
 * @uses		Wakka::Href()
 * @uses		Wakka::htmlspecialchars_ent()
 * @uses		Wakka::LoadComments()
 * @uses		Wakka::LoadPage()
 * @uses		Wakka::LoadUser()
 * @uses		Wakka::UserIsOwner()
 * @uses		Config::$anony_delete_own_comments
 * @uses		Config::$hide_comments
 * 
 * @todo		move <div> to template;
 */

echo "\n".'<!--starting page content-->'."\n";
echo '<div class="page">';


if (!$this->page)
{
	$createlink = '<a href="'.$this->Href('edit').'">'.WIKKA_PAGE_CREATE_LINK_DESC.'</a>';
	echo '<p>'.sprintf(SHOW_ASK_CREATE_PAGE_CAPTION,$createlink).'</p>'."\n";
	echo '</div><!--closing page content-->'."\n"; //TODO: move to templating class
}
else
{
	//THIS CODE IS FOR IF THE PAGE ISN'T LATEST VERSION. SHOW EDIT BOX, ETC.
	if ($this->page['latest'] == 'N')
	{
		$pagelink = '<a href="'.$this->Href().'">'.$this->tag.'</a>';
		echo '<div class="revisioninfo">'.sprintf(SHOW_OLD_REVISION_CAPTION,$pagelink,$this->Link($this->tag, 'revisions', $this->page['time']));
		// if this is an old revision, display some buttons
		if ($this->page['latest'] == 'N' && $this->HasAccess('write'))
		{
			// added if encapsulation : in case where some pages were brutally deleted from database
			if ($latest = $this->LoadPage($this->tag))
			{
?>
		<br />
			<?php echo $this->FormOpen('edit') ?>
			<input type="hidden" name="previous" value="<?php echo $latest['id'] ?>" />
			<input type="hidden" name="body" value="<?php echo $this->htmlspecialchars_ent($this->page['body']) ?>" />
			<input type="submit" value="<?php echo SHOW_RE_EDIT_BUTTON ?>" />
			<?php echo $this->FormClose(); ?>
<?php
			}
		}
	echo '</div>';
	}

	// display page
	echo $this->Format($this->page['body'], 'wakka', 'page');
	echo "\n".'</div><!--closing page content-->'."\n\n";
	
	//REMOVED COMMENTS PART OF THE PAGE FOR NOW

}

?>
