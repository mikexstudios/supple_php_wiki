<?php

$config['dummy'] = 'variable'; //Need to add a dummy config to dispel Error

//DATABASE
define('ST_PAGES_TABLE', ((isset($config['pages_table_prefix'])) ? $config['pages_table_prefix'] : $config['table_prefix']).'pages');
define('ST_USERS_TABLE', ((isset($config['users_table_prefix'])) ? $config['users_table_prefix'] : $config['table_prefix']).'users');
define('ST_CONFIG_TABLE', ((isset($config['config_table_prefix'])) ? $config['config_table_prefix'] : $config['table_prefix']).'config');
define('ST_SESSIONS_TABLE', ((isset($config['sessions_table_prefix'])) ? $config['sessions_table_prefix'] : $config['table_prefix']).'sessions');
$config['sess_table_name'] = ST_SESSIONS_TABLE; //We have to set the session_table here
define('ST_PAGE_METADATA_TABLE', ((isset($config['page_metadata_table_prefix'])) ? $config['page_metadata_table_prefix'] : $config['table_prefix']).'page_metadata');

//FILE STRUCTURE
define('EXTERNAL_DIR', 'st-external/'); //Always have trailing slash to keep in CI convention.
define('THEMES_DIR', EXTERNAL_DIR.'themes/');

?>
