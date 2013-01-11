<?php
$movies = $this->get_content( 'movies' );
if ( $movies ) :
    ?>

    <ul>

    <?php
    foreach ( $movies as $movie ) :
        if ( !isset( $movie['show'] ) || !$movie['show'] ) return;
        ?>

        <li>
            <?php
            for ( $i = 1; $i <= $movie['stars']; $i++ ) {
                echo '&#9733;';
            }
            ?>
            <a href="<?php echo $movie['url']; ?>"><?php echo $movie['title']; ?></a>
        </li>
    
        <?php 
    endforeach;
    ?>

    <ul>

    <?php
endif;
?>
