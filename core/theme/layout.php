<!doctype html>
<html lang="en">    
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        
        <title><?php $this->out( 'page_title' ); ?></title>
        
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <link rel="stylesheet" href="<?php echo $admin_url; ?>/core/theme/asset/css/style.css">

        <link rel="shortcut icon" href="<?php echo $admin_url; ?>/core/theme/asset/img/favicon.png">
        
        <?php $this->out( 'header' ); ?>
    </head>
    
    <body>
        <?php $this->out( 'content' ); ?>
        
        <?php $this->out( 'footer' ); ?>
    </body> 
</html>
