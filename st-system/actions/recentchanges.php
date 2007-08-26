<?php

$CI =& get_instance();

//We don't take any arguments

$CI->load->model('pages_model', 'recentchanges_pages_model');
$recent_changes_info = $CI->recentchanges_pages_model->get_all_revisions_data(100); //Get last 50 changes

//The changes should be in date/time order. We loop through (since the changes
//are in desc order) and print out the date. Then pages changed on that date.
//Once the day changes, we change the date printed.

$prev_day = ''; //mdate('%j', $recent_changes_info[0]['time']); //Initially, we set the previous day to the first day.
$first_header_print = false;
foreach($recent_changes_info as $each_change) 
{
	$day = mdate('%j', $each_change['time']);
	if($day !== $prev_day)
	{
		
		//A fix for the first \n not appearing
		if($first_header_print==true)
		{
			echo "\n";
		}
		else
		{
			$first_header_print=true;
		}
		
		//Print the date
		echo '**'.mdate('%l %d %F %Y', $each_change['time']).'**'."\n\n";
	}
	
	echo '*([['.construct_page_url($each_change['tag'], 'show', $each_change['id']).'|'.mdate('%H:%i %T', $each_change['time']).']])  ~[[['.construct_page_url($each_change['tag'], 'revisions').'|revisions]]] - [['.$each_change['tag'].']] &#8594; '.$each_change['user']."\n";
	
	$prev_day = $day;
	
	//print_r($each_change);
}

?>
