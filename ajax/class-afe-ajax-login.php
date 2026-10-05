<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AFE_Ajax_Login extends AFE_Ajax {

	protected $action = 'afe_login';

	protected function process(): void {
		$login    = $this->get_field( 'user_login' );
		$password = $this->get_password( 'password' );
		$remember = ! empty( $_POST['remember'] );
		$errors   = [];

		if ( '' === $login ) {
			$errors['user_login'] = __( 'Please enter your email or username.', 'auth-forms-for-elementor' );
		}

		if ( '' === $password ) {
			$errors['password'] = __( 'Please enter your password.', 'auth-forms-for-elementor' );
		}

		if ( ! empty( $errors ) ) {
			$this->send_error( __( 'Please fix the errors below.', 'auth-forms-for-elementor' ), $errors );
		}

		$user = wp_signon(
			[
				'user_login'    => $login,
				'user_password' => $password,
				'remember'      => $remember,
			]
		);

		if ( is_wp_error( $user ) ) {
			$code = $user->get_error_code();

			if ( in_array( $code, [ 'invalid_username', 'invalid_email', 'incorrect_password', 'invalidcombo' ], true ) ) {
				$this->send_error( __( 'Invalid login or password.', 'auth-forms-for-elementor' ) );
			}

			$this->send_error( wp_strip_all_tags( $user->get_error_message() ) );
		}

		wp_set_current_user( $user->ID );

		$redirect = apply_filters( 'afe_login_redirect', $this->get_redirect_url(), $user );

		$this->send_success( __( 'You have logged in successfully.', 'auth-forms-for-elementor' ), $redirect );
	}
}
