<?php
/**
 * Assortment of install error messages
 */

$CI =& get_instance();

function error_directory_writable() {
	global $CI;
	
	$data['error_title'] = 'Error Writing config.php file!';
	$data['error_content'] = '
		<p>
     suppleText was not able to create the config.php file that stores your configuration
     information in /st-external/. Please check the following:
     <ul>
          <li>If you are using a linux or unix based system, make sure you
          <code>chmod 755</code> the /st-external/ directory. This usually involves
          right clicking the /st-external/ folder in your FTP client and selecting
          the chmod command. If you have shell access to your account issue the
          following command: <code>chmod 755 /st-external/</code> while in the suppleText
          root directory (where index.php and LICENSE are).</li>
          
          <li>If you are on a windows based system, /st-external/ should already
          have write permissions. Please check that the directory exists.
          Otherwise please contact your host for help.</li>
     </ul>
     <p> 
     Once you have fixed the chmod problem, click back on the browser and try
     the last step again. If you still need help you can always visit the 
     <a href="http://www.suppletext.org/forum">suppleText Support Forums</a>.
     </p>			
	';
	
	$CI->load->view('error', $data);
} 

function error_db_connect() {
	global $CI;
	
	$data['error_title'] = 'Database Connect Error';
	$data['error_content'] = '
		<p>
     suppleText could not connect to the database with the information you provided.
     There are a few things you should double check:
     </p>
     <ul>
          <li>Did you select the correct database type and version?</li>
          <li>Are you sure you have the correct database name?</li>
          <li>Are you sure you have the correct username and password?</li>
          <li>Are you sure that you have typed the correct hostname?</li>
          <li>Are you sure that the database server is running?</li>
     </ul>
     <p>
     <strong>Click the back button on your browser and try correcting the 
     information</strong>. If you are unsure what these terms mean you should 
     probably contact your host. If you still need help you can always visit the 
     <a href="http://www.suppletext.org/forum">suppleText Support Forums</a>.
     </p>	
	';
	
	$CI->load->view('error', $data);
}

function error_sample_config_file() {
	global $CI;
	
	$data['error_title'] = 'config.php.sample Missing Error';
	$data['error_content'] = '
		<p>
     suppleText could not find /st-external/config.php.sample which is required to write
     the final config.php file! 
		</p>
		<p> 
		 Please check that /st-external/config.php.sample
     exists. If it does not exist, re-download suppleText and try installing it again. If you 
     still need help you can always visit the 
     <a href="http://www.suppletext.org/forum">suppleText Support Forums</a>.
     </p>
	';
	
	$CI->load->view('error', $data);
}


?>
