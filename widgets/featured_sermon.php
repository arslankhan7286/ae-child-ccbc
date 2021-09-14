<?php

namespace Elementor;

class FeaturedSermon extends Widget_Base
{

	public function get_name()
	{
		return 'featured-sermon';
	}

	public function get_title()
	{
		return 'Featured Sermon';
	}

	public function get_icon()
	{
		return 'fa fa-users';
	}

	public function get_categories()
	{
		return ['ardent'];
	}

	protected function _register_controls()
	{
		$this->start_controls_section(
			'section_title',
			[
				'label' => __('Content', 'Featured Sermon'),
			]
		);

		$this->end_controls_section();
	}

	protected function render()
	{
		$settings = $this->get_settings_for_display();
		$args = array(
			'post_type' => 'sermon',
			'posts_per_page' => 1,
			'orderby' => 'ID',
			'order' => 'DESC',
			'post_status' => 'publish',
			'tax_query' => array(
				array(
					'taxonomy' => 'sermon_category',
					'field' => 'slug',
					'terms' => 'sermons',
				)
			)
		);

		$the_query = new \WP_Query($args);
?>
		<div class="featured_sermon_container">
			<div class="background-overlay"></div>
			<?php if ($the_query->have_posts()) { ?>
				<?php while ($the_query->have_posts()) {
					$the_query->the_post(); ?>
					<?php
					$term = get_the_terms(get_the_ID(), 'sermon_category');
					$config = \Ardent\Ccbc\Sermons::getConfig();
					$bgImageID = \Ardent\Ccbc\Sermons\Term::getTermBGImage($term[0]->term_id);
					$bgImage = wp_get_attachment_image_src(($bgImageID ?: $config->header_image), 'large');
					?>
					<div class="featured_sermon_item sermon-media" style="background-image: url(<?php echo $bgImage[0]; ?>);">
						<div class="container-wrap">
							<?php get_template_part('templates/shortcode/featured_sermon'); ?>
						</div>
					</div>
				<?php } ?>
			<?php } ?>
		</div>

<?php }
	protected function _content_template()
	{
	}
}
