//Custom JS
(function($) {    



})(jQuery);

//Lightcase Initialization
jQuery(document).ready(function($) {
    $('a[data-rel^=lightcase]').lightcase();
});

(function ($) {
    /*
    Video overlay controls
    added by Micky
    */
$(function () {
       var sources = document.querySelectorAll('video.sermonvideotag source');
       var video = document.querySelector('video.sermonvideotag');
       $('.play-btn, .listen-btn, .vimeo-play-btn').on('click', function(e){
           e.preventDefault();
           reset();
           if ($(this).hasClass('play-btn')) {
               for(var i = 0; i < sources.length; i++) {
                   sources[i].setAttribute('src', sources[i].getAttribute('data-video-src'));
               }
           }
           if ($(this).hasClass('listen-btn')) {
               var mp3src = '';
               for(var i = 0; i < sources.length; i++) {
                   mp3src = sources[i].getAttribute('data-mp3-source');
               }
               $('video.sermonvideotag').attr('x-webkit-airplay', "allow");
               $('video.sermonvideotag').attr('playsinline', "");
               $('video.sermonvideotag').attr('webkit-playsinline', "");
               $('video.sermonvideotag').attr('src', mp3src);
               
             $('.video-container').addClass('audio-playing');
           } else {
               if ($('video.sermonvideotag').attr('src')) {
                   $('video.sermonvideotag').removeAttr('src');
                   $('video.sermonvideotag').removeAttr('x-webkit-airplay');
                   $('video.sermonvideotag').removeAttr('playsinline');
                   $('video.sermonvideotag').removeAttr('webkit-playsinline'); 
               }
           }
           if ($(this).hasClass('play-btn') || $(this).hasClass('listen-btn')) {
               video.load();
               video.oncanplay = function() {
                   console.log('playable');
                   video.play();
                   $('.video-container').addClass('played');
               }
           }
           if ($(this).hasClass('vimeo-play-btn')) {
               $('.iframe-embed iframe').attr('autplay', 'true');
               $('.video-container').addClass('played');
               $('.video-container').addClass('video-playing');
           }
       });

       $('.youtube-play-btn').on('click', function(e){
            e.preventDefault();
            reset();
            // var symbol = $("iframe.ardent-html-iframe")[0].src.indexOf("?") > -1 ? "&" : "?";
            // $("iframe.ardent-html-iframe")[0].src += symbol + "autoplay=1";
            $('.video-container').addClass('played');
            $('.video-container').addClass('video-playing');
            $('.video-container').addClass('youtube-video-playing');
       });
       $('.reset-btn').on('click', function(e){
           e.preventDefault();
           if (video) {
            video.pause();
           }
           $('.video-container').removeClass('played');
           $('.video-container').removeClass('audio-playing');
           $('.video-container').removeClass('video-playing');
       });
       function reset() {
            if (video) {
            video.pause();
           }
           $('.video-container').removeClass('played');
           $('.video-container').removeClass('audio-playing');
           $('.video-container').removeClass('video-playing');
       }
       
});
})(jQuery);

(function ($) {
    /*
    Float layout fix
    */
$(function () {
   function tallest(element) {
 var tallest = 0;
 var elements = $(element);
 for (var i = 0; i < elements.length; i++) {
   if (elements[i].clientHeight > tallest) {
     tallest = elements[i].clientHeight;
   }
 }
 $(element).height(tallest +'px');
}
   $(window).on("load",function(){
     tallest('.tallest');  
   });
   
})
})(jQuery);

(function ($) {
    /*
    Sermon ajax
added by Micky
    */
$(function () {
       $( document ).ready(function(){

       
       var scroll = new SmoothScroll();
       var anchor = document.querySelector( '#post-data' );
       function buildFilterUrl($auth_ids, $series_ids, $years, $scripture) {
           $url = '';
           $series = false;
           $author = false;
           $year_status = false;

           if ($series_ids || $auth_ids || $years || $scripture) {
               if ($series_ids) {
                   $url += '?series=' + $series_ids;
                   $series = true;
               }
               if ($auth_ids) {
                   $author = true;
                   if ($series) {
                       $url += '&';
                   } else {
                       $url += '?';
                   }
                   $url += 'auth=' + $auth_ids;
               }
               if ($years) {
                   $year_status = true;
                   if ($series || $author) {
                       $url += '&';
                   } else {
                       $url += '?';
                   }
                   $url += 'year=' + $years;
               }
               if ($scripture) {
                   
                   $scripture_status = true;
                   if ($series || $author || $year_status) {
                       $url += '&';
                   } else {
                       $url += '?';
                   }
                   $url += 'scripture=' + $scripture;
               } 
               return $url;
           }
       }
       function updateFilters() {
           var $target = $('.post-list'),
               series_url = $target.find('.series-url').attr('data-filter-url'),
               series = $target.find('.series-url').attr('data-series'),
               speakers = $target.find('.series-url').attr('data-speakers'),
               years = $target.find('.series-url').attr('data-years'),
               scriptures = $target.find('.series-url').attr('data-scriptures'),
               series_array = series.split(","),
               speakers_array = speakers.split(","),
               years_array = years.split(","),
               scriptures_array = scriptures.split(",");
            
           $('.filters .filter li').each(function(i) {
               var $filter = $(this).find('a'),
                   defaultFilter = $filter.attr('data-default-url'),
                   filterType = $filter.attr('data-filter-type'),
                   filter_url = '',
                   series_url = '',
                   series_ids = series_array,
                   speakers_url = '',
                   speakers_ids = speakers_array,
                   years_url = '',
                   years_ids = years_array,
                   scriptures_url = '',
                   scriptures_ids = scriptures_array;
                   
               $(this).removeClass('active-hidden');
               if ($(this).hasClass('clone')) {
                   $(this).remove();
               }
               if (filterType === 'series') {
                   var activeFilter = $(this).clone();
                   if (series_ids.indexOf(defaultFilter) > -1) {
                       series_ids = $.grep(series_ids, function(value) {
                           return value != defaultFilter;
                       });
                       series_url = series_ids.toString();
                       activeFilter.addClass('clone active-filter');
                       activeFilter.prependTo($(this).parent());
                       $(this).addClass('active-hidden');
                   } else {
                       $(this).removeClass('active-filter');
                       if (!series) {
                           series_url = defaultFilter;
                       } else {
                        
                           series_url = defaultFilter + ',' + series_ids.toString();
                       }
                   }
                   if (buildFilterUrl(speakers, series_url, years, scriptures)) {
                       $filter.attr('data-series-url', buildFilterUrl(speakers, series_url, years, scriptures));
                       activeFilter.find('a').attr('data-series-url', buildFilterUrl(speakers, series_url, years, scriptures));
                   } else {
                       $filter.attr('data-series-url', '');
                       activeFilter.find('a').attr('data-series-url', '');
                   }
               }
               if (filterType === 'auth') {
                   var activeFilter = $(this).clone();
                   if (speakers_ids.indexOf(defaultFilter) > -1) {
                       speakers_ids = $.grep(speakers_ids, function(value) {
                           return value != defaultFilter;
                       });
                       speakers_url = speakers_ids.toString();
                       activeFilter.addClass('clone active-filter');
                       activeFilter.prependTo($(this).parent());
                       $(this).addClass('active-hidden');
                   } else {
                       $(this).removeClass('active-filter');
                       if (!speakers) {
                           speakers_url = defaultFilter;
                       } else {
                           speakers_url = defaultFilter + ',' + speakers_ids.toString();
                       }
                   }
                   if (buildFilterUrl(speakers_url, series, years, scriptures)) {
                       $filter.attr('data-series-url', buildFilterUrl(speakers_url, series, years, scriptures));
                       activeFilter.find('a').attr('data-series-url', buildFilterUrl(speakers_url, series, years, scriptures));
                   } else {
                       $filter.attr('data-series-url', '');
                       activeFilter.find('a').attr('data-series-url', '');
                   }
               }
               if (filterType === 'year') {
                   var activeFilter = $(this).clone();
                   if (years_ids.indexOf(defaultFilter) > -1) {
                       years_ids = $.grep(years_ids, function(value) {
                           return value != defaultFilter;
                       });
                       years_url = years_ids.toString();
                       activeFilter.addClass('clone active-filter');
                       activeFilter.prependTo($(this).parent());
                       $(this).addClass('active-hidden');
                   } else {
                       $(this).removeClass('active-filter');
                       if (!years) {
                           years_url = defaultFilter;
                       } else {
                           years_url = defaultFilter + ',' + years_ids.toString();
                       }
                       
                   }
                   if (buildFilterUrl(speakers, series, years_url, scriptures)) {
                       $filter.attr('data-series-url', buildFilterUrl(speakers, series, years_url, scriptures));
                       activeFilter.find('a').attr('data-series-url', buildFilterUrl(speakers, series, years_url, scriptures));
                   } else {
                       $filter.attr('data-series-url', '');
                       activeFilter.find('a').attr('data-series-url', '');
                   }
               }
               if (filterType === 'scripture') {
                   var activeFilter = $(this).clone();
                   if (scriptures_ids.indexOf(defaultFilter) > -1) {
                       scriptures_ids = $.grep(scriptures_ids, function(value) {
                           return value != defaultFilter;
                       });
                       scriptures_url = scriptures_ids.toString();

                       activeFilter.addClass('clone active-filter');
                       activeFilter.prependTo($(this).parent());
                       $(this).addClass('active-hidden');
                   } else {
                       if (!scriptures) {
                           scriptures_url = defaultFilter;
                       } else {
                           scriptures_url = defaultFilter + ',' + scriptures_ids.toString();
                       }
                   }
                   if (buildFilterUrl(speakers, series, years, scriptures_url)) {
                       $filter.attr('data-series-url', buildFilterUrl(speakers, series, years, scriptures_url));
                       activeFilter.find('a').attr('data-series-url', buildFilterUrl(speakers, series, years, scriptures_url));
                   } else {
                       $filter.attr('data-series-url', '');
                       activeFilter.find('a').attr('data-series-url', '');
                   }
               }
           });
           
       }
       $('body').on('click','.filter ul li a',function(e){ 
          e.preventDefault(); 
        //   scroll.animateScroll( anchor );
          $('#header-outer').addClass('invisible');
          $('.sermon-media .search input[type="search"]').val("");
          var $target = $('.post-list');
          if ($('.filters').hasClass('search-active')) {
              $('.filters').removeClass('search-active');
              resetFilters();
          }
          $target.addClass('loading');
          var query = $(this).attr('data-series-url');
          if ($(this).attr('data-series-url') == '') {
              query = '?all=1';
          } 
           $.get(query, function (data, err) {
               $target.html(data);
               updateFilters();
               $target.removeClass('loading');
               $target.fadeIn();
           });
           return false;
       });
       
       $('body').on('click','a.clear_search',function(e){ 
           e.preventDefault(); 
           resetFilters();
           $('.sermon-media .search input[type="search"]').val("");
           var $target = $('.post-list');
           $target.addClass('loading');
           $.get('?all=1', function (data, err) {
               $target.html(data);
               $target.removeClass('loading');
               $target.fadeIn();
           });
       });
       $('body').on('click','.post-list .pagination a',function(e){ 
           scroll.animateScroll( anchor );
           $('#header-outer').addClass('invisible');
           if (!$('.filters').hasClass('search-active')) {
               e.preventDefault(); 
               var $target = $('.post-list');
               $target.addClass('loading');
               var query = $(this).attr('data-pagenumber');
               var series_url = $target.find('.series-url').attr('data-filter-url');
               var requestPref = '?';
               if (series_url) {
                  requestPref = '&';
               } else {
                  series_url = '';
               }
               $.get(series_url + requestPref + 'sermon_page=' + query, function (data, err) {
                       $target.html(data);
                       $target.removeClass('loading');
                       $target.fadeIn();
               });
               return false;
           }
       });
        $('body').on('click','.post-list .pagination a',function(e){ 
            scroll.animateScroll( anchor );
            $('#header-outer').addClass('invisible');
            if ($('.filters').hasClass('search-active')) {
               e.preventDefault(); 
               var $target = $('.post-list');
               var page_number = $(this).attr('data-pagenumber');
               $target.addClass('loading');
               var query = $('.sermon-media .search input[type="search"]').val();
                $.get('?sermonSearch='+ query +'&post_type=sermon' + '&sermon_page=' + page_number, function (data, err) {
                   $target.html(data);
                   $target.removeClass('loading');
                   $target.fadeIn();
                });
                return false;
            }
       });
       function resetFilters() {
           if ($('.filters .filter li').hasClass('active-filter')) {
               $('.filters .filter li').each(function(i) {
                   $(this).removeClass('active-hidden');
                   if ($(this).hasClass('clone')) {
                       $(this).remove();
                   }
                   var $filter = $(this).find('a'),
                       defaultFilter = $filter.attr('data-default');
                   $filter.attr('data-series-url', defaultFilter);
               });
           }
       }
       $('body').on('click','.sermon-media .search input[type="submit"]',function(e){ 
          e.preventDefault(); 
          var $target = $('.post-list');
          $('.sermon-media .filters').addClass('search-active');
          resetFilters();
          $target.addClass('loading');
          var query = $('.sermon-media .search input[type="search"]').val();
          
           $.get('?sermonSearch='+ query +'&post_type=sermon', function (data, err) {
                   $target.html(data);
                   $target.removeClass('loading');
                   $target.fadeIn();
           });
           return false;
       });
       });
});
})(jQuery);