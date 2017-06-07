<?php

require_once( 'lib/google-ajax-login.php' );
require_once( 'lib/sitepoint-login-widget.php' );

add_action( 'wp_enqueue_scripts', 'sp_theme_enqueue_styles' );
function sp_theme_enqueue_styles() {
	wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
	wp_enqueue_style( 'polymer', get_stylesheet_directory_uri() . '/bower_components/webcomponentsjs/webcomponents-lite.min.js' );
}

add_action( 'wp_head', 'include_polymer_elements' );
function include_polymer_elements() {
	?>
  <link rel="import"
        href="<?php echo get_stylesheet_directory_uri() ?>/bower_components/polymer/polymer.html">
  <link rel="import"
        href="<?php echo get_stylesheet_directory_uri() ?>/webcomponents/sitepoint-login.html">
	<?php
}



add_action( 'widgets_init', 'sp_register_widgets' );
function sp_register_widgets() {
	register_widget( 'SitepointLoginWidget' );
}