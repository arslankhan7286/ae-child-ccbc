<?php

if (isset($_REQUEST['sermonSearch'])) {
  add_action('wp_loaded', function() {
    global $wp_query, $paged;
    $config = \Ardent\Ccbc\Sermons::getConfig();
    $search_term = $_REQUEST['sermonSearch'];
    $page = $_REQUEST['sermon_page'] ?: 1;
    $prefix = '&';
    $page_request = '';
    if (isset($_REQUEST['sermon_page'])) {
        $page_request = $prefix . 'sermon_page=' . $page;
    }
    $qargs = array(
        'post_type' => 'sermon',
        'posts_per_page' => 9,
        'orderby' => 'date',
        'paged' => $page,
        'tax_query' => array(
            array(
                'taxonomy' => 'search_tags',
                'field' => 'name',
                'terms' => $search_term,
                'operator' => 'IN'
            )
        )
    );
    $all_qargs = array(
        'post_type' => 'sermon',
        'posts_per_page' => -1,
        'orderby' => 'date',
        'paged' => $page,
        'tax_query' => array(
            array(
                'taxonomy' => 'search_tags',
                'field' => 'name',
                'terms' => $search_term,
                'operator' => 'IN'
            )
        )
    );
    $search_args = array(
        'post_type' => 'sermon',
        'posts_per_page' => 9,
        'orderby' => 'date',
        'paged' => $page,
        's'=> $search_term,
    );
    $all_search_args = array(
        'post_type' => 'sermon',
        'posts_per_page' => -1,
        'orderby' => 'date',
        'paged' => $page,
        's'=> $search_term,
    );
    
    $query_1 = new WP_Query($all_qargs);
    $query_2 = new WP_Query($all_search_args);

    $count_1 = $query_1->post_count;
    $count_2 = $query_2->post_count;
    $post_count = $count_1 + $count_2;
 
    $sermons_per_page = 9;
    //custom pager
    $pager = new \Ardent\Pager([
        'page'   => $page,
        'pages'  => ceil($post_count / $sermons_per_page),
        'format' => get_the_permalink() . '{{page}}/',
        'max' => 5,
        'next' => '<i class="fa fa-chevron-right" aria-hidden="true"></i>',
        'prev' => '<i class="fa fa-chevron-left" aria-hidden="true"></i>'
    ]);

    //args for sermons to count them for the pager and to index their years
    $all_sermons_args = array(
        'post_type' => 'sermon',
        'posts_per_page' => -1,
        'fields' => 'ids',
        'no_found_rows' => true,
    );
    ?>
    <?php if ($post_count > 0) {?>
    <div>
        <h4>Showing <b><?php echo $post_count; ?></b> results for <b><?php echo $search_term; ?></b></h4>
        <a class="clear_search" href="/ccbc2018/sermons/?all=1">Clear search</a>
    </div>
    <?php }?>
    <?php 
        $general_search_results = false;
        $tag_search_results = false;
    ?>
<div class="series-url" 
    data-speakers=""
    data-series=""
    data-years=""
    data-scriptures=""
    data-filter-url="">
</div>
    <?php query_posts($qargs);
        
        if(have_posts()){
            $general_search_results = true;
            while(have_posts()){ the_post(); ?>
    <?php get_template_part('templates/ajax/sermons_archive/sermons_search');?>
        <?php } }?> 
    <?php query_posts($search_args);
        if(have_posts()){
            $tag_search_results = true;
            while(have_posts()){ the_post(); ?>
    <?php get_template_part('templates/ajax/sermons_archive/sermons_search');?>
    <?php } }
   
    ?>
    <?php 
    if (!$general_search_results && !$tag_search_results) {?>
    <div class="single-post">
        <h3>There are no results with the selected filters. </h3>
        <p>Try <a class="clear_search" href="/ccbc2018/sermons/?all=1">clearing</a> your search?</p>
    </div>
    <?php } ?>
    <div class="pagination">
        <?php echo implode(PHP_EOL, $pager->getLinks()); ?>
    </div>
     <?php 
      exit;
  });
}