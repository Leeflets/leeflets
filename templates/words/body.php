<div id="words">        
    <div class="inside">
        <h2><?php $this->content( 'page-title' ); ?></h2>
        
        <p><?php echo nl2br( $this->get_content( 'intro-paragraph' ) ); ?></p>
    
        <ul>
            <li>
                <a class="small black button left" href="<?php $this->content( 'left-button-url' ); ?>" title="<?php $this->content( 'left-button-text' ); ?>"><?php $this->content( 'left-button-text' ); ?></a>
                <a class="small charcoal button right" href="<?php $this->content( 'right-button-url' ); ?>" title="<?php $this->content( 'right-button-text' ); ?>"><?php $this->content( 'right-button-text' ); ?></a>
            </li>
        </ul>
    </div>
</div>
