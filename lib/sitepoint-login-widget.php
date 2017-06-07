<?php

class SitepointLoginWidget extends WP_Widget {

	function __construct() {
		// Instantiate the parent object
		parent::__construct( false, 'Firebase Login' );
	}

	function widget( $args, $instance ) {
		$isSignedIn = is_user_logged_in() ? "true" : "false";
		echo '<sitepoint-login is-signed-in="' . $isSignedIn . '"></sitepoint-login>';
	}
}
