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

<nav class="primary">
    <ul>
        <li><a class="home" href="<?php echo $this->router->admin_url(); ?>">Home</a></li>
        <li><a class="content" href="<?php echo $this->router->admin_url( '/content/edit/' ); ?>">Content</a></li>
        <li><a class="settings" href="<?php echo $this->router->admin_url( '/settings/edit/' ); ?>">Settings</a></li>
        <li><a class="logout" href="<?php echo $this->router->admin_url( '/user/logout/' ); ?>">Logout</a></li>
        <li><a class="publish" href="<?php echo $this->router->admin_url( '/content/publish/' ); ?>">Publish</a></li>
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
