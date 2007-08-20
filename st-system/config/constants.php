<?php

$config['dummy_key'] = 'dummy_value'; //Need this so that this config file will be valid.

//DATABASE
define('ST_PAGES_TABLE', 'pages');
define('ST_USERS_TABLE', 'users');
define('ST_CONFIG_TABLE', 'config');
define('ST_SESSIONS_TABLE', 'sessions');
define('ST_PAGE_METADATA_TABLE', 'page_metadata');

//FILE STRUCTURE
define('EXTERNAL_DIR', 'st-external/'); //Always have trailing slash to keep in CI convention.
define('THEMES_DIR', EXTERNAL_DIR.'themes/');

?>
