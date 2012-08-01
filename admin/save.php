<?php 

/*-----------------------------------------------------------------------------------*/
/* Save Submitted Content
/*-----------------------------------------------------------------------------------*/

if($_POST['submit'] == "submit") 
{
    // Get Stuff
    $content = $_POST['content'];
    $content_file = $_POST['content_file'];
   
    // Save Stuff
    file_put_contents('../content/'. $content_file .'.txt',  stripslashes($content));
}

?>