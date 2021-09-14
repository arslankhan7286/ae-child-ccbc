<?php
/**
 * The template for displaying sermon category.
 */
$config = \Ardent\Ccbc\Sermons::getConfig();
global $wp_query, $paged;
$page = 1;
$page_number = 1;
$sermons_per_page = 9;
$cat_meta = new \Ardent\Wp\Meta(get_queried_object_id());
$cat_image = \Ardent\Ccbc\Sermons\Term::getTermImage(get_queried_object_id());
$bgImageID = \Ardent\Ccbc\Sermons\Term::getTermBGImage(get_queried_object_id());
$bgImage = wp_get_attachment_image_src(($bgImageID ?: $config->header_image), 'large');
$cat_image_src = wp_get_attachment_image_src(($cat_image?:$config->default_image), 'large');
if(strpos($_SERVER['REQUEST_URI'], '/page/')){
    preg_match('/\/page\/([0-9]+?)\//', $_SERVER['REQUEST_URI'], $matches);
    $cp = (int)$matches[1];
    if($paged <= 1 && $cp != $paged){
        $paged = $cp;
    }
}
get_header();
?>
<?php 
    if ( function_exists( 'yoast_breadcrumb' ) ){ yoast_breadcrumb('<p id="breadcrumbs">','</p>'); } 
?>
<div class="sermon-media">
    <div class="container-wrap" style="padding-top:0 !important;">
        <div class="hero_section" style="background-image: url(<?php echo $bgImage[0]; ?>);
                            background-position: center;
                            background-repeat: no-repeat;
                            -webkit-background-size: cover;
                            -moz-background-size: cover;
                            -o-background-size: cover;
                            background-size: cover;">
            <div class="container">
                <div class="video-container">
                            <div class="no-media">
                                <div class="post-img" style="background-image: url(<?php echo $cat_image_src[0]; ?>);
                                     background-position: center;
                                     background-repeat: no-repeat;
                                     -webkit-background-size: cover;
                                     -moz-background-size: cover;
                                     -o-background-size: cover;
                                     background-size: cover;"></div>
                            </div>
                        <div class="title-wrapper">
                            <div class="social-media">
                                <h2>Share This Series</h2>
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
                        </div>
                </div>
            </div>
        </div>
        <div class="sermon-ctas">
            <a href="#series-list" class="full-width"><i class="fa fa-ellipsis-h"></i> View sermons in this series</a>
        </div>
        <div class="container main-content">
            <div class="row">
                <?php 
                    $category_query = new WP_Query(array(
                        'post_type' => 'sermon',
                        'posts_per_page' => 9,
                        'paged' => $paged,
                        'orderby' => 'date&order=ASC',
                        'tax_query' => array(
                            array(
                                'taxonomy' => 'sermon_category',
                                'field' => 'term_id',
                                'terms' => get_queried_object_id(),
                                'include_children' => true
                            )
                        )
                    ));
                    if($category_query->have_posts()){
                ?>
                <div class="series-list" id="series-list">
                    <h6>More sermons in this series</h6>
                    <div class="row">
                    <?php 
                        while($category_query->have_posts()){ $category_query->the_post();
                        $meta = new \Ardent\Wp\Meta;
                        $hidden = '';
                        $terms = get_the_terms(get_the_ID(), 'sermon_category');
                        foreach ($terms as $term) {
                            $cat_image = \Ardent\Ccbc\Sermons\Term::getTermImage($term->term_id);
                        }
                        $cat_image_src = wp_get_attachment_image_src(($cat_image?:$config->default_image), 'large');
                        
                    ?>
                    <div class="single-sermon tallest" <?php echo $hidden; ?>>
                        <a href="<?php the_permalink(); ?>" alt="<?php echo get_the_title(); ?>">
                            <div class="sermon-image" style="background-image: url(<?php echo $cat_image_src[0]; ?>);
                            background-position: center;
                            background-repeat: no-repeat;
                            -webkit-background-size: cover;
                            -moz-background-size: cover;
                            -o-background-size: cover;
                            background-size: cover;"></div>
                            <div class="single-sermon-copy">
                                <h4><?php echo get_the_title($meta->speaker).($meta->speaker?'   |   ':'').get_the_time('F d', get_the_ID()); ?></h4>
                                <h3><?php echo get_the_title(); ?></h3>
                            </div>
                        </a>
                    </div>
                    <?php  } wp_reset_postdata();  ?>
                </div>
                <?php } ?>
                
                <div class="pagination">
                    <?php echo \Ardent\Ccbc\Pagination::getAdvanced(); ?>
                </div>
              </div>
            </div><!--/row-->
        </div><!--/container-->
    </div><!--/container-wrap-->
</div>
	
<?php get_footer();