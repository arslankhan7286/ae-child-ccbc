<?php

namespace Ardent\Ccbc;

if (!class_exists('\Ardent\Ccbc\Pagination')) {

    final class Pagination {

        static function getSimple() {
            $return = '';
            if( get_next_posts_link() || get_previous_posts_link() ) { 
                $return = '<div data-is-text="'.__("All items loaded", NECTAR_THEME_NAME).'">
                    <div class="prev">'.get_previous_posts_link('&laquo; Previous Entries').'</div>
                    <div class="next">'.get_next_posts_link('Next Entries &raquo;','').'</div>
                  </div>';

            }
            return $return;
        }
        static function getAdvanced($s = false, $all = false) {
            global $wp_query; 
            $return = '';
	      
            $wp_query->query_vars['paged'] > 1 ? $current = $wp_query->query_vars['paged'] : $current = 1;
            if(strpos($_SERVER['REQUEST_URI'], '/page/')){
                preg_match('/\/page\/([0-9]+?)\//', $_SERVER['REQUEST_URI'], $matches);
                $cp = (int)$matches[1];
                if($current <= 1 && $cp != $current){
                    $current = $cp;
                }
            }
            
            $total_pages = $wp_query->max_num_pages;

            if ($total_pages > 1) {

                $permalink_structure = get_option('permalink_structure');
                $query_type = (count($_GET)) ? '&' : '?';
                $format = empty($permalink_structure) ? $query_type . 'paged=%#%' : 'page/%#%/';

                $return .= '<div data-is-text="' . __("All items loaded", NECTAR_THEME_NAME) . '">';

                $pargs = array(
                    'base' => $s?str_replace( 999999999, '%#%', esc_url( get_pagenum_link(999999999))):($all?str_replace('?all=1','',get_pagenum_link(1)):get_pagenum_link(1)).'%_%',
                    'format' => $format,
                    'current' => $current,
                    'total' => $total_pages,
                );
                
                if($all){
                    $pargs['add_args'] = array('all' => 1);
                }
                
                $return .= paginate_links($pargs);

                $return .= '</div>';
            }
            return $return;
        }

    }

}