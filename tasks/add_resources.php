<?php
add_action('wp_enqueue_scripts', function() {
	$ptdata = wp_get_theme('ardent-elements');
	$tdata = wp_get_theme();
	wp_enqueue_style('ardent-elements', get_template_directory_uri() . '/style.css', array(), $ptdata['Version']); 
	wp_enqueue_style('font-awesome', get_stylesheet_directory_uri() . '/font-awesome.min.css', array(), $tdata['Version']); 
	wp_enqueue_style('lightcase', get_stylesheet_directory_uri() . '/css/lightcase.css', array(), $tdata['Version']); 
	wp_enqueue_style('ae-child', get_stylesheet_directory_uri() . '/style.css', array(), $tdata['Version']); 
	wp_enqueue_style('custom', get_stylesheet_directory_uri() . '/css/custom.css', array(), $tdata['Version']);
	wp_enqueue_script('smooth-scroll', get_stylesheet_directory_uri() . '/js/smooth-scroll.js', array('jquery'), $tdata['Version']);
	wp_enqueue_script('lightcase', get_stylesheet_directory_uri() . '/js/lightcase.js', array('jquery'), $tdata['Version']);
	wp_enqueue_script('ardent-custom', get_stylesheet_directory_uri() . '/js/custom.js', array('jquery'), $tdata['Version']);	
	
}, 50);