<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * This helper file is for small functions that don't really require
 * their own helper file.
 */  

$CI =& get_instance();

/**
 * Scans the specified directory and includes all files in that directory. 
 *
 * @access private
 * @param string $inDir Directory of files to be loaded. NOTE: The directory should be input with the trailing slash.
 * @param string $ext the extention of files to be loaded (defaults to '.php')
 */
function load_files_in_directory($inDir, $ext = '.php') {
	if ($handle = opendir($inDir)) 
	{
		//Need the !== so that directories called '0' don't break the loop
		while (false !== ($file = readdir($handle)))
		{
		    if (is_dir($inDir.$file))
		    {
                  if ($file != '.' && $file != '..')
                  {
                      load_files_in_directory($inDir.$file); // Recurse subdirectories
                  }
                  continue;
        }
			if (strpos($file, $ext) !== false) // Only php files, for safety.
			{
				//echo $inDir.$file."\n";
				include_once($inDir.$file);
			}
		}
		closedir($handle); 
	}
}


function does_page_exist($in_pagename) {
	global $CI;
	
	//$CI->load->model('pages_model', 'pages_model_pageexist');
	//$CI->pages_model_pageexist->pagename = $in_pagename;
	//$CI->pages_model_pageexist->loadPage();
	
	//This is quicker...
	$CI->db->select('id');
	$CI->db->from(ST_PAGES_TABLE);
	$CI->db->where('tag', $in_pagename);
	$CI->db->limit(1);
	$query = $CI->db->get();
	$id = element('id', $query->row_array());

	if(!empty($id))
	{
		return true;
	}
	
	return false;
}

?>