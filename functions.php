<?php

define('ACTIVE_TEMPLATE', file_get_contents('./content/site_template.txt'));
define('ACTIVE_TEMPLATE_DIR', './templates/' . ACTIVE_TEMPLATE);

/*-----------------------------------------------------------------------------------*/
/* Leeflets Version
/*-----------------------------------------------------------------------------------*/

function get_leeflets_version() {
    // The About File
    $about_leeflets = './about.txt';
    
    // Reading Line Numbers
    $about_leeflets_line = file($about_leeflets);
    
    // Leeflet Version
    $leeflets_version = $about_leeflets_line[1];
    
    echo $leeflets_version;
}

/*-----------------------------------------------------------------------------------*/
/* Site Settings & Info
/*-----------------------------------------------------------------------------------*/

function get_site_info($info = '') {
    echo file_get_contents('./content/site_'. $info .'.txt');
}

/*-----------------------------------------------------------------------------------*/
/* Leeflet Parts
/*-----------------------------------------------------------------------------------*/

function get_template_part($part = '') {
    include('./templates/'. ACTIVE_TEMPLATE .'/' . $part . '.php'); 
}

/*-----------------------------------------------------------------------------------*/
/* Leeflet Content Parts
/*-----------------------------------------------------------------------------------*/

function get_content_part($part = '') {

    if (file_exists('./content/'. ACTIVE_TEMPLATE .'_'. $part .'.txt')) { 
        
        echo file_get_contents('./content/'. ACTIVE_TEMPLATE .'_'. $part .'.txt');
    
    } else { 
        
        echo file_get_contents('./templates/'. ACTIVE_TEMPLATE .'/sample-content/'. ACTIVE_TEMPLATE .'_'. $part .'.txt');
    
    }

}

/*-----------------------------------------------------------------------------------*/
/* Leeflet Directory
/*-----------------------------------------------------------------------------------*/

function get_template_dir() {
    echo ACTIVE_TEMPLATE_DIR;
}

?>