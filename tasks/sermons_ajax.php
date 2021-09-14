<?php
if (isset($_REQUEST['series']) || isset($_REQUEST['auth']) || isset($_REQUEST['year']) || isset($_REQUEST['sermon_page']) || isset($_REQUEST['scripture']) || isset($_REQUEST['all']) ) {
    if (strpos($_REQUEST['series'], 'page') == false && strpos($_REQUEST['auth'], 'page') == false) {
        add_action('wp_loaded', function() {
            ob_start();
          
            $page = $_REQUEST['sermon_page'] ?: 1;
            $prefix = '?';
                
            $series_array = [];
            if (isset($_REQUEST['series'])) {
                $prefix = '&';
                $series_array = explode(',', $_REQUEST['series']);

                foreach ($series_array as $key => $value) {
                    $series_array[$key] = (int) $value;
                }
            }

            $auth_array = [];
            if (isset($_REQUEST['auth'])) {
                $prefix = '&';
                $auth_array = explode(',', $_REQUEST['auth']);

                foreach ($auth_array as $key => $value) {
                    $auth_array[$key] = (int) $value;
                }
            }

            $year_array = [];
            if (isset($_REQUEST['year'])) {
                $prefix = '&';
                $year_array = explode(',', $_REQUEST['year']);

                foreach ($year_array as $key => $value) {
                    $year_array[$key] = (int) $value;
                }
            }
            
            $script_array = [];
            if (isset($_REQUEST['scripture'])) {
                $script_array = explode(',', $_REQUEST['scripture']);
                foreach($script_array as $key => $value){
                    $script_array[$key] = $value;
                }
            }
            function buildFilterUrl($auth_ids, $series_ids, $years, $scripture) {
                $url = '';
                $series = false;
                $author = false;
                $year_status = false;

                if (count($series_ids) > 0 || count($auth_ids) > 0 || count($years) > 0 || count($scripture) > 0) {
                    if (count($series_ids) > 0) {
                        $url .= '?series=' . implode(',', $series_ids);
                        $series = true;
                    }
                    if (count($auth_ids) > 0) {
                        $author = true;
                        if ($series) {
                            $url .= '&';
                        } else {
                            $url .= '?';
                        }
                        $url .= 'auth=' . implode(',', $auth_ids);
                    }
                    if (count($years) > 0) {
                        $year_status = true;
                        if ($series || $author) {
                            $url .= '&';
                        } else {
                            $url .= '?';
                        }
                        $url .= 'year=' . implode(',', $years);
                    }
                    if (count($scripture) > 0) {
                        $scripture_status = true;
                        if ($series || $author || $year_status) {
                            $url .= '&';
                        } else {
                            $url .= '?';
                        }
                        $url .= 'scripture=' . implode(',',$scripture);
                    } 
                  
                    return $url;
                }
            }
            $page_request = '';
            if (isset($_REQUEST['sermon_page'])) {
                $page_request = $prefix . 'sermon_page=' . $page;
            } 
            \Ardent\Ccbc\Sermons\Filters::getSermon(buildFilterUrl($auth_array, $series_array, $year_array, $script_array) . $page_request);
            
            $scat = get_query_var('sermon_category');
            $term = get_term_by('slug', $scat, 'sermon_category');

            $tax_query = array(array(
                    'taxonomy' => 'sermon_category',
                    'field' => 'ID',
                    'terms' => $series_array,
                    'operator' => 'IN'
            ));
            $latest_sermon = wp_get_recent_posts(array(
                'numberposts' => 1,
                'post_type' => 'sermon',
                'post_status' => 'publish',
                'tax_query' => $tax_query
            ));
            wp_reset_postdata();

            $qargs = array(
                'post_type' => 'sermon',
                'posts_per_page' => 9,
                'orderby' => 'date',
                'paged' => $page,
                'post__not_in' => array($latest_sermon[0]["ID"])
            );
            
            $post_count_args = '';
            
            if(isset($_GET['auth']) || isset($_GET['series']) || isset($_GET['year']) || isset($_REQUEST['scripture']) || isset($_REQUEST['all'])) {
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
                
                if (isset($_GET['scripture']) && isset($_GET['auth'])){
                    $meta_query = array(
                        'relation' => 'OR',
                        $speaker_meta_query,
                        $script_meta_query
                    );
                } else if (isset($_GET['scripture']) && !isset($_GET['auth'])){
                    $meta_query = array(
                        $script_meta_query
                    );
                } else if (!isset($_GET['scripture']) && isset($_GET['auth'])){
                    $meta_query = array(
                        $speaker_meta_query
                    );
                } else {
                    $meta_query = '';
                }
                if (isset($_REQUEST['all'])) {
                    $qargs = array(
                        'post_type' => 'sermon',
                        'posts_per_page' => 9,
                        'orderby' => 'date',
                        'paged' => $page,
                        'post__not_in' => array($latest_sermon[0]["ID"])
                    );

                    $post_count_args = array(
                        'post_type' => 'sermon',
                        'posts_per_page' => -1,
                        'orderby' => 'date',
                        'paged' => $page,
                        'post__not_in' => array($latest_sermon[0]["ID"])
                    );
                } else {
                    $qargs = array(
                        'post_type' => 'sermon',
                        'posts_per_page' => 9,
                        'orderby' => 'date',
                        'paged' => $page,
                        'meta_query' => $meta_query,
                        'tax_query' => $series_query,
                        'date_query' => $year_query,
                    );

                    $post_count_args = array(
                        'post_type' => 'sermon',
                        'posts_per_page' => -1,
                        'paged' => $page,
                        'fields' => 'ids',
                        'no_found_rows' => true,
                        'meta_query' => $meta_query,
                        'tax_query' => $series_query,
                        'date_query' => $year_query,
                    );
                }
                
            }


            $sermons_per_page = 9;

            if(isset($_GET['auth']) || isset($_GET['series']) || isset($_GET['year']) || isset($_REQUEST['scripture'])) {
                $all_sermons = new WP_Query($post_count_args);
                $sermons_count = $all_sermons->post_count;
            } else {
                $sermons_count = wp_count_posts( 'sermon' )->publish;
            }


            //custom pager
            $pager = new \Ardent\Pager([
                'page'   => $page,
                'pages'  => ceil($sermons_count / $sermons_per_page),
                'format' => get_the_permalink() . '{{page}}/',
                'max' => 5,
                'next' => '<i class="fa fa-chevron-right" aria-hidden="true"></i>',
                'prev' => '<i class="fa fa-chevron-left" aria-hidden="true"></i>'
            ]);
            ?>
            <div class="series-url" 
                 data-speakers="<?php echo implode(',', $auth_array); ?>"
                 data-series="<?php echo implode(',', $series_array); ?>"
                 data-years="<?php echo implode(',', $year_array);?>"
                 data-scriptures="<?php echo implode(',', $script_array); ?>"
                 data-filter-url="<?php echo buildFilterUrl($auth_array, $series_array, $year_array, $script_array); ?>">
            </div>
            <?php
            query_posts($qargs);
            if (have_posts()) {
                while (have_posts()) {
                    the_post();
                    get_template_part('templates/ajax/sermons_archive/posts', get_post_format());
                    ?>
                    <?php }
                } else { ?>
                <div class="single-post">
                    <h3>There are no results with the selected filters. </h3>
                    <p>Try <a class="clear_search" href="/ccbc2018/sermons/?all=1">clearing</a> your search?</p>
                </div>
            <?php } ?>
                        <div class="pagination">
            <?php echo implode(PHP_EOL, $pager->getLinks()); ?>
                        </div>
            <?php
            $contents = ob_get_contents(); 
            \Ardent\Ccbc\Sermons\Filters::cacheSermon(buildFilterUrl($auth_array, $series_array, $year_array, $script_array) . $page_request, $contents);
            exit;
        });
    }
}