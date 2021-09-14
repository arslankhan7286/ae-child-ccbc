<?php
$meta = new \Ardent\Wp\Meta(get_the_ID());
?>
<div class="flex-row">
    <div class="latest_sermon video-container <?php if ($meta->youtube && $meta->mp3) { ?>youtube-mp3<?php } ?>">
        <?php
        $poster;
        $pdfs = array_filter((array) json_decode($meta->pdfs));
        $term = get_the_terms(get_the_ID(), 'sermon_category');
        $image = \Ardent\Ccbc\Sermons\Term::getTermImage($term[0]->term_id);
        $posterimage = wp_get_attachment_image_src(($image ?: $config->default_image), 'large');
        $poster = ' poster="' . $posterimage[0] . '"';
        if ($meta->video || $meta->video_url || $meta->vimeo || $meta->mp3 || $meta->youtube) {
        ?>
            <div class="videoarea <?php if ($meta->vimeo) {
                                        echo 'iframe-true';
                                    } else {
                                        echo 'iframe-false';
                                    } ?>">
                <div class="video-overlay">
                    <a href="#" class="reset-btn"><i class="fa fa-angle-left"></i></a>
                </div>
                <?php if ($meta->video || $meta->vimeo || $meta->youtube) { ?>
                    <?php
                    $video = '';
                    if ($meta->video && !$meta->youtube) {
                        $video = \Ardent\Html::get(array('type' => 'truthcasting.player', 'mediaid' => $meta->video));
                    } else if ($meta->vimeo) {
                        $video = \Ardent\Html::get(array('type' => 'iframe', 'src' => '//player.vimeo.com/video/' . $meta->vimeo, 'frameborder' => 0, 'webkitallowfullscreen' => true, 'mozallowfullscreen' => true, 'allowfullscreen' => true));
                    } else if ($meta->youtube) {
                        $video = \Ardent\Html::get(array('type' => 'iframe', 'src' => 'https://www.youtube.com/embed/' . $meta->youtube . '/?ref=0&rel=0', 'frameborder' => 0, 'webkitallowfullscreen' => true, 'mozallowfullscreen' => true, 'allowfullscreen' => true));;
                    }
                    echo $video;
                    ?>
                <?php } ?>
                <?php
                if ($meta->video_url || $meta->mp3) {
                ?>
                    <video class="sermonvideotag" id="sermon-video" width="100%" controls style="background-image: url(<?php echo $posterimage[0]; ?>); background-size: cover; background-repeat: no-repeat;">
                        <source <?php if ($meta->video_url && !$meta->youtube) { ?> data-video-src="<?php echo $meta->video_url; ?>" <?php } ?> <?php if ($meta->mp3) { ?>data-mp3-source="<?php echo $meta->mp3; ?>" <?php } ?>src="" type="video/mp4" />
                    </video>
                    <div class="video-cover" style="background-image: url(<?php echo $posterimage[0]; ?>);
                                    background-position: center;
                                    background-repeat: no-repeat;
                                    -webkit-background-size: cover;
                                    -moz-background-size: cover;
                                    -o-background-size: cover;
                                    background-size: cover;">
                        <div class="video-play">
                            <?php if ($meta->video_url && !$meta->youtube) { ?>
                                <a href="#" alt="Play" class="play-btn"> <i class="eicon-play" aria-hidden="true"></i> </a>
                            <?php } else if ($meta->vimeo) { ?>
                                <a href="#" alt="Play" class="vimeo-play-btn"> <i class="eicon-play" aria-hidden="true"></i> </a>
                            <?php } else if ($meta->youtube) { ?>
                                <a href="#" alt="Play" class="youtube-play-btn"> <i class="eicon-play" aria-hidden="true"></i> </a>
                            <?php } else if ($meta->mp3) { ?>
                                <a href="#" alt="Listen" class="listen-btn"> <i class="eicon-play" aria-hidden="true"></i> </a>
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>
            </div>
            <div class="video-controls">
                <div class="controls-wrapper">
                    <?php if ($meta->video_url && !$meta->youtube) { ?>
                        <a href="#" alt="Play" class="play-btn">Watch</a>
                    <?php } ?>
                    <?php if ($meta->vimeo) { ?>
                        <a href="#" alt="Play" class="vimeo-play-btn">Watch</a>
                    <?php } ?>
                    <?php if ($meta->youtube) { ?>
                        <a href="#" alt="Play" class="youtube-play-btn">Watch</a>
                    <?php } ?>
                    <?php if ($meta->mp3) { ?>
                        <a href="#" alt="Listen" class="listen-btn">Listen</a>
                    <?php } ?>
                    <?php foreach ($pdfs as $pdf) { ?>
                        <a href="<?php echo $pdf->url; ?>" download="<?php echo $pdf->title ?: 'Notes'; ?>">Notes</a>
                    <?php } ?>
                </div>
            </div>
        <?php
        } else { ?>
            <div class="no-media">
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
    </div>
    <div class="title-wrapper">
        <a href="<?php echo get_the_permalink(); ?>">
            <h2>Hear the Latest</h2>
            <?php if ($meta->speaker) { ?>
                <p><b><?php the_title(); ?> | <?php echo get_the_time('F d', get_the_ID()); ?> | <?php echo get_the_title($meta->speaker); ?></b></p>
            <?php } else { ?>
                <p><b><?php the_title(); ?> | <?php echo get_the_time('F d', get_the_ID()); ?></b></p>
            <?php  } ?>
            <?php if ($meta->description) { ?>
                <p><?php echo $meta->description; ?></p>
            <?php  } ?>
            <a href="<?php echo get_term_link($term[0]); ?>">More from the series >></a>
        </a>
    </div>
</div>