<?php
/**
 * Assortment of install error messages
 */

$CI =& get_instance();

function error_already_installed() {
	global $CI;
	
	$data['error_title'] = 'Already Installed!';
	$data['error_content'] = '
		<p>
		 suppleText has already been installed. Therefore, this entire install script has
     been disabled. If you wish to reinstall suppleText, please delete st-config.php from
     /st-external.
		</p>
     <p> 
     If you need help you can always visit the 
     <a href="http://www.suppletext.org/forum">suppleText Support Forums</a>.
     </p>			
	';
	
	$CI->load->view('error', $data);
}

function error_directory_writable() {
	global $CI;
	
	$data['error_title'] = 'Error Writing config.php file!';
	$data['error_content'] = '
		<p>
     suppleText was not able to create the config.php file that stores your configuration
     information in /st-external/. Please check the following:
     <ul>
          <li>If you are using a linux or unix based system, make sure you
          <code>chmod 777</code> the /st-external/ directory. This usually involves
          right clicking the /st-external/ folder in your FTP client and selecting
          the chmod command. If you have shell access to your account issue the
          following command: <code>chmod 777 /st-external/</code> while in the suppleText
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

function error_no_config_temp() {
	global $CI;
	
	$data['error_title'] = 'Step 1 not Completed!';
	$data['error_content'] = '
		<p>
     suppleText could not find /st-external/config.temp.php which was generated
     during Step 1 of the installation.
		</p>
		<p> 
		 Please try <a href="index.php">running the installation script again from 
		 the beginning</a>. If you still need help you can always visit the 
     <a href="http://www.suppletext.org/forum">suppleText Support Forums</a>.
     </p>
	';
	
	$CI->load->view('error', $data);
}

function error_no_step($num) {
	global $CI;
	
	$data['error_title'] = 'Step '.$num.' not Completed!';
	$data['error_content'] = '
		<p>
     You did not seem to have completed Step '.$num.' of the installation! You should
     really follow the steps in the right order so that the installation can be
     completed successfully.
		</p>
		<p> 
		 Please try <a href="index.php">running the installation script again from 
		 the beginning</a>. If you still need help you can always visit the 
     <a href="http://www.suppletext.org/forum">suppleText Support Forums</a>.
     </p>
	';
	
	$CI->load->view('error', $data);
}

function error_no_dbschema($filename) {
	global $CI;
	
	$data['error_title'] = 'Missing Database Schema!';
	$data['error_content'] = '
		<p>
     suppleText could not find the database schema file:
		 <em>/st-system/install/sql/'.$filename.'</em> which is needed to create the database.
		</p>
		<p> 
		 Please check that <em>/st-system/install/sql/'.$filename.'</em>
     exists. If it does not exist, re-download suppleText and try installing 
		 it again. If you still need help you can always visit the 
     <a href="http://www.suppletext.org/forum">suppleText Support Forums</a>.
     </p>
	';
	
	$CI->load->view('error', $data);
}

function error_import_schema() {
	global $CI;
	
	$data['error_title'] = 'Error importing SQL Schema!';
	$data['error_content'] = '
		 <p>
     suppleText was not able to create tables and insert data necessary for the upgrade.
     Please check the following:
     </p>
     <ul>
          <li>One possible reason for this error is that the user that you 
          provided to access the database does not have write capabilities
          (such as using the commands CREATE TABLE, ALTER TABLE, and INSERT). Make sure the
          user has correct permissions.</li>
     </ul>
     <p>
     If you think everything is correct, click back and try to continue with the
     script installation. If you still need help you can always visit the 
     <a href="http://www.suppletext.org/forum">suppleText Support Forums</a>.
     Who knows? This could be a bug in the script!
     </p>
	';
	
	$CI->load->view('error', $data);
}

function error_creating_user() {
	global $CI;
	
	$data['error_title'] = 'Unable to create new user!';
	$data['error_content'] = '
		 <p>
     suppleText was not able to create a new user with the database connection 
     information you provided in step 1. One possible reason for this error 
     is that the user that you provided to access the database does not have 
     write capabilities (such as using the commands CREATE TABLE and INSERT). 
     Make sure the user has correct permissions. Or it could be that you are
     trying to install the script again and the user already exists.
     </p>
     <p>
     If you are unsure of what to do, you should probably contact your host. 
     If you still need help you can always visit the 
     <a href="http://www.suppletext.org/forum">suppleText Support Forums</a>.
     </p>
	';
	
	$CI->load->view('error', $data);
}

function error_renaming_config() {
	global $CI;
	
	$data['error_title'] = 'Unable to rename st-config.temp.php!';
	$data['error_content'] = '
		 <p>
     suppleText was not able to rename st-config.temp.php to st-config.php
     in /st-external. This is the final step in the installation. You can 
     try to correct the directory permissions of /st-external, click back in
     your browser, and try to complete step 3 again. Otherwise, you can
     just rename st-config.temp.php to st-config.php. Once that occurs, you have
     installed suppleText successfully and there is no need to come back to
     this installer.
     </p>
     <p>
     If you still need help you can always visit the 
     <a href="http://www.suppletext.org/forum">suppleText Support Forums</a>.
     </p>
	';
	
	$CI->load->view('error', $data);
}

?>
