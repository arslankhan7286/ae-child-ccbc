<?php
/**
 * The template for displaying single staff page.
 */
$config = \Ardent\Ccbc\Sermons::getConfig();
global $wp_query, $paged;
$page = 1;
$page_number = 1;
$sermons_per_page = 9;
get_header();
?>
<?php 
    if ( function_exists( 'yoast_breadcrumb' ) ){ yoast_breadcrumb('<p id="breadcrumbs">','</p>'); } 
        if (have_posts()) {
            while (have_posts()) { the_post();
?>
<div class="sermon-media">
    <div class="container-wrap" style="padding-top:0 !important;">
        <div class="hero_section" style="background-image: url(<?php echo wp_get_attachment_url($config->header_image); ?>);
                            background-position: center;
                            background-repeat: no-repeat;
                            -webkit-background-size: cover;
                            -moz-background-size: cover;
                            -o-background-size: cover;
                            background-size: cover;">
            <div class="container">
            <?php $meta = new \Ardent\Wp\Meta; ?>
            <div class="video-container <?php if ($meta->youtube && $meta->mp3) {?>youtube-mp3<?php }?>">
                    <?php 
                            $pdfs = array_filter((array) json_decode($meta->pdfs));
                            $term = array_shift(get_the_terms(get_the_ID(), 'sermon_category'));
                            $image = \Ardent\Ccbc\Sermons\Term::getTermImage($term->term_id);
                            $poster; 
                            $posterimage = wp_get_attachment_image_src(($image?:$config->default_image), 'large');
                            $poster = ' poster="'.$posterimage[0].'"';
                            $related_query = new WP_Query(array(
                                'post_type' => 'sermon',
                                'posts_per_page' => 9,
                                'paged' => $page,
                                'orderby' => 'date&order=DESC',
                                'tax_query' => array(
                                    array(
                                        'taxonomy' => 'sermon_category',
                                        'field' => 'term_id',
                                        'terms' => $term->term_id
                                    )
                                )
                            ));
                            $related_args = array(
                                'post_type' => 'sermon',
                                'posts_per_page' => -1,
                                'fields' => 'ids',
                                'paged' => $page,
                                'no_found_rows' => true,
                                'tax_query' => array(
                                    array(
                                        'taxonomy' => 'sermon_category',
                                        'field' => 'term_id',
                                        'terms' => $term->term_id
                                    )
                                )
                            );
                            $page_number = $page;
                            $all_related = get_posts($related_args);
                                $pager = new \Ardent\Pager([
                                'page'   => $page,
                                'pages'  => ceil(count($all_related) / $sermons_per_page),
                                'format' => get_the_permalink() . '{{page}}/',
                                'max' => 5,
                                'next' => '<i class="fa fa-chevron-right" aria-hidden="true"></i>',
                                'prev' => '<i class="fa fa-chevron-left" aria-hidden="true"></i>'
                                    ]);
                            $sermon_query = new WP_Query(array(
                                'post_type' => 'sermon',
                                'posts_per_page' => -1,
                                'orderby' => 'publish_date',
                                'order' => 'ASC',
                                'tax_query' => array(
                                    array(
                                        'taxonomy' => 'sermon_category',
                                        'field' => 'term_id',
                                        'terms' => $term->term_id
                                    )
                                )
                            ));
                            $sermon_index = 1;
                            $current_id = get_the_ID();
                            $current_index = 0;
                            if ($sermon_query->have_posts()) {
                                while($sermon_query->have_posts()){ $sermon_query->the_post();
                                    if ($current_id == get_the_ID()) {
                                        $current_index = $sermon_index;
                                    }
                                    $sermon_index++;
                                }
                            } 
                            wp_reset_postdata();
                            $related_count = $sermon_query->post_count;
                            $sermon_count = $sermon_query->post_count;
                            ?>
                            <?php if($meta->video || $meta->video_url || $meta->vimeo || $meta->mp3 || $meta->youtube){ ?>
                            <div class="videoarea <?php if ($meta->vimeo) { echo 'iframe-true'; } else { echo 'iframe-false'; }?>">
                                <div class="video-overlay">
                                    <a href="#" class="reset-btn"><i class="fa fa-angle-left"></i></a>
                                </div>
                                <?php if ($meta->video || $meta->vimeo || $meta->youtube) { ?>
                                <?php 
                                    $video = '';
                                    if ($meta->video) {
                                        $video = \Ardent\Html::get(array('type' => 'truthcasting.player', 'mediaid' => $meta->video));
                                    } else if ($meta->vimeo) {
                                        $video = \Ardent\Html::get(array('type' => 'iframe', 'src' => '//player.vimeo.com/video/' . $meta->vimeo, 'frameborder' => 0, 'webkitallowfullscreen' => true, 'mozallowfullscreen' => true, 'allowfullscreen' => true));
                                    }else if ($meta->youtube) {
                                        $video = \Ardent\Html::get(array('type' => 'iframe', 'src' => 'https://www.youtube.com/embed/' . $meta->youtube . '/?ref=0&rel=0', 'frameborder' => 0, 'webkitallowfullscreen' => true, 'mozallowfullscreen' => true, 'allowfullscreen' => true));;
                                    }
                                    echo $video;
                                ?>
                                <?php } ?>
                                <?php if ($meta->video_url || $meta->mp3) { ?>
                                <video class="sermonvideotag" id="sermon-video" width="100%" style="background-image: url(<?php echo $posterimage[0]; ?>); background-size: cover; background-repeat: no-repeat;" controls>
                                    <source <?php if ($meta->video_url && !$meta->youtube) {?> data-video-src="<?php echo $meta->video_url;?>" <?php } ?> <?php if ($meta->mp3) {?>data-mp3-source="<?php echo $meta->mp3; ?>" <?php } ?>src="" type="video/mp4"/>
                                </video>
                                <div class="video-cover" style="background-image: url(<?php echo $posterimage[0]; ?>);
                                    background-position: center;
                                    background-repeat: no-repeat;
                                    -webkit-background-size: cover;
                                    -moz-background-size: cover;
                                    -o-background-size: cover;
                                    background-size: cover;">
                                    <div class="video-controls">
                                        <div class="series-container"><a href="<?php echo get_term_link($term); ?>" class="series-btn"><img src="<?php echo $posterimage[0]; ?>" alt="<?php echo get_the_title();?>"></a></div>
                                        <p>Series <?php echo $current_index; ?> of <?php echo $sermon_count; ?> </p>
                                        <div class="controls-wrapper">
                                            <?php if ($meta->video_url && !$meta->youtube) {?>
                                            <a href="#" alt="Play" class="play-btn"> <i class="fa fa-play-circle"></i> </a>
                                            <?php } ?>
                                            <?php if ($meta->vimeo) {?>
                                            <a href="#" alt="Play" class="vimeo-play-btn"> <i class="fa fa-play-circle"></i> </a>
                                            <?php } ?>
                                            <?php if ($meta->youtube) {?>
                                            <a href="#" alt="Play" class="youtube-play-btn"> <i class="fa fa-play-circle"></i> </a>
                                            <?php } ?>
                                            <?php if ($meta->mp3) {?>
                                            <a href="#" alt="Listen" class="listen-btn"> <i class="fa fa-headphones"></i> </a>
                                            <?php } ?>
                                            <?php if ($meta->video_url || $meta->mp3) {?>
                                            <a href="<?php if ($meta->video_url) { echo $meta->video_url; } else { echo $meta->mp3; }?>" download alt="Download" class="download-btn"> <i class="fa fa-download"></i> </a>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                                <?php } ?>
                            </div>
                            <?php 
                            } else {?>
                            <div class="no-media">
                                <p>Series <?php echo $current_index; ?> of <?php echo $sermon_count; ?> </p>
                                 <div class="series-container"><a href="<?php echo get_term_link($term); ?>" class="series-btn"><img src="<?php echo $posterimage[0]; ?>" alt="<?php echo get_the_title();?>"></a></div>
                            <div class="post-img" style="background-image: url(<?php echo $posterimage[0]; ?>);
                                 background-position: center;
                                 background-repeat: no-repeat;
                                 -webkit-background-size: cover;
                                 -moz-background-size: cover;
                                 -o-background-size: cover;
                                 background-size: cover;"></div>
                            </div>
                            <?php
                            } 
                            ?>
                            <div class="title-wrapper">
                                <h2><?php echo get_the_title();?></h2>
                                <h4><?php echo get_the_title($meta->speaker).($meta->speaker?'   |   ':'').get_the_time('F d', get_the_ID()); ?></h4>
                            </div>
                </div>
            </div>
        </div>
        <div class="sermon-ctas">
            <?php if($related_query->have_posts() && $related_count > 1){?>
            <a href="#series-list" <?php if (count($pdfs) == 0) { ?> class="full-width" <?php } ?>><i class="fa fa-ellipsis-h"></i> More From This Series</a><?php } ?><?php foreach ($pdfs as $pdf) { ?><a <?php if($related_count == 1){?>class="full-width" <?php } ?>href="<?php echo $pdf->url; ?>" download="<?php echo $pdf->title?:'Notes'; ?>"><i class="fa fa-file"></i> <?php echo $pdf->title?:'Notes'; ?></a>
            <?php } ?>
        </div>
        <div class="container main-content">
            <div class="row">
                <div class="sermon-about">
                    <div class="social-media">
                        <h3>Share</h3>
                        <ul class="social-list">
                                <li>
                                    <a href="https://twitter.com/intent/tweet?url=<?php the_permalink(); ?>&text=<?php the_title(); ?>&via=<?php echo home_url(); ?>&hashtags=#<?php echo str_replace(' ', '', $meta->references)?>" target="_blank">
                                        <i class="fab fa-twitter"></i> 
                                    </a>
                                </li>
                                <li>
                                    <a href="https://www.facebook.com/sharer.php?u=<?php the_permalink(); ?>" target="_blank">
                                        <i class="fab fa-facebook"></i>
                                    </a>
                                </li>
                        </ul>
                    </div>
                    <?php 
                        if ($meta->description) {
                    ?>
                    <div class="description">
                        <p><?php echo $meta->description; ?></p>
                    </div>
                    <?php } ?>
                    <?php 
                        if ($meta->speaker) {
                            $smeta = new \Ardent\Wp\Meta($meta->speaker);
                            if ($smeta->bio) {
                    ?>
                    <div class="speaker-biography">
                        <?php if ($smeta->image) {?>
                        <div class="speaker-image">
                            <img src="<?php echo wp_get_attachment_url($smeta->image); ?>" alt="<?php echo get_the_title($meta->speaker); ?>">
                        </div>
                        <?php } ?>
                        <div class="speaker-bio">
                            <h4>About <?php echo get_the_title($meta->speaker); ?></h4>
                            <p><?php echo $smeta->bio; ?></p>
                            <?php if ($smeta->website) {?>
                            <a class="website-link" href="<?php echo $smeta->website; ?>" alt="<?php echo get_the_title($meta->speaker); ?>"><i class="fa fa-rss"></i> Website</a>
                            <?php } ?>
                        </div>
                    </div>
                        <?php } ?>
                    <?php } ?>
                </div>
                <?php 
                    if($related_query->have_posts() && $related_count > 1){
                ?>
                <div class="series-list" id="series-list">
                    <h6>More sermons in this series</h6>
                    <div class="row">
                    <?php 
                        while($related_query->have_posts()){ $related_query->the_post();
                        $rmeta = new \Ardent\Wp\Meta;
                        $rterm = array_shift(get_the_terms(get_the_ID(), 'sermon_category'));
                        $rimage = \Ardent\Ccbc\Sermons\Term::getTermImage($rterm->term_id) ?: $config->default_image;
                        $hidden = '';
                        if ($current_id == get_the_ID()) {
                            $hidden = 'style="display: none;"';
                        }
                        $index_num = $related_count;
 
                        if ($page_number != 1) {
                            $index_num = $related_count - (($page_number - 1) * 9);
                        } 
                        
                    ?>
                    <div class="single-sermon tallest" <?php echo $hidden; ?>>
                        <a href="<?php the_permalink(); ?>" alt="<?php echo get_the_title(); ?>">
                            <div class="sermon-image" style="background-image: url(<?php echo wp_get_attachment_url($rimage); ?>);
                            background-position: center;
                            background-repeat: no-repeat;
                            -webkit-background-size: cover;
                            -moz-background-size: cover;
                            -o-background-size: cover;
                            background-size: cover;"><p><?php echo $index_num; ?></p></div>
                            <div class="single-sermon-copy">
                                <h4><?php echo get_the_title($meta->speaker).($meta->speaker?'   |   ':'').get_the_time('F d', get_the_ID()); ?></h4>
                                <h3><?php echo get_the_title(); ?></h3>
                            </div>
                        </a>
                    </div>
                    <?php $related_count--; } ?>
                </div>
                <?php } ?>
                
                <div class="pagination">
                    <?php echo implode(PHP_EOL, $pager->getLinks()); ?>
                </div>
              </div>
            </div><!--/row-->
        </div><!--/container-->
    </div><!--/container-wrap-->
</div>
<?php 
    }
} wp_reset_postdata(); 
?>	
<?php get_footer();