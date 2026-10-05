<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function afe_get_settings(): array {
	$saved = get_option( 'afe_settings', [] );
	$saved = is_array( $saved ) ? $saved : [];

	return [
		'recaptcha_site_key'   => (string) ( $saved['recaptcha_site_key'] ?? '' ),
		'recaptcha_secret_key' => (string) ( $saved['recaptcha_secret_key'] ?? '' ),
		'recaptcha_threshold'  => (float) ( $saved['recaptcha_threshold'] ?? 0.5 ),
	];
}

function afe_registration_allowed(): bool {
	return (bool) apply_filters( 'afe_registration_allowed', (bool) get_option( 'users_can_register' ) );
}

function afe_min_password_length(): int {
	return max( 1, (int) apply_filters( 'afe_min_password_length', 8 ) );
}

function afe_field_texts( string $key, string $name ): array {
	return [
		$key . '_label'       => [
			'label'   => sprintf( __( '%s: label', 'auth-forms-for-elementor' ), $name ),
			'default' => $name,
		],
		$key . '_placeholder' => [
			'label'   => sprintf( __( '%s: placeholder', 'auth-forms-for-elementor' ), $name ),
			'default' => '',
		],
	];
}

function afe_get_texts(): array {
	static $texts = null;

	if ( null !== $texts ) {
		return $texts;
	}

	$login_or_email = __( 'Email or Username', 'auth-forms-for-elementor' );
	$password       = __( 'Password', 'auth-forms-for-elementor' );
	$first_name     = __( 'First Name', 'auth-forms-for-elementor' );
	$last_name      = __( 'Last Name', 'auth-forms-for-elementor' );
	$email          = __( 'Email', 'auth-forms-for-elementor' );
	$username       = __( 'Username', 'auth-forms-for-elementor' );
	$confirm        = __( 'Confirm Password', 'auth-forms-for-elementor' );

	$tab_title   = __( 'Tab title', 'auth-forms-for-elementor' );
	$heading     = __( 'Heading', 'auth-forms-for-elementor' );
	$button_text = __( 'Button text', 'auth-forms-for-elementor' );
	$success     = __( 'Success message', 'auth-forms-for-elementor' );

	$texts = [
		'login'    => [
			'title'     => __( 'Login form texts', 'auth-forms-for-elementor' ),
			'condition' => [ 'form_type' => [ 'login', 'both' ] ],
			'fields'    => array_merge(
				[
					'login_tab_title' => [
						'label'   => $tab_title,
						'default' => __( 'Log In', 'auth-forms-for-elementor' ),
					],
					'login_heading'   => [
						'label'   => $heading,
						'default' => '',
					],
				],
				afe_field_texts( 'login_user', $login_or_email ),
				afe_field_texts( 'login_password', $password ),
				[
					'login_remember_label' => [
						'label'   => __( 'Remember Me text', 'auth-forms-for-elementor' ),
						'default' => __( 'Remember Me', 'auth-forms-for-elementor' ),
					],
					'login_lost_link'      => [
						'label'   => __( 'Forgot password link', 'auth-forms-for-elementor' ),
						'default' => __( 'Forgot password?', 'auth-forms-for-elementor' ),
					],
					'login_button'         => [
						'label'   => $button_text,
						'default' => __( 'Log In', 'auth-forms-for-elementor' ),
					],
					'login_success'        => [
						'label'   => $success,
						'default' => __( 'You have logged in successfully. Redirecting…', 'auth-forms-for-elementor' ),
					],
				]
			),
		],
		'register' => [
			'title'     => __( 'Registration form texts', 'auth-forms-for-elementor' ),
			'condition' => [ 'form_type' => [ 'register', 'both' ] ],
			'fields'    => array_merge(
				[
					'register_tab_title' => [
						'label'   => $tab_title,
						'default' => __( 'Register', 'auth-forms-for-elementor' ),
					],
					'register_heading'   => [
						'label'   => $heading,
						'default' => '',
					],
				],
				afe_field_texts( 'first_name', $first_name ),
				afe_field_texts( 'last_name', $last_name ),
				afe_field_texts( 'email', $email ),
				afe_field_texts( 'username', $username ),
				afe_field_texts( 'register_password', $password ),
				afe_field_texts( 'confirm', $confirm ),
				[
					'register_button'          => [
						'label'   => $button_text,
						'default' => __( 'Register', 'auth-forms-for-elementor' ),
					],
					'register_success'         => [
						'label'   => $success,
						'default' => __( 'Registration complete. Redirecting…', 'auth-forms-for-elementor' ),
					],
					'registration_closed_text' => [
						'label'   => __( 'Registration closed message', 'auth-forms-for-elementor' ),
						'default' => __( 'User registration is currently not allowed.', 'auth-forms-for-elementor' ),
					],
				]
			),
		],
		'lost'     => [
			'title'     => __( 'Forgot password form texts', 'auth-forms-for-elementor' ),
			'condition' => [
				'form_type'          => [ 'login', 'both' ],
				'show_lost_password' => 'yes',
			],
			'fields'    => array_merge(
				[
					'lost_heading'     => [
						'label'   => $heading,
						'default' => __( 'Reset password', 'auth-forms-for-elementor' ),
					],
					'lost_description' => [
						'label'   => __( 'Description', 'auth-forms-for-elementor' ),
						'default' => __( 'Enter your email or username and we will send you a link to reset your password.', 'auth-forms-for-elementor' ),
						'type'    => 'textarea',
					],
				],
				afe_field_texts( 'lost_user', $login_or_email ),
				[
					'lost_button'    => [
						'label'   => $button_text,
						'default' => __( 'Send reset link', 'auth-forms-for-elementor' ),
					],
					'lost_back_link' => [
						'label'   => __( 'Back link', 'auth-forms-for-elementor' ),
						'default' => __( 'Back to login', 'auth-forms-for-elementor' ),
					],
					'lost_success'   => [
						'label'   => $success,
						'default' => __( 'If an account with these details exists, a password reset link has been sent to its email address.', 'auth-forms-for-elementor' ),
						'type'    => 'textarea',
					],
				]
			),
		],
		'misc'     => [
			'title'  => __( 'Other texts', 'auth-forms-for-elementor' ),
			'fields' => [
				'show_password_text' => [
					'label'   => __( 'Show password (button label)', 'auth-forms-for-elementor' ),
					'default' => __( 'Show password', 'auth-forms-for-elementor' ),
				],
				'hide_password_text' => [
					'label'   => __( 'Hide password (button label)', 'auth-forms-for-elementor' ),
					'default' => __( 'Hide password', 'auth-forms-for-elementor' ),
				],
				'logged_in_text'     => [
					'label'       => __( 'Logged-in message', 'auth-forms-for-elementor' ),
					'default'     => __( 'You are logged in as %s.', 'auth-forms-for-elementor' ),
					'description' => __( '%s is replaced with the user name.', 'auth-forms-for-elementor' ),
				],
				'logout_text'        => [
					'label'   => __( 'Log out link', 'auth-forms-for-elementor' ),
					'default' => __( 'Log out', 'auth-forms-for-elementor' ),
				],
			],
		],
	];

	return $texts;
}

function afe_get_default_texts(): array {
	$defaults = [];

	foreach ( afe_get_texts() as $section ) {
		foreach ( $section['fields'] as $key => $field ) {
			$defaults[ $key ] = $field['default'];
		}
	}

	return $defaults;
}
