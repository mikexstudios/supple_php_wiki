<?php

$CI =& get_instance();

//global $Supple, $Show;
global $args; //Need this so we can use it.

$tag = $args;
$tag = strtolower($tag);

if(preg_match('/[a-zA-Z0-9_-]+/', $tag))
{
	out($tag);
}


?>
