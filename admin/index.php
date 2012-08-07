<?php session_start();

/*-----------------------------------------------------------------------------------*/
/* Get Reqiured Leeflets Functions
/*-----------------------------------------------------------------------------------*/

include('functions.php');

/*-----------------------------------------------------------------------------------*/
/* Then Build the Admin
/*-----------------------------------------------------------------------------------*/

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    
    <!-- Site Title -->
    <title>Leeflets Admin</title>
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="includes/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="includes/bootstrap/css/bootstrap-responsive.min.css">
    
    <!-- Additional CSS -->
    <link rel="stylesheet" href="style.css">
    
    <!-- Favicon -->
    <link rel="shortcut icon" href="images/favicon.png">
</head>

<body class="leeflets-admin<?php if (!isset($_SESSION['user'])) { ?> welcome<?php } ?>">
    <div class="progress progress-striped active save-loading hide">
        <div class="bar" style="width: 100%;"></div>
    </div>
    
    <div class="alert alert-success hide">
        <strong>Well done!</strong> Your settings have been saved.
    </div>
    
    <div class="navbar navbar-fixed-top">
        <div class="navbar-inner">
            <div class="container-fluid">
                <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </a>
                
                <a class="brand" href="http://leeflets.com" target="_blank">Leeflets v<?php get_leeflets_version(); ?></a>
                
                <?php if (!isset($_SESSION['user'])) { ?>
                <!-- Don't Show Menus Unless the User is Logged In -->
                <?php } else { ?>
                <div class="nav-collapse">
                    <ul class="nav pull-left">
                        <li class="active"><a href="#dashboard" data-toggle="tab"><i class="icon-home icon-white"></i> Dashboard</a></li>
                        <li><a href="#marketplace" data-toggle="tab"><i class="icon-shopping-cart icon-white"></i> Marketplace</a></li>
                        <li><a href="#template-select" data-toggle="tab"><i class="icon-eye-open icon-white"></i> Design</a></li>
                        <li><a href="#site-settings" data-toggle="tab"><i class="icon-cog icon-white"></i> Settings</a></li>
                        <li><a href="#site-content" data-toggle="tab"><i class="icon-align-left icon-white"></i> Content</a></li>
                        <li><a href="#leeflets-support" data-toggle="tab"><i class="icon-question-sign icon-white"></i> Support</a></li>
                        <li><a href="<?php get_home_url(); ?>"><i class="icon-leaf icon-white"></i> View Site</a></li>
                        <li><a class="logout" href="?action=logout"><i class="icon-off icon-white"></i> Logout</a></li>
                    </ul>
                </div>
                <?php } ?>
            </div>
        </div>
    </div> <!-- .navbar -->

    <div class="container-fluid">

<?php 

/*-----------------------------------------------------------------------------------*/
/* If Logged Out, Get the Login Panel
/*-----------------------------------------------------------------------------------*/
    
if (!isset($_SESSION['user'])) { 
    
?>   
        <?php if (file_exists('../config.php')) { ?>
            
            <div class="row-fluid">
                <div class="page-header">
                    <h1>Welcome Back! <small>Enter your username and password to login.</small></h1>
                </div>
                
                <form method="POST" action="?action=login">
                    <fieldset>
                        <div class="clearfix">
                            <?php if(isset($login_error)): ?>
                            <p class="alert alert-error"><?php echo $login_error; ?></p>
                            <?php endif; ?>
                        </div>
                        <div class="clearfix">
                            <label for="username"><i class="icon-user icon-black"></i> User Name:</label>
                            <div class="input">
                                <input type="text" name="username" id="username">
                            </div>
                        </div>
                        <div class="clearfix">
                            <label for="password"><i class="icon-lock icon-black"></i> Password:</label>
                            <div class="input">
                                <input type="password" name="password" id="password">
                            </div>
                        </div>
                        
                        <span class="help-block"><strong>Note: </strong>Forgot your password? Delete "config.php" and then reload this page to reset your password.</span>
                        
                        <hr>
                        
                        <div class="actions">
                            <input type="submit" class="btn btn-info" value="Login">
                            <a href="<?php get_home_url(); ?>" class="btn">Cancel</a>
                        </div>
                    </fieldset>
                </form>
            </div>
        
        <?php } else { ?>
            
            <div class="row-fluid">
                <div class="hero-unit">
                    <h1>Welcome to Leeflets!</h1>
                    
                    <p>Congrats... you're already <strong>99% finished</strong> installing Leeflets. Pretty easy right? All you need to do now is set your desired <strong>username</strong> and <strong>password</strong> and we're done.</p>
                
                    <p class="hero-nav">
                        <a data-toggle="modal" href="#set-user" class="btn btn-info btn-large">Set Username &amp; Password</a> 
                        <a href="<?php get_home_url(); ?>" class="btn btn-large">Cancel</a>
                    </p>
                </div>
                
                <div class="modal fade hide" id="set-user">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">×</button>
                        <h3>Set Your Username &amp; Password</h3>
                    </div>
                    
                    <form method="POST" action="save-user.php">
                        <fieldset>
                            <div class="modal-body">
                                <p><strong>Note: </strong>After your password has been set you will be redirected to the Leeflets admin login page where you will prompted to enter your selected <strong>username</strong> and <strong>password</strong>. You will be able to change your username and password at any time within the Leeflets admin.</p>
                                
                                <div class="clearfix">
                                    <label for="username"><i class="icon-user icon-black"></i> Choose a Username:</label>
                                    <div class="input">
                                        <input type="text" name="username" id="username">
                                    </div>
                                </div>
                                <div class="clearfix">
                                    <label for="password"><i class="icon-lock icon-black"></i> Choose a Password:</label>
                                    <div class="input">
                                        <input type="password" name="password" id="password">
                                    </div>
                                </div>   
                            </div>
                            
                            <div class="modal-footer">
                                <button type="submit" name="submit" value="submit" class="btn btn-info">Continue</button>
                                <a href="#" class="btn" data-dismiss="modal">Cancel</a>
                            </div>
                        </fieldset>
                    </form>
                </div>
            </div>
        
        <?php } ?>    
<?php 

} else { 

/*-----------------------------------------------------------------------------------*/
/* Else, If Logged In, Get The Admin Panels
/*-----------------------------------------------------------------------------------*/

?> 

        <div class="tab-content">
             <section class="tab-pane fade in active accordion" id="dashboard">
                <div class="hero-unit">
                    <h1>Welcome Back!</h1>
                    
                    <p>To manage your site content, appearance or settings, choose from the menu above. If you're looking for something else... the links below might be helpful.</p>
                    
                    <p class="hero-nav">
                        <a class="btn btn-info btn-large">View Your Site</a> 
                        <a class="btn btn-warning btn-large">Get Support</a>
                        <a class="btn btn-success btn-large" href="#marketplace" data-toggle="tab">Purchase New Templates</a>
                    </p>
                </div>
                
                <div class="accordion-group">
                    <div class="page-header extra-margin">
                        <h1><a data-toggle="collapse" data-parent="#dashboard" href="#featured-templates">Latest Featured Templates <small>Here are some great new templates available for purchase.</small></a></h1>
                    </div> 
                    
                    <div id="featured-templates" class="collapse in">
                        <ul class="thumbnails">
                            <?php get_premium_templates('featured'); // Get Featured Templates ?>
                        </ul>
                    </div>
                </div>
                
                <div class="accordion-group">
                    <div class="page-header">
                        <h1><a data-toggle="collapse" data-parent="#dashboard" href="#leeflets-news">Latest Leeflets News! <small>This is important stuff, so read carefully!</small></a></h1>
                    </div>
                    
                    <div id="leeflets-news" class="collapse in">
                        <?php get_latest_tweets('3'); // Get Latest News ?>
                        <script src="//platform.twitter.com/widgets.js" charset="utf-8"></script>
                    </div>
                </div>
            </section>
            
            <section class="tab-pane fade accordion" id="marketplace">
                <div class="hero-unit">
                    <h1>The Leeflets Marketplace</h1>
                    
                    <p>Browse our growing marketplace of amazing Leeflets. If you find something you like, buy it, install it and have your new site online in under 5 minutes. Yeah... it's that simple.</p>
                    
                    <p class="hero-nav">
                        <a class="btn btn-info btn-large" data-toggle="collapse" data-parent="#marketplace" href="#get-featured-templates">What's New</a> 
                        <a class="btn btn-warning btn-large" data-toggle="collapse" data-parent="#marketplace" href="#get-popular-templates">Templates</a> 
                        <a class="btn btn-success btn-large" data-toggle="collapse" data-parent="#marketplace" href="#get-all-templates">Blocks</a> 
                    </p>
                </div>
                
                <div class="accordion-group">
                    <div class="page-header extra-margin">
                        <h1><a data-toggle="collapse" data-parent="#marketplace" href="#get-featured-templates">New and Noteworthy <small>Here are some great new templates.</small></a></h1>
                    </div> 
                    
                    <div id="get-featured-templates" class="collapse in">
                        <ul class="thumbnails">    
                            <?php get_premium_templates('featured'); // Get Featured Templates ?>
                        </ul>
                    </div>
                </div>
                
                <div class="accordion-group">    
                    <div class="page-header extra-margin">
                        <h1><a data-toggle="collapse" data-parent="#marketplace" href="#get-popular-templates">What's Hot <small>The most popular templates.</small></a></h1>
                    </div> 
                    
                    <div id="get-popular-templates" class="collapse">
                        <ul class="thumbnails">    
                            <?php get_premium_templates('popular'); // Get Popular Templates ?>
                        </ul>
                    </div>
                </div>
                
                <div class="accordion-group">      
                    <div class="page-header extra-margin">
                        <h1><a data-toggle="collapse" data-parent="#marketplace" href="#get-all-templates">All Templates <small>Everything under the sun.</small></a></h1>
                    </div> 
                    
                    <div id="get-all-templates" class="collapse">
                        <ul class="thumbnails">    
                            <?php get_premium_templates('all'); // Get All Templates ?>
                        </ul>
                    </div>
                </div>
            </section>
            
            <section class="tab-pane fade" id="template-select">
                <div class="hero-unit">
                    <h1>Choose Wisely</h1>
                    
                    <p>Below is a list of templates that are currently installed and available for you to use. Click <strong>Activate</strong> to use a leeflet or <strong>Delete</strong> to delete a leeflet. If you don't see anything you like, you can purchase new templates below as well.</p>
                    
                    <p class="hero-nav">
                        <a class="btn btn-info btn-large refresh">Install New Templates</a> 
                        <a class="btn btn-success btn-large" href="#marketplace" data-toggle="tab">Purchase New Templates</a>
                    </p>
                </div>
                
                <div class="page-header extra-margin">
                    <h1>Currently Installed Templates <small>Choose the template you would like to use for this site.</small></h1>
                </div> 
                
                <ul class="thumbnails">
                    <?php get_available_templates(); // Get Installed Templates ?>
                </ul>
            </section>
            
            <section class="tab-pane fade accordion" id="site-settings">
                <div class="hero-unit">
                    <h1>Your Site Settings</h1>
                    
                    <p>Use the options below to configure your <strong>site preferences</strong> including your <strong>site title</strong>, <strong>meta information</strong>, <strong>social media</strong> and much more. Some of these settings are essential for generating good search results, so read carefully and choose your words wisely.</p>
                </div>
                
                <?php get_options('settings'); // Get Settings Options ?>
            </section>
            
            <section class="tab-pane fade accordion" id="site-content">
                <div class="hero-unit">
                    <h1>Your Site Content</h1>
                    
                    <p>Use the options below to configure your actual <strong>site content</strong>. Keep in mind that these options will change depending on the currently active Leeflet. No worries... changing Leeflets will not delete your saved content for the active Leeflet.</p>
                
                    <p class="hero-nav">
                        <a class="btn btn-info btn-large">Change Templates</a> 
                        <a class="btn btn-success btn-large" href="#marketplace" data-toggle="tab">Purchase New Templates</a>
                    </p>
                </div>
                
                <?php get_options('content'); // Get Settings Options ?>
            </section>  
            
            <section class="tab-pane fade accordion" id="leeflets-support">
                <div class="hero-unit">
                    <h1>Need Some Help?</h1>
                    
                    <p>No problem, there's a few different ways to get your questions answered. First and foremost, we recommend that you browse the Leeflets <strong>FAQ</strong> and <strong>video tutorials</strong> below. If you still aren't finding the answers you need, just login to the <strong>support forums</strong> by clicking the link below.</p>
                
                    <p class="hero-nav">
                        <a class="btn btn-info btn-large">Take Me to the Support Forums</a>
                    </p>
                </div>
                
                <div class="accordion-group">      
                    <div class="page-header">
                        <h1><a data-toggle="collapse" data-parent="#leeflets-support" href="#saving-content">Saving Settings &amp; Content <small>My content won't save... how do I fix that?</small></a></h1>
                    </div> 
                    
                    <div id="saving-content" class="collapse">                        
                        <div class="well">
                        <p>In some cases, depending on your server configuration, saving site content and settings can be a problem. Just follow the steps below for the typical fix.</p>
                        <ol>
                            <li>Lorem ipsum dolor sit amet</li>
                            <li>Consectetur adipiscing elit</li>
                            <li>Integer molestie lorem at massa</li>
                            <li>Facilisis in pretium nisl aliquet</li>
                            <li>Nulla volutpat aliquam velit</li>
                            <li>Faucibus porta lacus fringilla vel</li>
                            <li>Aenean sit amet erat nunc</li>
                            <li>Eget porttitor lorem</li>
                        </ol>
                        </div>
                    </div>
                </div>
            </section>             
        </div> <!-- tab-content -->
        
<?php 

}

/*-----------------------------------------------------------------------------------*/
/* Then Get the Footer for Both the Login Panel & Admin Panels
/*-----------------------------------------------------------------------------------*/ 

?> 

        <!--<hr>

        <footer>
            <p class="alert alert-info">Powered by Leeflets&#0153; v<?php get_leeflets_version(); ?> | &copy; <?php echo date("Y"); ?> Circa75 Media, LLC</p>
        </footer>-->
    </div> <!-- .container-fluid -->
    
    <!-- Latest jQuery -->
    <script src="includes/jquery.js"></script>
    
    <!-- Bootstrap Scripts -->
    <script src="includes/bootstrap/js/bootstrap.min.js"></script>
    <script type="text/javascript">
        $(function() {
            $('.modal').modal({
                backdrop: true;
            });
            
            $(".collapse").collapse();
        });
    </script>
    
    <!-- Ajax Forms -->
    <script src="includes/jquery.form.js"></script>
    <script> 
        $(document).ready(function() { 
            var options = { 
            
                // Rebinding Everything
                delegation: true,
                
                // The Save 
                success: function() { 
                    // Start Transitions
                    $(".tab-content").fadeTo(500, 0.00);
                    $(".save-loading").fadeIn(500).delay(1500).fadeOut(1500);
                    
                    // Refresh Content
                    $("#template-select").load(location.href+" #template-select>*","");
                    $("#site-content").load(location.href+" #site-content>*","");
                    
                    // End Transitions
                    $(".tab-content").delay(1500).fadeTo(2000, 1.0);
                    $(".alert-success").delay(3000).fadeIn(500).delay(1500).fadeOut(1500); 
                } 
            }; 
             
            // Pass options to ajaxForm 
            $('.ajax-form').ajaxForm(options);
        }); 
    </script>
    
    <!-- WYSIHTML5 Editor for Bootstrap -->
    <link rel="stylesheet" type="text/css" href="includes/wysihtml5.css"></link>
    <script src="includes/wysihtml5.js"></script>
    <script src="includes/bootstrap/js/bootstrap-wysihtml5.js"></script>
    <script type="text/javascript">
    	$('.fancy').wysihtml5();
    </script>
</body>
</html>