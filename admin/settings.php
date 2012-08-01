<?php

/*-----------------------------------------------------------------------------------*/
/* Site Meta
/*-----------------------------------------------------------------------------------*/                   

$options[] = array( "name"  =>  "Site Meta",
                    "desc"  =>  "Really important stuff here, so pay attention!",
                    "id"    =>  $prefix ."meta",
                    "type"  =>  "group_start",
                    "panel" =>  $panel );
		
$options[] = array( "name"  =>  "Site Title",
					"desc"  =>  "Pretty self explanitory, don't you think?",
					"id"    =>  $prefix ."title",
					"type"  =>  "text" );	
					
$options[] = array( "name"  =>  "Site Author",
					"desc"  =>  "Nothing fancy here, just type your name.",
					"id"    =>  $prefix ."author",
					"type"  =>  "text" );									
					
$options[] = array( "name"  =>  "Site Description",
					"desc"  =>  "This is the description that will be indexed by Google and other search engines.",
					"id"    =>  $prefix ."description",
					"type"  =>  "textarea",
					"rows"  =>  "4" );
					
$options[] = array( "type"  =>  "group_finish" );

/*-----------------------------------------------------------------------------------*/
/* Privacy Settings
/*-----------------------------------------------------------------------------------*/                   

$options[] = array( "name"  =>  "Privacy Settings",
                    "desc"  =>  "This lets you define your sites visibility.",
                    "id"    =>  $prefix ."privacy",
                    "type"  =>  "group_start",
                    "panel" =>  $panel );
		
$options[] = array( "name"  =>  "Site Visibility",
					"desc"  =>  "Neither of these options blocks access to your site — it is up to search engines to honor your request.",
					"id"    =>  $prefix ."visibility",
					"type"  =>  "radio",
					"opt"   =>  array ( "1" => "Allow search engines to index this site.", 
					                    "2" => "Ask search engines not to index this site." ),	
					"std"   =>  "1" );
					
$options[] = array( "type"  =>  "group_finish" );	

/*-----------------------------------------------------------------------------------*/
/* Social Media
/*-----------------------------------------------------------------------------------*/                   

$options[] = array( "name"  =>  "Social Media",
                    "desc"  =>  "Enter your social media account info below.",
                    "id"    =>  $prefix ."social",
                    "type"  =>  "group_start",
                    "panel" =>  $panel );
		
$options[] = array( "name"  =>  "Twitter Username",
					"desc"  =>  "Just your username (e.g. GetLeeflets).",
					"id"    =>  $prefix ."twitter",
					"type"  =>  "text" );	
					
$options[] = array( "name"  =>  "Facebook Address",
					"desc"  =>  "Your public Facebook address (e.g. http://facebook.com/your-name).",
					"id"    =>  $prefix ."facebook",
					"type"  =>  "text" );
					
$options[] = array( "type"  =>  "group_finish" );	

/*-----------------------------------------------------------------------------------*/
/* Site Analytics
/*-----------------------------------------------------------------------------------*/ 				
					
$options[] = array( "name"  =>  "Site Analytics",
                    "desc"  =>  "Only if you want to (this is optional).",
                    "id"    =>  $prefix ."analy",
                    "type"  =>  "group_start",
                    "panel" =>  $panel );					
					
$options[] = array( "name"  =>  "Analytics Code",
					"desc"  =>  "Paste your analytics code below to begin tracking this site.",
					"id"    =>  $prefix ."analytics",
					"type"  =>  "textarea",
					"rows"  =>  "8" );
					
$options[] = array( "type"  =>  "group_finish" );						

?>