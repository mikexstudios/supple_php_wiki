<?php

$CI =& get_instance();

//global $Supple, $Show;
global $args; //Need this so we can use it.

$page = $args;
$page = strtolower($page);

if ($page != get_current_pagename()) 
{
	//Create another instance of the pages_model
	$CI->load->model('Pages_model', 'pages_model_include');
	$CI->pages_model_include->pagename = $page;
	//die($page);
	$CI->pages_model_include->loadPage();
		
	//Syntax formatting. 
	//$this->pages_model->page['body'] = format_text($this->pages_model->page['body']);
	
	echo $CI->pages_model_include->page['body'];
	
	unset($CI->pages_model_include);
} 
else print '<em class="error">Circular reference.</em>';

?>
