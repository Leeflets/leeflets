<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

        <!-- Site Meta -->
        <title><?php $this->setting( 'title' ); ?></title>
        <meta name="description" content="<?php $this->setting( 'description' ); ?>">
        <meta name="author" content="<?php $this->setting( 'author' ); ?>">

        <!-- For Mobile Browsers -->
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- Current Template Styles -->
        <link rel="stylesheet" href="<?php $this->template_url( 'style.css' ); ?>">

        <!-- Current Template Icons -->
        <link rel="shortcut icon" href="<?php $this->template_url( 'images/favicon.png' ); ?>">

        <?php $this->part( 'head' ); ?>
    </head>

    <body>
        <?php $this->part( 'body' ); ?>

        <?php $this->part( 'footer' ); ?>

        <?php $this->settings( 'analytics' ); ?>
    </body>
</html>
