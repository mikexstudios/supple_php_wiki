<?php

$CI =& get_instance();

/*
global $args; //Need this so we can use it.

$page = $args;
$page = strtolower($page);
*/
echo rtrim(base_url(), '/'); //Remove trailing slash. Better on the user syntax side.

?>
