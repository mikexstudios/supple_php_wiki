<?php

/**
 * Need to specify the table prefix (even though not used)
 * to keep constants.php (included from the base config directory)
 * from erroring.
 */   
$config['table_prefix'] = "st_";

//Load constants (includes database tables)
include_once ABSPATH.'st-system/config/constants.php';

?>
