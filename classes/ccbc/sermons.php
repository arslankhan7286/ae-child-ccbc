<?php

namespace Ardent\Ccbc;

if (!class_exists('\Ardent\Ccbc\Sermons')) {

    final class Sermons {

        static function getConfig() {
            return \Ardent\Wp\Config::getInstance('Sermons', 'Extra Content');
        }

        static function seriesArray() {
            $series_array = [];
            if (isset($_REQUEST['series'])) {
                $series_array = explode(',', $_REQUEST['series']);
                foreach($series_array as $key => $value){
                    $series_array[$key] = (int)$value;
                }
            }
            return $series_array;
        }

        static function authArray() {
            $auth_array = [];
            if (isset($_REQUEST['auth'])) {
                $auth_array = explode(',', $_REQUEST['auth']);
                foreach($auth_array as $key => $value){
                    $auth_array[$key] = (int)$value;
                }
            }
            return $auth_array;
        }
        static function yearArray() {
            $year_array = [];
            if (isset($_REQUEST['year'])) {
                $year_array = explode(',', $_REQUEST['year']);
                foreach($year_array as $key => $value){
                    $year_array[$key] = (int)$value;
                }
            }
            return $year_array;
        }
        static function scriptArray() {
            $script_array = [];
            if (isset($_REQUEST['scripture'])) {
                $script_array = explode(',', $_REQUEST['scripture']);
                foreach($script_array as $key => $value){
                    $script_array[$value] = $key;
                }
            }
            return $script_array;
        }
        static function buildFilterUrl($auth_ids, $series_ids, $years, $scripture) {
            $url = '';
            $series = false;
            $author = false;
            $year_status = false;
            $scripture_status = false;
            if (count($series_ids) > 0 || count($auth_ids) > 0 || count($years) > 0 || count($scripture) > 0) {
                if (count($series_ids) > 0) {
                    $url .= '?series=' . implode(',',$series_ids);
                    $series = true;
                }
                if (count($auth_ids) > 0) {
                    $author = true;
                    if ($series) {
                        $url .= '&';
                    } else {
                        $url .= '?';
                    }
                    $url .= 'auth=' . implode(',',$auth_ids);
                }
                if (count($years) > 0) {
                    $year_status = true;
                    if ($series || $author) {
                        $url .= '&';
                    } else {
                        $url .= '?';
                    }
                    $url .= 'year=' . implode(',',$years);
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

    }

}