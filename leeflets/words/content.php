<?php
                    
/*-----------------------------------------------------------------------------------*/
/* Title Text Options
/*-----------------------------------------------------------------------------------*/                                       

$options[] = array( "name"  =>  "Your Title",
                    "desc"  =>  "This is the title displayed on your page.",
                    "id"    =>  $prefix ."your_title",
                    "type"  =>  "group_start",
                    "panel" =>  $panel );			
					
$options[] = array( "name"  =>  "Title Text",
					"desc"  =>  "Pretty straight forward don't ya think.",
					"id"    =>  $prefix ."content_title",
					"type"  =>  "text" );
					
$options[] = array( "type"  =>  "group_finish" );					

/*-----------------------------------------------------------------------------------*/
/* Paragraph Text Options
/*-----------------------------------------------------------------------------------*/

$options[] = array( "name"  =>  "Your Words",
                    "desc"  =>  "This is the text displayed on your page.",
                    "id"    =>  $prefix ."your_words",
                    "type"  =>  "group_start",
                    "panel" =>  $panel );			
					
$options[] = array( "name"  =>  "Paragraph Text",
					"desc"  =>  "Pretty straight forward don't ya think.",
					"id"    =>  $prefix ."content_text",
					"type"  =>  "textarea",
					"edit"  =>  "fancy",
					"rows"  =>  "8" );
					
$options[] = array( "type"  =>  "group_finish" );					

/*-----------------------------------------------------------------------------------*/
/* Left Hand Button Link
/*-----------------------------------------------------------------------------------*/
					
$options[] = array( "name"  =>  "Left Button Link",
                    "desc"  =>  "The text and link for the left hand blue button.",
                    "id"    =>  $prefix ."left_button",
                    "type"  =>  "group_start",
                    "panel" =>  $panel );			
					
$options[] = array( "name"  =>  "Left Link Text",
					"desc"  =>  "The text for the left hand button link.",
					"id"    =>  $prefix ."left_link_text",
					"type"  =>  "text" );	
					
$options[] = array( "name"  =>  "Left Link",
					"desc"  =>  "The actual link for the left hand button link.",
					"id"    =>  $prefix ."left_link",
					"type"  =>  "text" );
					
$options[] = array( "type"  =>  "group_finish" );					
					
/*-----------------------------------------------------------------------------------*/
/* Right Hand Button Link
/*-----------------------------------------------------------------------------------*/					
					
$options[] = array( "name"  =>  "Right Button Link",
                    "desc"  =>  "The text and link for the right hand blue button.",
                    "id"    =>  $prefix ."right_button",
                    "type"  =>  "group_start",
                    "panel" =>  $panel );						
					
$options[] = array( "name"  =>  "Right Link Text",
					"desc"  =>  "The text for the right hand button link.",
					"id"    =>  $prefix ."right_link_text",
					"type"  =>  "text" );	
					
$options[] = array( "name"  =>  "Right Link",
					"desc"  =>  "The actual link for the right hand button link.",
					"id"    =>  $prefix ."right_link",
					"type"  =>  "text" );	
					
$options[] = array( "type"  =>  "group_finish" );				

?>