<div id="words">        
    <div class="inside">
        <h2><?php get_content_part('content_title'); ?></h2>
        
        <p><?php get_content_part('content_text'); ?></p>
    
        <ul>
            <li>
                <a class="small dark-blue button left" href="<?php get_content_part('left_link'); ?>" title="<?php get_content_part('left_link_text'); ?>"><?php get_content_part('left_link_text'); ?></a>
                <a class="small blue button right" href="<?php get_content_part('right_link'); ?>" title="<?php get_content_part('left_link_text'); ?>"><?php get_content_part('right_link_text'); ?></a>
            </li>
        </ul>
    </div>
</div>