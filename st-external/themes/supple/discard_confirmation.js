/**
 * Discard changes confirmation on navigating away from page.
 * @author Michael Huynh (mike@mikexstudios.com)
 * Usage: In input form fields, add the onChange="form_changed()"
*/

var is_form_changed = false;

function unload_message() {
	if(is_form_changed)
	{
		return "You made some edits to the page. If you leave this page all changes will be lost."
 	}
}

function form_changed() {
	is_form_changed = true;
}

window.onbeforeunload = unload_message;
