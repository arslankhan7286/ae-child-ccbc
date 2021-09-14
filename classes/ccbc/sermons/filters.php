<?php

namespace Ardent\Ccbc\Sermons;

if (!class_exists('\Ardent\Ccbc\Sermons\Filters')) {

    final class Filters {

        static function getSermon($query) {
            if(is_admin() || is_user_logged_in()){
                return;
            }
            $url = $query;
            $sermondata = \Ardent\Ccbc\Sermons\Filters::getCachedSermon($url);
            if(count($sermondata)){
                echo $sermondata;
                exit;
            }
        }
        
        private function getCachedSermon($url){
            $file = WP_CONTENT_DIR.'/sermon_filters_cache/' . md5($url) .'.cache';
            if(file_exists($file)){
                return file_get_contents($file);
            }else{
                return array();
            }
        }
        
        static function cacheSermon($url, $data){
            if(is_admin() || is_user_logged_in()){
                return;
            }
            $dir = WP_CONTENT_DIR.'/sermon_filters_cache';
            $file = $dir . '/' . md5($url) .'.cache';
            if(!file_exists($dir)){
                mkdir($dir,0755);
            }
            if(!file_exists($dir.'/.htaccess')){
                file_put_contents($dir.'/.htaccess', 'Deny from all');
            }
            if(!file_exists($file)){
                file_put_contents($file, $data);
            }
        }
        
        private function getData($content){
           return $content;
        }

    }

}