<?php
$movies = $this->get_content( 'movies' );
if ( $movies ) :
    ?>

    <ul>

    <?php
    foreach ( $movies as $movie ) :
        ?>

        <li><a href="<?php echo $movie['url']; ?>"><?php echo $movie['title']; ?></a></li>
    
        <?php 
    endforeach;
    ?>

    <ul>

    <?php
endif;
?>
