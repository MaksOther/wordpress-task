<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AFE_Plugin {

	private static $instance = null;

	public static function instance(): self {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function init(): void {
		add_action( 'init', [ $this, 'load_textdomain' ], 0 );

		$settings = new AFE_Settings();
		$settings->register();

		if ( ! did_action( 'elementor/loaded' ) ) {
			add_action( 'admin_notices', [ $this, 'notice_missing_elementor' ] );
			return;
		}

		if ( version_compare( ELEMENTOR_VERSION, AFE_MIN_ELEMENTOR_VERSION, '<' ) ) {
			add_action( 'admin_notices', [ $this, 'notice_old_elementor' ] );
			return;
		}

		$assets = new AFE_Assets();
		$assets->register();

		new AFE_Ajax_Login();
		new AFE_Ajax_Register();
		new AFE_Ajax_Lost_Password();

		add_action( 'elementor/widgets/register', [ $this, 'register_widget' ] );
	}

	public function load_textdomain(): void {
		load_plugin_textdomain( 'auth-forms-for-elementor', false, dirname( plugin_basename( AFE_FILE ) ) . '/languages' );
	}

	public function register_widget( $widgets_manager ): void {
		require_once AFE_PATH . 'widgets/class-afe-widget.php';

		$widgets_manager->register( new AFE_Widget() );
	}

	public function notice_missing_elementor(): void {
		$this->show_notice( __( 'Auth Forms for Elementor requires Elementor to be installed and activated.', 'auth-forms-for-elementor' ) );
	}

	public function notice_old_elementor(): void {
		$this->show_notice(
			sprintf(
				__( 'Auth Forms for Elementor requires Elementor version %s or newer.', 'auth-forms-for-elementor' ),
				AFE_MIN_ELEMENTOR_VERSION
			)
		);
	}

	private function show_notice( string $message ): void {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}

		echo '<div class="notice notice-warning"><p>' . esc_html( $message ) . '</p></div>';
	}
}
