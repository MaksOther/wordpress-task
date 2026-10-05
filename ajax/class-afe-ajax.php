<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

abstract class AFE_Ajax {

	protected $action = '';

	public function __construct() {
		add_action( 'wp_ajax_nopriv_' . $this->action, [ $this, 'handle_request' ] );
		add_action( 'wp_ajax_' . $this->action, [ $this, 'handle_request' ] );
	}

	abstract protected function process(): void;

	public function handle_request(): void {
		if ( is_user_logged_in() ) {
			wp_send_json_error(
				[
					'message' => __( 'You are already logged in.', 'auth-forms-for-elementor' ),
					'reload'  => true,
				]
			);
		}

		if ( ! wp_verify_nonce( $this->get_field( 'afe_nonce' ), $this->action ) ) {
			$this->send_error( __( 'Security check failed. Please refresh the page and try again.', 'auth-forms-for-elementor' ), [], 403 );
		}

		if ( AFE_Recaptcha::is_enabled() && ! AFE_Recaptcha::verify( $this->get_field( 'afe_recaptcha' ), $this->action ) ) {
			$this->send_error( __( 'reCAPTCHA verification failed. Please try again.', 'auth-forms-for-elementor' ), [], 403 );
		}

		$this->process();
	}

	protected function get_field( string $name ): string {
		if ( ! isset( $_POST[ $name ] ) || ! is_string( $_POST[ $name ] ) ) {
			return '';
		}

		return sanitize_text_field( wp_unslash( $_POST[ $name ] ) );
	}

	protected function get_password( string $name ): string {
		if ( ! isset( $_POST[ $name ] ) || ! is_string( $_POST[ $name ] ) ) {
			return '';
		}

		return $_POST[ $name ];
	}

	protected function get_redirect_url(): string {
		if ( empty( $_POST['redirect_to'] ) || ! is_string( $_POST['redirect_to'] ) ) {
			return '';
		}

		$url = esc_url_raw( wp_unslash( $_POST['redirect_to'] ) );

		return wp_validate_redirect( $url, '' );
	}

	protected function send_success( string $message, ?string $redirect = null ): void {
		$data = [ 'message' => $message ];

		if ( null !== $redirect ) {
			$data['redirect'] = $redirect;
		}

		wp_send_json_success( $data );
	}

	protected function send_error( string $message, array $errors = [], int $status = 200 ): void {
		wp_send_json_error(
			[
				'message' => $message,
				'errors'  => (object) $errors,
			],
			$status
		);
	}
}
