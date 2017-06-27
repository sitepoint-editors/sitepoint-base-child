<?php
// lib/sitepoint-map-widget.php

class SitepointMapWidget extends WP_Widget {

	function __construct() {
		// Instantiate the parent object
		parent::__construct( false, 'Google Map' );
	}

	function widget( $args, $instance ) {
		echo '<sitepoint-map client-id="' . GOOGLE_MAP_API_KEY . '"></sitepoint-map>';
	}
}
