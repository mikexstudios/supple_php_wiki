<?php

/**
 * An alias for $Supple->doAction() that is used in templating
 * so that users have an easier time using "tags". Can accept
 * additional arguments which will be passed to ->doAction().
 *
 * @param string $inTag Short variable-like name associated with a function such as 'getimagetag'.
 * @return mixed Returns whatever the function associated to the tag returns. Could possibly be nothing. Usually, expect a string.
 */
function get($inTag) {
	global $Supple;

    if (func_num_args() > 1)
    {
        $args = func_get_args();
        return call_user_func_array(array(&$Supple, 'doAction'), $args);
    }

	return $Supple->doAction($inTag);
}

/**
 * Similar to get() as an alias for $Supple->doAction, but prints
 * the output rather than returning it. Can accept
 * additional arguments which will be passed to ->doAction().
 * 
 * @param string $inTag Short variable-like name associated with a function such as 'getimagetag'.
 */
function out($inTag) {
    global $Supple;
    
    if (func_num_args() > 1)
    {
        $args = func_get_args();
        echo call_user_func_array(array(&$Supple, 'doAction'), $args);
        return;
    }

    echo $Supple->doAction($inTag);
}

/**
 * Used in theme files to include other theme files. This function provides
 * the correct paths.
 * 
 * @param string $file The file to be included.   
 */ 
function theme_include($file)
{
    //global $themePath;
    
    include ABSPATH.'/st-external/themes/default/'.$file;
}

?>