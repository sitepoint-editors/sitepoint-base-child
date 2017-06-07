<?php
require_once 'vendor/autoload.php';

class GoogleAjaxLogin {
	public static function verify_token() {

		$idTokenString = sanitize_text_field( $_POST['id_token'] );

		check_ajax_referer( 'sitepoint-login-widget-1337', 'nonce' );

		try {
			$client = new Google_Client(['client_id' => '559275657764-9u8r9337bksddqa37os9p81ev5f8sc1i.apps.googleusercontent.com']);
			$payload = $client->verifyIdToken($idTokenString);
			if ($payload) {
				$userid = $payload['sub'];
			} else {
				throw new Exception("Invalid token");
			}
		} catch (Exception $e) {
			echo $e->getMessage();
			wp_die();
		}

		$google_id = $userid;
		$email = $payload['email'];
		$name = $payload['name'];
		$name = explode(" ", $name);


		$user = GoogleAjaxLogin::match_wp_user_to_uid($google_id, $email);


		if ( $user ) {
			GoogleAjaxLogin::login_user($user->ID );
			do_action( 'wp_login', $user->user_login, $user );
		} else {
			$password = wp_generate_password(12);

			$user_data = array(
				'user_login' => $email,
				'user_email' => $email,
				'user_pass'  => $password,
				'first_name' => $name[0],
				'last_name'  => $name[1],
			);

			$user_id = GoogleAjaxLogin::complete_registration($user_data);
			GoogleAjaxLogin::link_account($user_id, $google_id);
		}
		echo json_encode(["success" => true, "user" => $user_id]);
		wp_die();
	}

	public static function match_wp_user_to_uid($google_uid, $email) {
		global $wpdb;
		$usermeta_table = $wpdb->usermeta;
		$query_string = "SELECT $usermeta_table.user_id FROM $usermeta_table WHERE $usermeta_table.meta_key = 'sp_identity' AND $usermeta_table.meta_value LIKE '%" . $google_uid . "%'";
		$query_result = $wpdb->get_var($query_string);
		$user = get_user_by('id', $query_result);

		if ( ! $user ) {
			$user = get_user_by('email', $email);
			GoogleAjaxLogin::link_account($user->ID, $google_uid);
		}

		if ( ! $user ) {
			return false;
		}

		return $user;
	}

	public static function link_account( $wp_id, $google_uid ) {
		add_user_meta( $wp_id, 'sp_identity', $google_uid );
	}

	public static function login_user( $user_id ) {
		wp_set_current_user( $user_id );
		$secure_cookie = is_ssl() ? true : false;
		wp_set_auth_cookie( $user_id, true, $secure_cookie );
	}

	public static function logout_user() {
		check_ajax_referer( 'sitepoint-login-widget-1337', 'nonce' );
		wp_logout();
		wp_die();
	}

	public static function complete_registration( $data ) {
		$user_data = array(
			'user_login' => $data['email'],
			'user_email' => $data['email'],
			'user_pass'  => $data['password'],
			'first_name' => $data['first_name'],
			'last_name'  => $data['last_name'],
		);
		$user_id   = wp_insert_user( $user_data );

		do_action( 'register_new_user', $user_id );

		return $user_id;
	}

}
add_action( 'wp_ajax_verify_token', array('GoogleAjaxLogin', 'verify_token') );
add_action( 'wp_ajax_nopriv_verify_token', array('GoogleAjaxLogin', 'verify_token') );

add_action( 'wp_ajax_logout_user', array('GoogleAjaxLogin', 'logout_user') );

add_action( 'wp_enqueue_scripts', 'sp_variables' );
function sp_variables() {

	wp_enqueue_script( 'ajax-script', get_stylesheet_directory_uri() . '/variables.js' );

	$ajax_nonce = wp_create_nonce( 'sitepoint-login-widget-1337' );

	wp_localize_script( 'ajax-script', 'ajax',
		array(
			'url'   => admin_url( 'admin-ajax.php' ),
			'nonce' => $ajax_nonce
		)
	);
}