<?php

require_once( 'lib/sitepoint-map-widget.php' );

define('GOOGLE_MAP_API_KEY', '<your-api-key-here>');

add_action( 'wp_enqueue_scripts', 'sp_theme_enqueue_styles' );
function sp_theme_enqueue_styles() {
	wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
	wp_enqueue_script( 'polymer', get_stylesheet_directory_uri() . '/bower_components/webcomponentsjs/webcomponents-lite.js' );
}

add_action( 'wp_head', 'include_polymer_elements' );
function include_polymer_elements() {
	?>

  <link rel="import"
        href="<?php echo get_stylesheet_directory_uri() ?>/bower_components/polymer/polymer.html">
  <link rel="import"
        href="<?php echo get_stylesheet_directory_uri() ?>/webcomponents/index.html">

	<?php
}

add_action( 'widgets_init', 'sp_register_widgets' );
function sp_register_widgets() {
	register_widget( 'SitepointMapWidget' );
}
