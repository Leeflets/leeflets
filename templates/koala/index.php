<!doctype html>
<html lang="en" data-lf-edit="test-fields">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

        <!-- Site Meta -->
        <title><?php $settings->out( 'site-meta', 'title' ); ?></title>
        <meta name="description" content="<?php $settings->out( 'site-meta', 'description' ); ?>">
        <meta name="author" content="<?php $settings->out( 'site-meta', 'author' ); ?>">

        <!-- For Mobile Browsers -->
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- Current Template Icons -->
        <link rel="shortcut icon" href="<?php $template->url( 'images/favicon.png' ); ?>">

        <?php $hook->apply( 'head' ); ?>
    </head>

    <body>
        <div data-lf-edit="intro button" id="header">
            <div id="background" class="row">
                <div id="intro" class="row">
                    <div class="two-thirds">
                        <h1><?php $content->out( 'intro', 'title' ); ?></h1>
                        <p><?php $content->out( 'intro', 'paragraph' ); ?></p>
                        <a class="button" href="<?php $content->out( 'button', 'url' ); ?>"><?php $content->out( 'button', 'text' ); ?></a>
                    </div>
                </div>
            </div>
        </div>
        
        <div data-lf-edit="features" id="content">
            <div class="row centered">
                 <?php
                 $features = $content->get( 'features' );
                 if ( $features ) :
                     ?>
                 
                     <?php
                     foreach ( $features as $feature ) :
                         if ( !isset( $feature['text'] ) || !$feature['text'] ) continue;
                         ?>
                         <div class="one-quarter">
                             <?php
                             if ( isset( $feature['icon'] ) && $icon_image = $image->get( 'icon@2x', $feature['icon'] ) ) {
                                echo $icon_image;
                             }
                             ?>

                             <h2><?php echo $feature['title']; ?></h2>
                             <p><?php echo $feature['text']; ?></p>
                         </div>
                         <?php 
                     endforeach;
                     ?>
                 
                     <?php
                 endif;
                 ?>
            </div>
        </div>
        
        <div data-lf-edit="footer" id="footer">
            <div class="row">
                <div class="one-half">
                    <h3><?php $content->out( 'footer', 'copyright' ); ?></h3>
                </div>
                <div class="one-half">
                    <ul>
                        <?php $footer = $content->get( 'footer' ); ?>
                        
                        <?php if ( $footer['twitter'] !== '' ) { ?>
                        <li><a href="<?php $content->out( 'footer', 'twitter' ); ?>"><img src="<?php $template->url( 'images/twitter_btn@2x.png' ); ?>" /></a></li>
                        <?php } ?>
                        
                        <?php if ( $footer['facebook'] !== '' ) { ?>
                        <li><a href="<?php $content->out( 'footer', 'facebook' ); ?>"><img src="<?php $template->url( 'images/facebook_btn@2x.png' ); ?>" /></a></li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
        </div>

        <?php $hook->apply( 'footer' ); ?>
    </body>
</html>
