<?php
/**
 * The template for displaying the sermon archive pages.
 */
ob_start();
\Ardent\Ccbc\Sermons\Filters::getSermon('sermons?archive=1');
$config = \Ardent\Ccbc\Sermons::getConfig();

$scat = get_query_var('sermon_category');
$term = get_term_by('slug', $scat, 'sermon_category');
$page = 1;
$series_array = \Ardent\Ccbc\Sermons::seriesArray();
$auth_array = \Ardent\Ccbc\Sermons::authArray();
$year_array = \Ardent\Ccbc\Sermons::yearArray();
$script_array = \Ardent\Ccbc\Sermons::scriptArray();
function countSpeakerPosts($speakerID) {
    $speaker_args = array(
        'post_type' => 'sermon',
        'posts_per_page' => -1,
        'fields' => 'ids',
        'no_found_rows' => true,
        'meta_query' => array(
            array(
                'key' => 'speaker',
                'value' => $speakerID,
                'compare' => '=',
            )
        )
    );
    $speaker_counter = new WP_Query($speaker_args);
    return $speaker_counter->post_count; 
}
$tax_query = array(array(
    'taxonomy' => 'sermon_category',
    'field' => 'ID',
    'terms' => $series_array,
    'operator' => 'IN'
));
//sermon displayed in hero
$latest_sermon_args = array(
    'numberposts' => 1,
    'post_type' => 'sermon',
    'post_status' => 'publish',
    'tax_query' => array( 
        array(
            'taxonomy' => 'sermon_category',
            'field' => 'term_id',
            'terms' => $config->featured_series
        )
    )
);
$latest_sermon = get_posts($latest_sermon_args);
$latest_term = get_the_terms($latest_sermon[0]->ID, 'sermon_category');

//Default Query
$qargs = array(
    'post_type' => 'sermon',
    'posts_per_page' => 9,
    'orderby' => 'date',
    'paged' => $page,
    'post__not_in' => array($latest_sermon[0]->ID)
);

//Speaker Query
$speaker_query = new WP_Query(array(
    'post_type' => 'Speaker',
    'posts_per_page' => -1,
    'orderby' => 'post_title',
    'order' => 'ASC'
));
wp_reset_postdata();

//Filter Query
if(isset($_GET['auth']) || isset($_GET['series']) || isset($_GET['year'])) {
    if(isset($_GET['auth'])) {
        $speaker = $_GET['auth'];
        $speaker_meta_query = array(
                'key' => 'speaker',
                'value' => $speaker,
                'compare' => '=',
            );
    } else {
        $speaker_meta_query = '';
    }
    if(isset($_GET['scripture'])) {
        $scripture = $_GET['scripture'];
        $script_meta_query = array(
                'key' => 'references',
                'value' => $scripture,
                'compare' => 'LIKE',
            );
    } else {
        $script_meta_query = '';
    }
    if(isset($_GET['series'])) {
        $series_query = $tax_query;
    } else {
        $series_query = '';
    }
    if(isset($_GET['year'])) {
        $year = $_GET['year'];
        $year_query = array(
            array(
                'year'  => $year
            ),
        );
    } else {
        $year_query = '';
    }
  
    $qargs = array(
        'post_type' => 'sermon',
        'posts_per_page' => 9,
        'orderby' => 'date',
        'paged' => $page,
        'meta_query' => array(
            'relation' => 'OR',
            $speaker_meta_query,
            $script_meta_query
        ),
        'tax_query' => $series_query,
        'date_query' => $year_query,
    );
}

//args for sermons to count them for the pager and to index their years
$all_sermons_args = array(
    'post_type' => 'sermon',
    'posts_per_page' => -1,
    'fields' => 'ids',
    'no_found_rows' => true,
);

$years = array();
$scripture = array();
query_posts($all_sermons_args);
if(have_posts()){
    while(have_posts()){
        the_post();
        $year = get_the_date( 'Y' );
        $years[] = $year;
        $smeta = new \Ardent\Wp\Meta();
        if ($smeta->references) {
            $scripture[get_the_ID()] = $smeta->references;
        }
    }
    wp_reset_postdata(); 
}

//count the sermons
$sermons_count = wp_count_posts( 'sermon' )->publish;

$sermons_per_page = 9;

//custom pager
$pager = new \Ardent\Pager([
    'page'   => $page,
    'pages'  => ceil($sermons_count / $sermons_per_page),
    'format' => get_the_permalink() . '{{page}}/',
    'max' => 5,
    'next' => '<i class="fa fa-chevron-right" aria-hidden="true"></i>',
    'prev' => '<i class="fa fa-chevron-left" aria-hidden="true"></i>'
]);
get_header();

?>
<div class="sermon-media">
    <div class="container-wrap">
        <div class="row">
            <div class="hero_section" style="background-image: url(<?php echo wp_get_attachment_url($config->header_image); ?>);
                            background-position: center;
                            background-repeat: no-repeat;
                            -webkit-background-size: cover;
                            -moz-background-size: cover;
                            -o-background-size: cover;
                            background-size: cover;">
                <div class="container">  
                    <?php $lmeta = new \Ardent\Wp\Meta($latest_sermon[0]->ID); ?>
                    <div class="latest_sermon video-container <?php if ($lmeta->youtube && $lmeta->mp3) {?>youtube-mp3<?php }?>">
                        <?php 
                        $poster; 
                        $term = get_the_terms($latest_sermon[0]->ID, 'sermon_category');
                        $image = \Ardent\Ccbc\Sermons\Term::getTermImage($term[0]->term_id);
                        $posterimage = wp_get_attachment_image_src(($image?:$config->default_image), 'large');
                        $poster = ' poster="'.$posterimage[0].'"';
                        if($lmeta->video || $lmeta->video_url || $lmeta->vimeo || $lmeta->mp3 || $lmeta->youtube) { 
                        ?>
                        <div class="videoarea <?php if ($lmeta->vimeo) { echo 'iframe-true'; } else { echo 'iframe-false'; }?>">
                            <div class="video-overlay">
                                <a href="#" class="reset-btn"><i class="fa fa-angle-left"></i></a>
                            </div>
                            <?php if ($lmeta->video || $lmeta->vimeo || $lmeta->youtube) { ?>
                            <?php 
                                $video = '';
                                if ($lmeta->video && !$lmeta->youtube) {
                                    $video = \Ardent\Html::get(array('type' => 'truthcasting.player', 'mediaid' => $lmeta->video));
                                } else if ($lmeta->vimeo) {
                                    $video = \Ardent\Html::get(array('type' => 'iframe', 'src' => '//player.vimeo.com/video/' . $lmeta->vimeo, 'frameborder' => 0, 'webkitallowfullscreen' => true, 'mozallowfullscreen' => true, 'allowfullscreen' => true));
                                }else if ($lmeta->youtube) {
                                    $video = \Ardent\Html::get(array('type' => 'iframe', 'src' => 'https://www.youtube.com/embed/' . $lmeta->youtube . '/?ref=0&rel=0', 'frameborder' => 0, 'webkitallowfullscreen' => true, 'mozallowfullscreen' => true, 'allowfullscreen' => true));;
                                }
                                echo $video;
                            ?>
                            <?php } ?>
                            <?php 
                            if ($lmeta->video_url || $lmeta->mp3) {
                            ?> 
                            <video class="sermonvideotag" id="sermon-video" width="100%" style="background-image: url(<?php echo $posterimage[0]; ?>); background-size: cover; background-repeat: no-repeat;" controls>
                                <source <?php if ($lmeta->video_url && !$lmeta->youtube) {?> data-video-src="<?php echo $lmeta->video_url;?>" <?php } ?> <?php if ($lmeta->mp3) {?>data-mp3-source="<?php echo $lmeta->mp3; ?>" <?php } ?>src="" type="video/mp4"/>
                            </video>
                            <div class="video-cover" style="background-image: url(<?php echo $posterimage[0]; ?>);
                                background-position: center;
                                background-repeat: no-repeat;
                                -webkit-background-size: cover;
                                -moz-background-size: cover;
                                -o-background-size: cover;
                                background-size: cover;">
                                <div class="video-controls">
                                    <div class="series-container"><a href="<?php echo get_term_link($term[0]); ?>" class="series-btn"><img src="<?php echo $posterimage[0]; ?>" alt="<?php echo get_the_title();?>"></a></div>
                                    <p>Series <?php echo $term[0]->count; ?> of <?php echo $term[0]->count; ?> </p>
                                    <div class="controls-wrapper">
                                        <?php if ($lmeta->video_url && !$lmeta->youtube) {?>
                                            <a href="#" alt="Play" class="play-btn"> <i class="fa fa-play-circle"></i> </a>
                                            <?php } ?>
                                            <?php if ($lmeta->vimeo) {?>
                                            <a href="#" alt="Play" class="vimeo-play-btn"> <i class="fa fa-play-circle"></i> </a>
                                            <?php } ?>
                                            <?php if ($lmeta->youtube) {?>
                                            <a href="#" alt="Play" class="youtube-play-btn"> <i class="fa fa-play-circle"></i> </a>
                                            <?php } ?>
                                            <?php if ($lmeta->mp3) {?>
                                            <a href="#" alt="Listen" class="listen-btn"> <i class="fa fa-headphones"></i> </a>
                                            <?php } ?>
                                            <?php if ($lmeta->video_url || $lmeta->mp3) {?>
                                            <a href="<?php if ($lmeta->video_url) { echo $lmeta->video_url; } else { echo $lmeta->mp3; }?>" download alt="Download" class="download-btn"> <i class="fa fa-download"></i> </a>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                            <?php } ?>
                        </div>
                        <?php 
                            } else {?>
                            <div class="no-media">
                                <p>Series <?php echo $term[0]->count; ?> of <?php echo $term[0]->count; ?> </p>
                                 <div class="series-container"><a href="<?php echo get_term_link($term[0]); ?>" class="series-btn"><img src="<?php echo $posterimage[0]; ?>" alt="<?php echo get_the_title();?>"></a></div>
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
                            <a href="<?php echo get_the_permalink($latest_sermon[0]->ID); ?>">
                                <h2><?php echo $latest_term[0]->name ;?></h2>
                                <h4><?php echo get_the_title($lmeta->speaker).($lmeta->speaker?'   |   ':' ').get_the_time('F d',$latest_sermon[0]->ID); ?></h4>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container-wrap">
        <div class="container main-content">
            <div class="search">
                <form class="search" action="<?php echo get_home_url(); ?>/sermons/main/">
                   <input type="search" name="sermonSearch" placeholder="Search">
                   <input type="submit" value="Search">
                   <input type="hidden" name="post_type" value="sermon">
                 </form>        
            </div>
            <div class="post-data" id="post-data">
            <div class="row">
                <div class="col-md-3">
                    <div class="filters">
                        <div class="filter">
                            <h4>Series</h4>
                            <ul class="series-filters">
                                <?php 
                                $terms = get_terms( array(
                                    'taxonomy' => 'sermon_category',
                                    'hide_empty' => true,
                                    'orderby'    => 'count',
                                    'posts_per_page' => -1,
                                    'order' => 'DESC'
                                ));
                                foreach ($terms  as $term ) { 
                                    $series_ids = $series_array;   
                                    if(in_array($term->term_id, $series_ids)){
                                        $key = array_search($term->term_id, $series_ids);
                                        unset($series_ids[$key]);
                                    } else {
                                        $series_ids[] = $term->term_id;
                                    }
                                ?>
                                <li <?php if (in_array($term->term_id, $series_array)) {echo 'class="active-filter"';}; ?>><a data-filter-type="series" data-default="?series=<?php echo $term->term_id; ?>" data-default-url="<?php echo $term->term_id; ?>" data-series-url="<?php echo \Ardent\Ccbc\Sermons::buildFilterUrl($auth_array, $series_ids, $year_array, $script_array); ?>" href="<?php echo get_home_url(); ?>/sermons/main/<?php echo \Ardent\Ccbc\Sermons::buildFilterUrl($auth_array, $series_ids, $year_array, $script_array); ?>"><?php echo $term->name; ?><span><?php echo $term->count; ?></span></a></li>
                                <?php }
                                ?>
                            </ul>
                        </div>
                        <div class="filter">
                            <h4>Speakers</h4>
                            <ul class="speakers-filters">
                                <?php 
                                if ($speaker_query->have_posts()) {
                                    while($speaker_query->have_posts()){ $speaker_query->the_post();   
                                $auth_ids = $auth_array;
                                if(in_array(get_the_ID(), $auth_ids)){
                                    $key = array_search(get_the_ID(), $auth_ids);
                                    unset($auth_ids[$key]);
                                } else {
                                    $auth_ids[] = get_the_ID();
                                }   
                                ?>
                                <li <?php if (in_array(get_the_ID(), $auth_array)) {echo 'class="active-filter"';}; ?>><a data-filter-type="auth" data-default="?auth=<?php echo get_the_ID(); ?>" data-default-url="<?php echo get_the_ID(); ?>" data-series-url="<?php echo \Ardent\Ccbc\Sermons::buildFilterUrl($auth_ids, $series_array, $year_array, $script_array); ?>" href="<?php echo get_home_url() . '/sermons/main/' . \Ardent\Ccbc\Sermons::buildFilterUrl($auth_ids, $series_array, $year_array, $script_array); ?>"><?php the_title();?> <span><?php echo countSpeakerPosts(get_the_ID()); ?></span></a></li>
                                <?php 
                                    } 
                                wp_reset_postdata();  
                                } ?>
                            </ul>
                        </div>
                        <div class="filter">
                            <h4>Year</h4>
                            <ul class="year-filters">
                               <?php $vals = array_count_values($years);
                                    foreach($vals as $key => $val) {     
                                        $year_ids = $year_array;
                                        if(in_array($key, $year_ids)){
                                            $i = array_search($key, $year_ids);
                                            unset($year_ids[$i]);
                                        } else {
                                            $year_ids[] = $key;
                                        }
                                        ?>
                                        <li <?php if (in_array($key, $year_array)) { ?> class="active-filter" <?php } ?>><a data-filter-type="year" data-default="?year=<?php echo $key; ?>" data-default-url="<?php echo $key; ?>" data-series-url="<?php echo \Ardent\Ccbc\Sermons::buildFilterUrl($auth_array, $series_array, $year_ids, $script_array); ?>" href="<?php echo get_home_url() . '/sermons/main/' . \Ardent\Ccbc\Sermons::buildFilterUrl($auth_array, $series_array, $year_ids, $script_array); ?>"><?php echo $key; ?><span><?php  echo $val; ?></span></a></li>
                                <?php }
                                ?>
                            </ul>
                        </div>
                        <div class="filter">
                            <h4>Scripture</h4>
                            <ul class="scripture-filters">
                               <?php $svals = $scripture;
                                    foreach($svals as $key => $val) { 
                                        if( strpos($val, ',') !== false || strpos($val, ', ') !== false) {
                                            $refs = explode(",",$val);
                                            foreach ($refs as $ref ) { 
                                                $script_ids = $script_array;
                                                if(in_array($key, $script_ids)){
                                                    $i = array_search($ref, $script_ids);
                                                    unset($script_ids[$i]);
                                                } else {
                                                    $script_ids[] = $ref;
                                                }
                                                $script_count = array_count_values($svals)[$val];
                                                ?>
                                <li <?php if (in_array($ref, $script_array)) { ?> class="active-filter" <?php } ?>><a data-filter-type="scripture" data-default="?scripture=<?php echo $ref; ?>" data-default-url="<?php echo $ref; ?>" data-series-url="<?php echo \Ardent\Ccbc\Sermons::buildFilterUrl($auth_array, $series_array, $year_array, $script_ids); ?>" href="<?php echo get_home_url() . '/sermons/main/' . \Ardent\Ccbc\Sermons::buildFilterUrl($auth_array, $series_array, $year_array, $script_ids); ?>"><?php echo $ref; ?> <span><?php echo $script_count; ?></span></a></li>
                                    <?php }}}
                
                                ?>
                                <?php $svals = $scripture;
                                    foreach($svals as $key => $val) {   
                                        if( strpos($val, ',') == false || strpos($val, ', ') == false) {
                                            $script_ids = $script_array;
                                            if(in_array($key, $script_ids)){
                                                $i = array_search($val, $script_ids);
                                                unset($script_ids[$i]);
                                            } else {
                                                $script_ids[] = $val;
                                            }
                                            $script_count = array_count_values($svals)[$val];
                                            ?>
                                <li <?php if (in_array($val, $script_array)) { ?> class="active-filter" <?php } ?>><a data-filter-type="scripture" data-default="?scripture=<?php echo $val; ?>" data-default-url="<?php echo $val; ?>" data-series-url="<?php echo \Ardent\Ccbc\Sermons::buildFilterUrl($auth_array, $series_array, $year_array, $script_ids); ?>" href="<?php echo get_home_url() . '/sermons/main/' . \Ardent\Ccbc\Sermons::buildFilterUrl($auth_array, $series_array, $year_array, $script_ids); ?>"><?php echo $val; ?> <span><?php echo $script_count; ?></span></a></li><?php }} ?>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="post-list">
                       <div class="series-url" 
                 data-speakers="<?php echo implode(',', $auth_array); ?>"
                 data-series="<?php echo implode(',', $series_array); ?>"
                 data-years="<?php echo implode(',', $year_array);?>"
                 data-scriptures="<?php echo implode(',', $script_array); ?>"
                 data-filter-url="<?php echo \Ardent\Ccbc\Sermons::buildFilterUrl($auth_array, $series_array, $year_array, $script_array); ?>">
            </div>
                        <?php 
                        query_posts($qargs);
                        if(have_posts()){
                                while(have_posts()){ the_post();
                                $pterm = get_the_terms(get_the_ID(), 'sermon_category');
                                $image = \Ardent\Ccbc\Sermons\Term::getTermImage($pterm[0]->term_id);
                                $meta = new \Ardent\Wp\Meta(get_the_ID());
                                $posterimage = wp_get_attachment_image_src(($image?:$config->default_image), 'large');
                            ?>
                        <div class="single-post">
                            <a href="<?php the_permalink(); ?>">
                                <div class="post-thumbnail">
                                    <img src="<?php echo $posterimage[0]; ?>" alt="<?php the_title(); ?>" />
                                </div>
                                <div class="post-text">
                                    <h4><?php the_title(); ?></h4>
                                    <p><span><?php echo get_the_time('F d Y', get_the_ID());?></span> | <span><?php echo get_the_title($meta->speaker); ?></span></p>
                                </div>
                            </a>
                        </div>
                        <?php } } else { ?>
                        <div class="single-post">
                            <h3>There are no results with the selected filters. </h3>
                            <p>Try <a class="clear_search" href="/ccbc2018/sermons/?all=1">clearing</a> your search?</p>
                        </div>
                        <?php } ?>
                        <div class="pagination">
                           <?php echo implode(PHP_EOL, $pager->getLinks()); ?>
                        </div>
                </div>
            </div>
            </div>
        </div>
    </div> 
</div> 
<?php get_footer(); ?>
<?php
$contents = ob_get_contents(); 
\Ardent\Ccbc\Sermons\Filters::cacheSermon('sermons?archive=1', $contents);
?>