<!doctype html>
<html lang="en" data-lf-edit="test-fields">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

        <!-- Site Meta -->
        <title><?php $this->setting( 'site-meta', 'title' ); ?></title>
        <meta name="description" content="<?php $this->setting( 'site-meta', 'description' ); ?>">
        <meta name="author" content="<?php $this->setting( 'site-meta', 'author' ); ?>">

        <!-- For Mobile Browsers -->
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- Current Template Icons -->
        <link rel="shortcut icon" href="<?php $this->template_url( 'images/favicon.png' ); ?>">

        <?php $this->hook->apply( 'head' ); ?>
    </head>

    <body>
        <div data-lf-edit="intro button" id="header">
            <div id="background" class="row">
                <div id="intro" class="row">
                    <div class="two-thirds">
                        <h1><?php $this->content( 'intro', 'title' ); ?></h1>
                        <p><?php $this->content( 'intro', 'paragraph' ); ?></p>
                        <a class="button" href="<?php $this->content( 'button', 'url' ); ?>"><?php $this->content( 'button', 'text' ); ?></a>
                    </div>
                </div>
            </div>
        </div>
        
        <div data-lf-edit="features" id="content">
            <div class="row centered">
                 <?php
                 $features = $this->get_content( 'features' );
                 if ( $features ) :
                     ?>
                 
                     <?php
                     $i = 0;
                     foreach ( $features as $feature ) :
                     $i++;
                         if ( !isset( $feature['text'] ) || !$feature['text'] ) return;
                         ?>
                         <div class="one-quarter">
                             <?php
                             if ( isset( $feature['icon'] ) && $icon_image = $this->get_image( 'icon@2x', $feature['icon'] ) ) {
                                echo $icon_image;
                             }
                             else {
                                printf( '<img src="%s" alt="" />', $this->get_template_url( 'images/icon_0'.$i.'@2x.png' ) );
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
                    <h3><?php $this->content( 'footer', 'copyright' ); ?></h3>
                </div>
                <div class="one-half">
                    <ul>
                        <?php $footer = $this->get_content( 'footer' ); ?>
                        
                        <?php if ( $footer['twitter'] !== '' ) { ?>
                        <li><a href="<?php $this->content( 'footer', 'twitter' ); ?>"><img src="<?php $this->template_url( 'images/twitter_btn@2x.png' ); ?>" /></a></li>
                        <?php } ?>
                        
                        <?php if ( $footer['facebook'] !== '' ) { ?>
                        <li><a href="<?php $this->content( 'footer', 'facebook' ); ?>"><img src="<?php $this->template_url( 'images/facebook_btn@2x.png' ); ?>" /></a></li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
        </div>

        <?php $this->hook->apply( 'footer' ); ?>
    </body>
</html>
