<!doctype html>
<html lang="en">    
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        
        <title><?php $this->out( 'page-title' ); ?></title>
        
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <link rel="stylesheet" href="<?php echo $this->router->admin_url( '/core/theme/asset/css/style.css' ); ?>">

        <link rel="shortcut icon" href="<?php echo $this->router->admin_url( '/core/theme/asset/img/favicon.png' ); ?>">
        
        <?php $this->out( 'head' ); ?>
    </head>
    
    <body>
        <p class="home-link"><a href="<?php echo $this->router->admin_url(); ?>">Home</a></p>

        <?php $this->out( 'content' ); ?>

        <?php $this->out( 'foot' ); ?>
    </body> 
</html>
