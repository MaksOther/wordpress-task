<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AFE_Assets {

	public const HANDLE = 'afe-auth-forms';

	public function register(): void {
		add_action( 'wp_enqueue_scripts', [ $this, 'register_assets' ] );
	}

	public function register_assets(): void {
		$deps = [];

		$is_preview = isset( $_GET['elementor-preview'] );

		if ( AFE_Recaptcha::is_enabled() && ! $is_preview ) {
			wp_register_script(
				'afe-recaptcha',
				add_query_arg( 'render', rawurlencode( AFE_Recaptcha::site_key() ), 'https://www.google.com/recaptcha/api.js' ),
				[],
				null,
				true
			);
			$deps[] = 'afe-recaptcha';
		}

		wp_register_style( self::HANDLE, AFE_URL . 'assets/css/auth-forms.css', [], AFE_VERSION );
		wp_register_script( self::HANDLE, AFE_URL . 'assets/js/auth-forms.js', $deps, AFE_VERSION, true );

		wp_localize_script(
			self::HANDLE,
			'afeData',
			[
				'ajaxUrl'          => admin_url( 'admin-ajax.php' ),
				'recaptchaSiteKey' => $is_preview ? '' : AFE_Recaptcha::site_key(),
				'i18n'             => [
					'error'   => __( 'Something went wrong. Please try again.', 'auth-forms-for-elementor' ),
					'preview' => __( 'Forms are not submitted in the editor preview.', 'auth-forms-for-elementor' ),
				],
			]
		);
	}
}
