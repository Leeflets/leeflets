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
        <?php $this->part( 'body' ); ?>

        <?php $this->part( 'footer' ); ?>

        <?php $this->hook->apply( 'footer' ); ?>

        <?php
        list( $image_url, $width, $height ) = $this->get_image_atts( 'square@2x', 'page', 'photo' );
        
        if ( !$image_url ) {
            $image_url = $this->get_template_url( 'images/samueljacksonbeer-bg.jpg' );
        }
        ?>

        <script>
        (function($) {
            $(document).ready(function() {
                $.backstretch('<?php echo addslashes( $image_url ); ?>')
            })
        })(jQuery);
        </script>

    </body>
</html>
