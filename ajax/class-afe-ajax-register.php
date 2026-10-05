<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AFE_Ajax_Register extends AFE_Ajax {

	protected $action = 'afe_register';

	protected function process(): void {
		if ( ! afe_registration_allowed() ) {
			$this->send_error( __( 'User registration is currently not allowed.', 'auth-forms-for-elementor' ), [], 403 );
		}

		$first_name = $this->get_field( 'first_name' );
		$last_name  = $this->get_field( 'last_name' );
		$username   = $this->get_field( 'username' );
		$email      = isset( $_POST['email'] ) && is_string( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
		$password   = $this->get_password( 'password' );
		$password2  = $this->get_password( 'password_confirm' );

		$errors = $this->validate( $first_name, $last_name, $email, $username, $password, $password2 );

		if ( ! empty( $errors ) ) {
			$this->send_error( __( 'Please fix the errors below.', 'auth-forms-for-elementor' ), $errors );
		}

		$user_id = wp_create_user( $username, $password, $email );

		if ( is_wp_error( $user_id ) ) {
			$this->send_error( wp_strip_all_tags( $user_id->get_error_message() ) );
		}

		wp_update_user(
			wp_slash(
				[
					'ID'           => $user_id,
					'first_name'   => $first_name,
					'last_name'    => $last_name,
					'display_name' => trim( $first_name . ' ' . $last_name ),
					'role'         => 'subscriber',
				]
			)
		);

		wp_new_user_notification( $user_id, null, 'admin' );

		do_action( 'afe_user_registered', $user_id );

		$user = wp_signon(
			[
				'user_login'    => $username,
				'user_password' => $password,
				'remember'      => false,
			]
		);

		if ( is_wp_error( $user ) ) {
			$this->send_success( __( 'Registration complete. You can now log in.', 'auth-forms-for-elementor' ), '' );
		}

		wp_set_current_user( $user->ID );

		$redirect = apply_filters( 'afe_register_redirect', $this->get_redirect_url(), $user );

		$this->send_success( __( 'Registration complete.', 'auth-forms-for-elementor' ), $redirect );
	}

	private function validate( string $first_name, string $last_name, string $email, string $username, string $password, string $password2 ): array {
		$errors = [];

		if ( '' === $first_name ) {
			$errors['first_name'] = __( 'Please enter your first name.', 'auth-forms-for-elementor' );
		}

		if ( '' === $last_name ) {
			$errors['last_name'] = __( 'Please enter your last name.', 'auth-forms-for-elementor' );
		}

		if ( '' === $email || ! is_email( $email ) ) {
			$errors['email'] = __( 'Please enter a valid email address.', 'auth-forms-for-elementor' );
		} elseif ( email_exists( $email ) ) {
			$errors['email'] = __( 'This email address is already registered.', 'auth-forms-for-elementor' );
		}

		$illegal_logins = array_map( 'strtolower', (array) apply_filters( 'illegal_user_logins', [] ) );

		if ( '' === $username ) {
			$errors['username'] = __( 'Please enter a username.', 'auth-forms-for-elementor' );
		} elseif ( sanitize_user( $username, true ) !== $username || ! validate_username( $username ) ) {
			$errors['username'] = __( 'The username may only contain Latin letters, numbers, spaces and the characters _ . - @', 'auth-forms-for-elementor' );
		} elseif ( strlen( $username ) > 60 ) {
			$errors['username'] = __( 'The username may not be longer than 60 characters.', 'auth-forms-for-elementor' );
		} elseif ( in_array( strtolower( $username ), $illegal_logins, true ) ) {
			$errors['username'] = __( 'Sorry, that username is not allowed.', 'auth-forms-for-elementor' );
		} elseif ( username_exists( $username ) ) {
			$errors['username'] = __( 'This username is already taken.', 'auth-forms-for-elementor' );
		}

		$min_length = afe_min_password_length();

		if ( '' === $password ) {
			$errors['password'] = __( 'Please enter a password.', 'auth-forms-for-elementor' );
		} elseif ( mb_strlen( wp_unslash( $password ) ) < $min_length ) {
			$errors['password'] = sprintf(
				_n(
					'The password must be at least %d character long.',
					'The password must be at least %d characters long.',
					$min_length,
					'auth-forms-for-elementor'
				),
				$min_length
			);
		}

		if ( $password !== $password2 ) {
			$errors['password_confirm'] = __( 'The passwords do not match.', 'auth-forms-for-elementor' );
		}

		return $errors;
	}
}
