<?php
$config = \Ardent\Ccbc\Sermons::getConfig();
$pterm = get_the_terms(get_the_ID(), 'sermon_category');
$image = \Ardent\Ccbc\Sermons\Term::getTermImage($pterm[0]->term_id);
$meta = new \Ardent\Wp\Meta(get_the_ID());
$posterimage = wp_get_attachment_image_src(($image ?: $config->default_image), 'large');

?>
<div class="single-post">
    <a href="<?php the_permalink(); ?>">
        <div class="post-thumbnail">
            <img src="<?php echo $posterimage[0]; ?>" alt="<?php the_title(); ?>" />
        </div>
        <div class="post-text">
            <h4><?php the_title(); ?></h4>
            <p><span><?php echo get_the_time('F d Y', get_the_ID()); ?></span> | <span><?php echo get_the_title($meta->speaker); ?></span></p>
        </div>
    </a>
</div>