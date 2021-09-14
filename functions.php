<?php 
add_action('after_setup_theme', function() {
	$dir = dirname(__FILE__);
	$t = \Ardent\Wordpress\Theme::getInstance('ardent-elements', 'https://wordpress.updates.it.ardentcreative.com/theme/ardent-elements/download/', false, false);  	
   \Ardent\Autoloader::addPath("{$dir}/classes/");
    foreach (array('ajax', 'tasks', 'post_types', 'shortcodes', 'template', 'template/shortcode') as $file_dir) {
        foreach (glob("{$dir}/{$file_dir}/*.php") as $file) {
            require_once($file);
        }
    }
});

class My_Elementor_Widgets {
	protected static $instance = null;
	public static function get_instance() {
		if ( ! isset( static::$instance ) ) {
			static::$instance = new static;
		}

		return static::$instance;
	}

	protected function __construct() {
		$dir = dirname(__FILE__);
		foreach (array('widgets') as $file_dir) {
			foreach (glob("{$dir}/{$file_dir}/*.php") as $file) {
				require_once($file);
			}
		}
		add_action( 'elementor/widgets/widgets_registered', [ $this, 'register_widgets' ] );
	}

	public function register_widgets() {
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \Elementor\FeaturedSermon() );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \Elementor\Staff() );
	}

}

add_action( 'init', 'my_elementor_init' );
function my_elementor_init() {
	My_Elementor_Widgets::get_instance();
}