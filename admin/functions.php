<?php

/*-----------------------------------------------------------------------------------*/
/* Definitions
/*-----------------------------------------------------------------------------------*/

define('SITE_DIR', '../');
define('ADMIN_DIR', '../admin/');
define('CONTENT_DIR', '../content/');
define('TEMPLATES_DIR', '../templates/');
define('ACTIVE_TEMPLATE', file_get_contents(CONTENT_DIR . 'site_template.txt'));
define('ACTIVE_TEMPLATE_DIR', '../templates/' . ACTIVE_TEMPLATE . '/');

/*-----------------------------------------------------------------------------------*/
/* Leeflets Version
/*-----------------------------------------------------------------------------------*/

function get_leeflets_version() {
    // The About File
    $about_leeflets = SITE_DIR . 'about.txt';
    
    // Reading Line Numbers
    $about_leeflets_line = file($about_leeflets);
    
    // Leeflet Version
    $leeflets_version = $about_leeflets_line[1];
    
    echo $leeflets_version;
}

/*-----------------------------------------------------------------------------------*/
/* Site URL
/*-----------------------------------------------------------------------------------*/

function get_home_url() {
    echo SITE_DIR;
}

/*-----------------------------------------------------------------------------------*/
/* Login & Logout
/*-----------------------------------------------------------------------------------*/

// User Credentials
include(SITE_DIR . 'config.php');

// User Machine
if (isset($_GET['action'])) {
        $action = $_GET['action'];
        switch ($action) {

            // Session Authentication
            case 'login':
                if ((isset($_POST['username']))
                && (isset($_POST['password']))
                && ($_POST['username']===$username)
                && ($_POST['password']===$password))
                {
                    $_SESSION['user']=true;
                    
                    // Redirect
                    header('Location: ' . '../admin/'); 
                } else {
                    $login_error = "<strong>Whoops!</strong> Something went wrong, please try again.";
                }
            break;
                
            // End Session    
            case 'logout':
                session_unset();
                session_destroy();
                
                // Redirect
                header('Location: ' . '../admin/');
            break;            
                
    }
}
					
/*-----------------------------------------------------------------------------------*/
/* Define Options Output
/*-----------------------------------------------------------------------------------*/

function options_machine($options) {			
    $counter = 0;
    $output = '';
    
    foreach ($options as $value) {	
        $counter++;
    	$val = '';
 
        switch ( $value['type'] ) {
            
            // Group Start
        	case 'group_start':
        	    $output .= '<div class="accordion-group">';
        		$output .= '<div class="page-header">';
        		$output .= '<h1><a data-toggle="collapse" data-parent="#'. $value['panel'] .'" href="#'. $value['id'] .'">'. $value['name'] .' <small>'. $value['desc'] .'</small></a></h1>';
        		$output .= '</div>';
        		$output .= '<div id="'. $value['id'] .'" class="collapse">';
        	break;
        	
        	// Group Finish
        	case 'group_finish':
        	    $output .= '</div>';
        	    $output .= '</div>';
        	break;
        	
        	// Headings
        	case 'heading':
        		$output .= '<div class="page-header">';
        		$output .= '<h1>'. $value['name'] .' <small>'. $value['desc'] .'</small></h1>';
        		$output .= '</div>';
        	break;
        	
        	// Text Fields
        	case 'text':
        		$output .= '<form id="'. $value['id'] .'" class="ajax-form well" method="POST" action="save.php">';
        		$output .= '<label><i class="icon-align-left icon-black"></i> <strong>'. $value['name'] .':</strong></label>';
        		
        		if (file_exists(''. CONTENT_DIR . $value['id'] .'.txt')) { 
        		    
        		    $output .= '<input type="text" class="input-large" name="content" value="'. file_get_contents(''. CONTENT_DIR . $value['id'] .'.txt') .'">';
        		
        		} else { 
        		    
        		    $output .= '<input type="text" class="input-large" name="content" value="'. file_get_contents(''. ACTIVE_TEMPLATE_DIR .'sample-content/'. $value['id'] .'.txt') .'">';
        		
        		}
        		
        		$output .= '<input type="hidden" name="content_file" value="'. $value['id'] .'">';
        		$output .= '<span class="help-block"><strong>Note: </strong>'. $value['desc'] .'</span>';
        		$output .= '<button type="submit" name="submit" value="submit" class="btn btn-info">Save '. $value['name'] .'</button>';
        		$output .= '</form>';
        	break;
        	
        	// Text Areas
        	case 'textarea':
        		$output .= '<form id="'. $value['id'] .'" class="ajax-form well" method="POST" action="save.php">';
        		$output .= '<label><i class="icon-align-left icon-black"></i> <strong>'. $value['name'] .':</strong></label>';
        		
        		if (file_exists(''. CONTENT_DIR . $value['id'] .'.txt')) { 
        		    
        		    $output .= '<textarea class="input-xlarge '. $value['edit'] .'" name="content" rows="'. $value['rows'] .'">'. file_get_contents(''. CONTENT_DIR . $value['id'] .'.txt') .'</textarea>';
        		
        		} else { 
        		    
        		    $output .= '<textarea class="input-xlarge '. $value['edit'] .'" name="content" rows="'. $value['rows'] .'">'. file_get_contents(''. ACTIVE_TEMPLATE_DIR .'sample-content/'. $value['id'] .'.txt') .'</textarea>';
        		
        		}
        		
        		$output .= '<input type="hidden" name="content_file" value="'. $value['id'] .'">';
        		$output .= '<span class="help-block"><strong>Note: </strong>'. $value['desc'] .'</span>';
        		$output .= '<button type="submit" name="submit" value="submit" class="btn btn-info">Save '. $value['name'] .'</button>';
        		$output .= '</form>';
        	break;
        	
        	// Radios
        	case "radio":
        	    $output .= '<form id="'. $value['id'] .'" class="ajax-form well" method="POST" action="save.php">';
        	    $output .= '<label><i class="icon-list icon-black"></i> <strong>'. $value['name'] .':</strong></label>';
				
				foreach ($value['opt'] as $key => $option) {
				    
				    $checked = '';
				    
				    if ($key == file_get_contents(''. CONTENT_DIR . $value['id'] .'.txt')) { $checked = ' checked'; } 
				    
					$output .= '<label class="radio">';
					$output .= '<input type="radio" name="content" value="'. $key .'" '. $checked .'/>';
					$output .= '' . $option .'';
					$output .= '</label>';
				}
				
				$output .= '<input type="hidden" name="content_file" value="'. $value['id'] .'">';
				$output .= '<span class="help-block"><strong>Note: </strong>'. $value['desc'] .'</span>';
				$output .= '<button type="submit" name="submit" value="submit" class="btn btn-info">Save '. $value['name'] .'</button>';
				$output .= '</form>';
			break;
        	
    	}
	   
	}
    return array($output);

}

/*-----------------------------------------------------------------------------------*/
/* Get Options & Build Panels
/*-----------------------------------------------------------------------------------*/

function get_options($part = '') {
    $options = array();
    
    if ($part == 'settings') : 
        
        // Prefix
        $prefix = 'site_';
        
        // Prefix
        $panel = 'site-settings';
    
        // Build the Settings Panel	Options
        include('settings.php');
        
    elseif ($part == 'content') : 
    
        // The Active Template Prefix	
        $prefix = ACTIVE_TEMPLATE .'_';
        
        // Prefix
        $panel = 'site-content';
        
        // Get the Active Template Content Options
        include(ACTIVE_TEMPLATE_DIR . 'content.php');   
    					
    endif;						
    
    // Build all Panels
    $get_options = options_machine($options);
    
    // Get Options Panels
    echo $get_options[0]; 
}

/*-----------------------------------------------------------------------------------*/
/* Get Latest Leeflets News
/*-----------------------------------------------------------------------------------*/

function get_latest_tweets($count = '1') {
    
    $statuses = simplexml_load_file('http://twitter.com/statuses/user_timeline/GetLeeflets.xml?count='. $count .'') or die('<blockquote>Sorry, but the latest news is not currently available.</blockquote>');
    
    foreach ($statuses->status as $status):
        
        $text = $status->text;
        $id = $status->id;
        $created_at = $status->created_at;
        
        { ?>
        <blockquote class="twitter-tweet"><p><?php echo $text; ?></p>&mdash; Leeflets (@GetLeeflets) <a href="https://twitter.com/GetLeeflets/status/<?php echo $id; ?>" data-datetime="<?php echo $created_at; ?>"><?php echo $created_at; ?></a></blockquote>
        <?php } 
    
    endforeach;

}

/*-----------------------------------------------------------------------------------*/
/* Display Available Templates
/*-----------------------------------------------------------------------------------*/

function get_available_templates() {
    
    // Currently Active Template
    $active_template = ACTIVE_TEMPLATE;

    // The Templates Directory
    $templates_directory = TEMPLATES_DIR;
    
    // Get All Templates in the Templates Directory
    $templates = glob($templates_directory . "*");

    foreach($templates as $template): 
    
        // Template Name    
        $template_dir_name = substr($template, 13); 
        
        // Screenshots
        $template_screenshot = ''. $templates_directory .''. $template_dir_name .'/screenshot.jpg';

        // The About File
        $about = ''. $templates_directory .''. $template_dir_name .'/about.txt';
        
        // About Array (Reading Line Numbers)
        $about_line = file($about);
        
        // Template Name
        $template_name = $about_line[1];
        
        // Template Description
        $template_description = $about_line[4];
        
        // Template Author
        $template_author = $about_line[7];
        
        // Template Author URL
        $template_author_url = $about_line[10];
        
        // Template Version
        $template_version = $about_line[13];
    
        { ?>
        <li class="span3">
            <div class="thumbnail">
                <div class="screenshot-frame"></div>
                
                <img src="<?php if (file_exists($template_screenshot)) { echo $template_screenshot; } else { echo ADMIN_DIR . 'images/screenshot.jpg'; } ?>">
                
                <div class="caption">
                    <h3><?php if ($template_name == '') : echo('Whoops!'); else : echo $template_name; endif; ?> <span class="badge badge-inverse"><?php if ($template_version == '') : echo('0.0'); else : echo $template_version; endif; ?></span></h3>
                    
                    <h6>by: <a href="<?php echo $template_author_url; ?>"><?php if ($template_author == '') : echo('Who Knows'); else : echo $template_author; endif; ?></a></h6>
                    
                    <p><?php if ($template_description == '') : echo('This Leeflet is unfinished, broken or maybe it just did not install properly. Probably a good idea to re-install it.'); else : echo $template_description; endif; ?></p>
                    
                    <p>
                        <?php if ($active_template == $template_dir_name) : ?>
                        <span class="btn btn-info disabled"><i class="icon-ok-circle icon-white"></i> Currently Active</span>
                        <?php else : ?>
                        <form id="activate-<?php echo $template_dir_name; ?>" class="ajax-form" method="POST" action="save.php">
                            <input type="hidden" name="content_file" value="site_template">
                            <input type="hidden" name="content" value="<?php echo $template_dir_name; ?>">
                            <button type="submit" name="submit" value="submit" class="btn btn-info">Activate</button>
                        </form>
                        
                        <a class="btn" data-toggle="modal" href="#delete-<?php echo $template_dir_name; ?>">Delete</a>
                        <?php endif; ?>
                    </p>
                    
                    <div class="modal fade hide" id="delete-<?php echo $template_dir_name; ?>">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">×</button>
                            <h3>You Are About to Delete <?php echo $template_name; ?></h3>
                        </div>
                        
                        <div class="modal-body">
                            <p>Are you absolutely sure you want to delete the <?php echo $template_name; ?> template?.</p>
                        </div>
                        
                        <div class="modal-footer">
                            <a href="#" class="btn" data-dismiss="modal">Cancel</a>
                            <form id="delete-<?php echo $template_dir_name; ?>" class="ajax-form" method="POST" action="actions/delete.php">
                                <input type="hidden" name="leeflet_path" value="<?php echo TEMPLATES_DIR . $template_dir_name; ?>">
                                <button type="submit" name="submit" value="submit" class="btn btn-danger">Delete</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </li>    
        <?php } 
    
    endforeach;

}

/*-----------------------------------------------------------------------------------*/
/* Display Premium Templates
/*-----------------------------------------------------------------------------------*/

//include(ADMIN_DIR . 'includes/feedcache.php');

function get_premium_templates($type = 'all') {

    //$feed_cache = new FeedCache('leeflets-'. $type .'.xml', 'http://leeflets.com/updates/leeflets-'. $type .'.xml');
    //$leeflets = simplexml_load_string($feed_cache->get_data());
    
    $templates = simplexml_load_file('http://leeflets.com/updates/templates-'. $type .'.xml');
    
    foreach ($templates as $template):
        
        $template_name=$template->name;
        $template_file_name=$template->file_name;
        $template_price=$template->price;
        $template_buy_url=$template->buy_url;
        $template_featured=$template->featured;
        
        // The About File
        $about = 'http://leeflets.com/view/'. $template_file_name .'/templates/'. $template_file_name .'/about.txt';
        
        // About Array (Reading Line Numbers)
        $about_line = file($about);
        
        // Template Description
        $template_description = $about_line[4];
        
        // Template Author
        $template_author = $about_line[7];
        
        // Template Author URL
        $template_author_url = $about_line[10];
        
        // Template Version
        $template_version = $about_line[13];
        
        { ?>
        <li class="span3">
            <div class="thumbnail">
                <div class="screenshot-frame"></div>
                 
                <img src="http://leeflets.com/view/<?php echo $template_file_name; ?>/templates/<?php echo $template_file_name; ?>/screenshot.jpg" alt="<?php echo $template_name; ?>">
                
                <div class="caption">
                    <h3><?php echo $template_name; ?> <span class="badge badge-inverse"><?php echo $template_version; ?></span></h3>
                    
                    <h6>by: <a href="<?php echo $template_author_url; ?>"><?php echo $template_author; ?></a></h6>
                    
                    <p><?php echo $template_description; ?></p>
                    
                    <p><a class="btn btn-success" data-toggle="modal" href="#buy-<?php echo $template_file_name; ?>">Purchase <?php echo $template_price; ?></a> <a href="http://leeflets.com/view/<?php echo $template_file_name; ?>/" class="btn" target="_blank">Live Demo</a></p>
                    
                    <div class="buy modal fade hide" id="buy-<?php echo $template_file_name; ?>">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">×</button>
                            <h3>Purchase <?php echo $template_name; ?></h3>
                        </div>
                        
                        <div class="modal-body">
                            <p>Click "Buy Now" below to continue to <strong>PayPal.com</strong> and complete your purchase. After your payment has successfully cleared (usually instant), you will receive purchase confirmation and a unique link via email which you will be able to use to download and install the <?php echo $template_name; ?> template.</p>
                        </div>
                        
                        <div class="modal-footer">
                            <a href="#" class="btn" data-dismiss="modal">Close</a>
                            <a href="<?php echo $buy_url; ?>" class="btn btn-success">Buy Now <?php echo $template_price; ?></a>
                        </div>
                    </div>
                </div>
            </div>
        </li>
        <?php } 
    
    endforeach;

}
					
?>