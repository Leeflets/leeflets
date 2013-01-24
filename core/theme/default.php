<!doctype html>
<html lang="en">    
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    
    <title><?php $this->out( 'page-title' ); ?></title>

    <link rel="apple-touch-icon" sizes="144x144" href="<?php echo $this->router->admin_url( '/core/theme/asset/img/touch-icons/144.png' ); ?>">
    <link rel="apple-touch-icon" sizes="114x114" href="<?php echo $this->router->admin_url( '/core/theme/asset/img/touch-icons/114.png' ); ?>">
    <link rel="apple-touch-icon" sizes="72x72" href="<?php echo $this->router->admin_url( '/core/theme/asset/img/touch-icons/72.png' ); ?>">
    <link rel="apple-touch-icon" href="<?php echo $this->router->admin_url( '/core/theme/asset/img/touch-icons/57.png' ); ?>">

    <link rel="shortcut icon" href="<?php echo $this->router->admin_url( 'favicon.ico' ); ?>" />
    <link rel="icon" href="<?php echo $this->router->admin_url( '/core/theme/asset/img/favicon/32.png' ); ?>" type="image/png" />

    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <?php $this->hook->apply( 'admin_head' ); ?>
</head>

<body>

<a class="show-primary-nav" href=""></a>

<nav class="primary-menu">
    <?php
    $nav = $this->get_primary_nav();
    foreach ( $nav as $n ) {
        $_atts = array();
        foreach ( $n['atts'] as $name => $value ) {
            $_atts[] = $name . '="' . $value . '"';
        }

        printf("<a %s>%s</a>", join( ' ', $_atts ), $n['text'] );
    }
    ?>
</nav>

<?php $this->out( 'content' ); ?>

<iframe src="<?php echo $this->router->admin_url( '/content/view/' ); ?>" class="viewer" width="100%"></iframe>

<?php $this->hook->apply( 'admin_footer' ); ?>

</body> 
</html>
