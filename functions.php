<?php

define('ACTIVE_LEEFLET', file_get_contents('./content/site_leeflet.txt'));
define('ACTIVE_LEEFLET_DIR', './leeflets/' . ACTIVE_LEEFLET);

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

function get_leeflet_part($part = '') {
    include('./leeflets/'. ACTIVE_LEEFLET .'/' . $part . '.php'); 
}

/*-----------------------------------------------------------------------------------*/
/* Leeflet Content Parts
/*-----------------------------------------------------------------------------------*/

function get_content_part($part = '') {

    if (file_exists('./content/'. ACTIVE_LEEFLET .'_'. $part .'.txt')) { 
        
        echo file_get_contents('./content/'. ACTIVE_LEEFLET .'_'. $part .'.txt');
    
    } else { 
        
        echo file_get_contents('./leeflets/'. ACTIVE_LEEFLET .'/sample-content/'. ACTIVE_LEEFLET .'_'. $part .'.txt');
    
    }

}

/*-----------------------------------------------------------------------------------*/
/* Leeflet Directory
/*-----------------------------------------------------------------------------------*/

function get_leeflet_dir() {
    echo ACTIVE_LEEFLET_DIR;
}

?>