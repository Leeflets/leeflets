<?php

/*-----------------------------------------------------------------------------------*/
/* Definitions
/*-----------------------------------------------------------------------------------*/

define('SITE_DIR', '../');
define('ADMIN_DIR', '../admin/');
define('CONTENT_DIR', '../content/');
define('ACTIONS_DIR', '../admin/actions/');
define('LEEFLETS_DIR', '../leeflets/');
define('ACTIVE_LEEFLET', file_get_contents(CONTENT_DIR . 'site_leeflet.txt'));
define('ACTIVE_LEEFLET_DIR', '../leeflets/' . ACTIVE_LEEFLET . '/');

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
        		    
        		    $output .= '<input type="text" class="input-large" name="content" value="'. file_get_contents(''. ACTIVE_LEEFLET_DIR .'sample-content/'. $value['id'] .'.txt') .'">';
        		
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
        		    
        		    $output .= '<textarea class="input-xlarge '. $value['edit'] .'" name="content" rows="'. $value['rows'] .'">'. file_get_contents(''. ACTIVE_LEEFLET_DIR .'sample-content/'. $value['id'] .'.txt') .'</textarea>';
        		
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
    
        // The Active Leeflet Prefix	
        $prefix = ACTIVE_LEEFLET .'_';
        
        // Prefix
        $panel = 'site-content';
        
        // Get the Active Leeflet Content Options
        include(ACTIVE_LEEFLET_DIR . 'content.php');   
    					
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
/* Display Available Leeflets
/*-----------------------------------------------------------------------------------*/

function get_available_leeflets() {
    
    // Currently Active Leeflet
    $active_leeflet = ACTIVE_LEEFLET;

    // The Leeflets Directory
    $leeflets_directory = LEEFLETS_DIR;
    
    // Get All Leeflets in the Leeflets Directory
    $available_leeflets = glob($leeflets_directory . "*");

    foreach($available_leeflets as $leeflet): 
    
        // Leeflet Name    
        $leeflet_dir_name = substr($leeflet, 12); 
        
        // Screenshots
        $leeflet_screenshot = ''. $leeflets_directory .''. $leeflet_dir_name .'/screenshot.jpg';

        // The About File
        $about = ''. $leeflets_directory .''. $leeflet_dir_name .'/about.txt';
        
        // About Array (Reading Line Numbers)
        $about_line = file($about);
        
        // Leeflet Name
        $leeflet_name = $about_line[1];
        
        // Leeflet Description
        $leeflet_description = $about_line[4];
        
        // Leeflet Author
        $leeflet_author = $about_line[7];
        
        // Leeflet Author URL
        $leeflet_author_url = $about_line[10];
        
        // Leeflet Version
        $leeflet_version = $about_line[13];
    
        { ?>
        <li class="span3">
            <div class="thumbnail">
                <div class="screenshot-frame"></div>
                
                <img src="<?php if (file_exists($leeflet_screenshot)) { echo $leeflet_screenshot; } else { echo ADMIN_DIR . 'images/screenshot.jpg'; } ?>">
                
                <div class="caption">
                    <h3><?php if ($leeflet_name == '') : echo('Whoops!'); else : echo $leeflet_name; endif; ?> <span class="badge badge-inverse"><?php if ($leeflet_version == '') : echo('0.0'); else : echo $leeflet_version; endif; ?></span></h3>
                    
                    <h6>by: <a href="<?php echo $leeflet_author_url; ?>"><?php if ($leeflet_author == '') : echo('Who Knows'); else : echo $leeflet_author; endif; ?></a></h6>
                    
                    <p><?php if ($leeflet_description == '') : echo('This Leeflet is unfinished, broken or maybe it just did not install properly. Probably a good idea to re-install it.'); else : echo $leeflet_description; endif; ?></p>
                    
                    <p>
                        <?php if ($active_leeflet == $leeflet_dir_name) : ?>
                        <span class="btn btn-info disabled"><i class="icon-ok-circle icon-white"></i> Currently Active</span>
                        <?php else : ?>
                        <form id="activate-<?php echo $leeflet_dir_name; ?>" class="ajax-form" method="POST" action="save.php">
                            <input type="hidden" name="content_file" value="site_leeflet">
                            <input type="hidden" name="content" value="<?php echo $leeflet_dir_name; ?>">
                            <button type="submit" name="submit" value="submit" class="btn btn-info">Activate</button>
                        </form>
                        
                        <a class="btn" data-toggle="modal" href="#delete-<?php echo $leeflet_dir_name; ?>">Delete</a>
                        <?php endif; ?>
                    </p>
                    
                    <div class="modal fade hide" id="delete-<?php echo $leeflet_dir_name; ?>">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">×</button>
                            <h3>You Are About to Delete <?php echo $leeflet_name; ?></h3>
                        </div>
                        
                        <div class="modal-body">
                            <p>Are you absolutely sure you want to delete the <?php echo $leeflet_name; ?> leeflet? This will delete everything, including the leeflet and all your content associated with it.</p>
                        </div>
                        
                        <div class="modal-footer">
                            <a href="#" class="btn" data-dismiss="modal">Cancel</a>
                            <form id="delete-<?php echo $leeflet_dir_name; ?>" class="ajax-form" method="POST" action="actions/delete.php">
                                <input type="hidden" name="leeflet_path" value="<?php echo LEEFLETS_DIR . $leeflet_dir_name; ?>">
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
/* Display Premium Leeflets
/*-----------------------------------------------------------------------------------*/

//include(ADMIN_DIR . 'includes/feedcache.php');

function get_premium_leeflets($type = 'all') {

    //$feed_cache = new FeedCache('leeflets-'. $type .'.xml', 'http://leeflets.com/updates/leeflets-'. $type .'.xml');
    //$leeflets = simplexml_load_string($feed_cache->get_data());
    
    $leeflets = simplexml_load_file('http://leeflets.com/updates/leeflets-'. $type .'.xml');
    
    foreach ($leeflets as $leeflet):
        
        $leeflet_name=$leeflet->name;
        $leeflet_file_name=$leeflet->file_name;
        $leeflet_price=$leeflet->price;
        $leeflet_buy_url=$leeflet->buy_url;
        $leeflet_featured=$leeflet->featured;
        
        // The About File
        $about = 'http://leeflets.com/view/'. $leeflet_file_name .'/leeflets/'. $leeflet_file_name .'/about.txt';
        
        // About Array (Reading Line Numbers)
        $about_line = file($about);
        
        // Leeflet Description
        $leeflet_description = $about_line[4];
        
        // Leeflet Author
        $leeflet_author = $about_line[7];
        
        // Leeflet Author URL
        $leeflet_author_url = $about_line[10];
        
        // Leeflet Version
        $leeflet_version = $about_line[13];
        
        { ?>
        <li class="span3">
            <div class="thumbnail">
                <div class="screenshot-frame"></div>
                 
                <img src="http://leeflets.com/view/<?php echo $leeflet_file_name; ?>/leeflets/<?php echo $leeflet_file_name; ?>/screenshot.jpg" alt="<?php echo $leeflet_name; ?>">
                
                <div class="caption">
                    <h3><?php echo $leeflet_name; ?> <span class="badge badge-inverse"><?php echo $leeflet_version; ?></span></h3>
                    
                    <h6>by: <a href="<?php echo $leeflet_author_url; ?>"><?php echo $leeflet_author; ?></a></h6>
                    
                    <p><?php echo $leeflet_description; ?></p>
                    
                    <p><a class="btn btn-success" data-toggle="modal" href="#buy-<?php echo $leeflet_file_name; ?>">Purchase <?php echo $leeflet_price; ?></a> <a href="http://leeflets.com/view/<?php echo $leeflet_file_name; ?>/" class="btn" target="_blank">Live Demo</a></p>
                    
                    <div class="buy modal fade hide" id="buy-<?php echo $leeflet_file_name; ?>">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">×</button>
                            <h3>Purchase <?php echo $leeflet_name; ?></h3>
                        </div>
                        
                        <div class="modal-body">
                            <p>Click "Buy Now" below to continue to <strong>PayPal.com</strong> and complete your purchase. After your payment has successfully cleared (usually instant), you will receive purchase confirmation and a unique link via email which you will be able to use to download and install the <?php echo $leeflet_name; ?> Leeflet.</p>
                        </div>
                        
                        <div class="modal-footer">
                            <a href="#" class="btn" data-dismiss="modal">Close</a>
                            <a href="<?php echo $buy_url; ?>" class="btn btn-success">Buy Now <?php echo $leeflet_price; ?></a>
                        </div>
                    </div>
                </div>
            </div>
        </li>
        <?php } 
    
    endforeach;

}
					
?>