<?php

$config['dummy_key'] = 'dummy_value'; //Need this so that this config file will be valid.

//DATABASE
define('ST_PAGES_TABLE', 'pages');
define('ST_USERS_TABLE', 'users');
define('ST_ARCHIVES_TABLE', 'archives');
define('ST_CONFIG_TABLE', 'config');
define('ST_CACHE_TABLE', 'cache');
define('ST_SESSIONS_TABLE', 'sessions');

//FILE STRUCTURE
define('EXTERNAL_DIR', 'st-external/'); //Always have trailing slash to keep in CI convention.
define('THEMES_DIR', EXTERNAL_DIR.'themes/');

?>
