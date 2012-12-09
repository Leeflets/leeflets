<div id="words">        
    <div class="inside">
        <h2><?php $this->content( 'page', 'title' ); ?></h2>
        
        <?php echo $this->get_content( 'page', 'intro-paragraph' ); ?>

        <?php $this->part( 'movies' ); ?>
    
        <ul>
            <li>
                <a class="small black button left" href="<?php $this->content( 'left-button', 'url' ); ?>" title="<?php $this->content( 'left-button', 'text' ); ?>"><?php $this->content( 'left-button', 'text' ); ?></a>
                <a class="small charcoal button right" href="<?php $this->content( 'right-button', 'url' ); ?>" title="<?php $this->content( 'right-button', 'text' ); ?>"><?php $this->content( 'right-button', 'text' ); ?></a>
            </li>
        </ul>
    </div>
</div>
