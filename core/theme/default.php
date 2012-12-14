<!doctype html>
<html lang="en">    
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    
    <title><?php $this->out( 'page-title' ); ?></title>
    
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <link rel="stylesheet" href="<?php echo $this->router->admin_url( '/core/theme/asset/css/style.css' ); ?>">
    <link rel="stylesheet" href="<?php echo $this->router->admin_url( '/core/third-party/redactor/css/redactor.css' ); ?>" />

    <script src="<?php echo $this->router->admin_url( '/core/theme/asset/js/jquery-1.8.2.min.js' ); ?>"></script>
    <script src="<?php echo $this->router->admin_url( '/core/third-party/redactor/redactor.js' ); ?>"></script>
    <script src="<?php echo $this->router->admin_url( '/core/theme/asset/js/script.js' ); ?>"></script>

    <link rel="shortcut icon" href="<?php echo $this->router->admin_url( '/core/theme/asset/img/favicon.png' ); ?>">
    
    <?php $this->out( 'head' ); ?>
</head>

<body>

<?php
$nav = array(
    array(
        'text' => 'Home',
        'atts' => array(
            'class' => 'home',
            'href' => $this->router->admin_url()
        )
    ),
    array(
        'text' => 'Content',
        'atts' => array(
            'class' => 'content',
            'href' => $this->router->admin_url( '/content/edit/' ),
            'container-name' => 'edit-content'
        )
    ),
    array(
        'text' => 'Settings',
        'atts' => array(
            'class' => 'settings',
            'href' => $this->router->admin_url( '/settings/edit/' ),
            'container-name' => 'edit-settings'
        )
    ),
    array(
        'text' => 'Logout',
        'atts' => array(
            'class' => 'logout',
            'href' => $this->router->admin_url( '/user/logout/' )
        )
    ),
    array(
        'text' => 'Publish',
        'atts' => array(
            'class' => 'publish',
            'href' => $this->router->admin_url( '/content/publish/' )
        )
    ),
    array(
        'text' => 'View',
        'atts' => array(
            'class' => 'view',
            'href' => $this->router->site_url(),
            'target' => '_blank'
        )
    ),
);

$nav = $this->hook->apply( 'primary_menu', $nav );
?>

<nav class="primary">
    <ul>
        <?php 
        foreach ( $nav as $n ) {
            $_atts = array();
            foreach ( $n['atts'] as $name => $value ) {
                $_atts[] = $name . '="' . $value . '"';
            }

            printf("<li><a %s>%s</a></li>", join( ' ', $_atts ), $n['text'] );
        }
        ?>
    </ul>
</nav>

<div class="clip">
    <div class="container">

        <?php $this->out( 'content' ); ?>

        <iframe src="<?php echo $this->router->admin_url( '/content/view/' ); ?>" class="viewer" width="100%"></iframe>

    </div>
</div>

<?php $this->out( 'foot' ); ?>

</body> 
</html>
