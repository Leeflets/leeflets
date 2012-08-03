<?php 

/*-----------------------------------------------------------------------------------*/
/* Save Submitted Content
/*-----------------------------------------------------------------------------------*/

if($_POST['submit'] == "submit") 
{
    // Get Stuff
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    // Output Stuff
    $config[] = "<?php";
    $config[] = "\$username = '$username';";
    $config[] = "\$password = '$password';";
    
    // Put Stuff
    file_put_contents("../config.php", implode("\n", $config));
    
    // Redirect
    header('Location: ' . '../admin/');
}

?>