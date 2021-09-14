<?php
add_action('tribe_events_list_widget_after_the_event_title', function(){ ?>
    <div class="tribe-event-excerpt"><?php the_excerpt(); ?></div>
    <a class="fill_link" href="<?php the_permalink(); ?>"></a>
<?php });