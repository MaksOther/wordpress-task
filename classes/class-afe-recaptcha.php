<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AFE_Recaptcha {

	private const VERIFY_URL = 'https://www.google.com/recaptcha/api/siteverify';

	public static function is_enabled(): bool {
		$settings = afe_get_settings();

		return '' !== $settings['recaptcha_site_key'] && '' !== $settings['recaptcha_secret_key'];
	}

	public static function site_key(): string {
		return self::is_enabled() ? afe_get_settings()['recaptcha_site_key'] : '';
	}

	public static function verify( string $token, string $action ): bool {
		if ( '' === $token ) {
			return false;
		}

		$settings = afe_get_settings();
		$response = wp_remote_post(
			self::VERIFY_URL,
			[
				'timeout' => 10,
				'body'    => [
					'secret'   => $settings['recaptcha_secret_key'],
					'response' => $token,
				],
			]
		);

		if ( is_wp_error( $response ) || 200 !== (int) wp_remote_retrieve_response_code( $response ) ) {
			return false;
		}

		$body = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( ! is_array( $body ) || empty( $body['success'] ) ) {
			return false;
		}

		if ( isset( $body['action'] ) && $body['action'] !== $action ) {
			return false;
		}

		return (float) ( $body['score'] ?? 0 ) >= $settings['recaptcha_threshold'];
	}
}
