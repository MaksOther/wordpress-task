<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AFE_Ajax_Lost_Password extends AFE_Ajax {

	protected $action = 'afe_lost_password';

	protected function process(): void {
		$login = $this->get_field( 'user_login' );

		if ( '' === $login ) {
			$this->send_error(
				__( 'Please fix the errors below.', 'auth-forms-for-elementor' ),
				[ 'user_login' => __( 'Please enter your email or username.', 'auth-forms-for-elementor' ) ]
			);
		}

		$result = retrieve_password( $login );

		if ( is_wp_error( $result ) && ! in_array( $result->get_error_code(), [ 'invalid_email', 'invalidcombo' ], true ) ) {
			$this->send_error( __( 'The email could not be sent. Please try again later or contact the site administrator.', 'auth-forms-for-elementor' ), [], 500 );
		}

		$this->send_success( __( 'If an account with these details exists, a password reset link has been sent to its email address.', 'auth-forms-for-elementor' ) );
	}
}
