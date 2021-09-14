<?php
namespace Elementor;

class Staff extends Widget_Base {
	
	public function get_name() {
		return 'staff-listing';
	}
	
	public function get_title() {
		return 'Staff Listing';
	}
	
	public function get_icon() {
		return 'fa fa-users';
	}
	
	public function get_categories() {
		return [ 'ardent' ];
	}
	
	protected function _register_controls() {
		$this->start_controls_section(
			'section_title',
			[
				'label' => __( 'Content', 'Staff Listing' ),
			]
		);
		
		$this->add_control(
			'posts_per_page',
			[
				'label' => __( 'Posts Per Page', 'plugin-domain' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => __( -1, 'plugin-domain' ),
				'placeholder' => __( 'Input number of posts', 'plugin-domain' ),
			]
		);
		
		$this->add_control(
			'terms',
			[
				'label' => __( 'Category Type', 'plugin-domain' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => __( ' ', 'plugin-domain' ),
				'placeholder' => __( 'Category Type', 'plugin-domain' ),
			]
        );


		$this->end_controls_section();
	}
	
	protected function render() { 
		$settings = $this->get_settings_for_display();
		$args = array(
			'post_type' => 'staff',
			'posts_per_page' => isset($settings['posts_per_page'])?(int)$settings['posts_per_page']:-1,
			'orderby'   => 'meta_value',
			'meta_key'  => 'last_name',  
			'order'   => 'ASC'
		);
		$qo = get_queried_object();
		if(isset($qo->taxonomy) && $qo->taxonomy == 'staff_category'){
			$args['tax_query'] = [
				[
					'taxonomy' => $qo->taxonomy,
					'field' => 'slug',
					'terms' => $qo->slug
				]
			];
		}else if(isset($settings['terms']) && $settings['terms']) {
			$args['tax_query'] = [
				[
					'taxonomy'=> 'staff_category',
					'field'=> 'slug',
					'terms' => explode(',', $settings['terms']),
				]
			];
		}

		$the_query = new \WP_Query($args);
		?>
	

		<section data-particle_enable="false" data-particle-mobile-disabled="false" class="elementor-element elementor-element-69afa4f5 elementor-section-boxed elementor-section-height-default elementor-section-height-default elementor-section elementor-inner-section" data-id="69afa4f5" data-element_type="section">
	<?php $i == 0; while ($the_query->have_posts()) { $the_query->the_post() ; ?>                                
						<?php if($i == 0) {  echo '<div class="elementor-container elementor-column-gap-default"><div class="elementor-row"> '; } 
								?>
							<?php get_template_part('templates/shortcode/staff_listing/item-staff'); ?> 
					
					<?php $i++;
						if($i == 3) {
							$i = 0;
							echo '</div></div>';
						}
					?>
						<?php } wp_reset_query(); ?>         
				</div>
			</div>            
	</section>

	<?php }	
	protected function _content_template() {
    }
}
