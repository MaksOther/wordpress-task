<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AFE_Settings {

	public const OPTION = 'afe_settings';
	private const PAGE  = 'afe-settings';
	private const GROUP = 'afe_settings_group';

	public function register(): void {
		add_action( 'admin_menu', [ $this, 'add_page' ] );
		add_action( 'admin_init', [ $this, 'register_settings' ] );
		add_filter( 'plugin_action_links_' . plugin_basename( AFE_FILE ), [ $this, 'action_links' ] );
	}

	public function add_page(): void {
		add_options_page(
			__( 'Auth Forms for Elementor', 'auth-forms-for-elementor' ),
			__( 'Auth Forms', 'auth-forms-for-elementor' ),
			'manage_options',
			self::PAGE,
			[ $this, 'render_page' ]
		);
	}

	public function register_settings(): void {
		register_setting(
			self::GROUP,
			self::OPTION,
			[
				'type'              => 'array',
				'sanitize_callback' => [ $this, 'sanitize' ],
				'default'           => [],
			]
		);

		add_settings_section(
			'afe_recaptcha',
			__( 'Google reCAPTCHA v3', 'auth-forms-for-elementor' ),
			[ $this, 'render_section' ],
			self::PAGE
		);

		$fields = [
			'recaptcha_site_key'   => __( 'Site key', 'auth-forms-for-elementor' ),
			'recaptcha_secret_key' => __( 'Secret key', 'auth-forms-for-elementor' ),
			'recaptcha_threshold'  => __( 'Score threshold', 'auth-forms-for-elementor' ),
		];

		foreach ( $fields as $key => $label ) {
			add_settings_field(
				$key,
				$label,
				[ $this, 'render_field' ],
				self::PAGE,
				'afe_recaptcha',
				[
					'key'       => $key,
					'label_for' => 'afe-' . $key,
				]
			);
		}
	}

	public function sanitize( $input ): array {
		$input     = is_array( $input ) ? $input : [];
		$threshold = is_numeric( $input['recaptcha_threshold'] ?? null ) ? (float) $input['recaptcha_threshold'] : 0.5;

		return [
			'recaptcha_site_key'   => $this->sanitize_key_value( $input['recaptcha_site_key'] ?? '' ),
			'recaptcha_secret_key' => $this->sanitize_key_value( $input['recaptcha_secret_key'] ?? '' ),
			'recaptcha_threshold'  => min( 1.0, max( 0.0, $threshold ) ),
		];
	}

	private function sanitize_key_value( $value ): string {
		return is_string( $value ) ? sanitize_text_field( $value ) : '';
	}

	public function render_section(): void {
		echo '<p>' . esc_html__( 'Fill in both keys to protect the login, registration and password reset forms. Leave them empty to disable reCAPTCHA.', 'auth-forms-for-elementor' ) . '</p>';
	}

	public function render_field( array $args ): void {
		$key   = $args['key'];
		$value = afe_get_settings()[ $key ] ?? '';
		$name  = self::OPTION . '[' . $key . ']';

		if ( 'recaptcha_threshold' === $key ) {
			printf(
				'<input type="number" id="%1$s" name="%2$s" value="%3$s" min="0" max="1" step="0.1" class="small-text">',
				esc_attr( $args['label_for'] ),
				esc_attr( $name ),
				esc_attr( (string) $value )
			);
			echo '<p class="description">' . esc_html__( 'Requests scoring below this value (0–1) are rejected. Default: 0.5.', 'auth-forms-for-elementor' ) . '</p>';
			return;
		}

		printf(
			'<input type="%1$s" id="%2$s" name="%3$s" value="%4$s" class="regular-text" autocomplete="off">',
			'recaptcha_secret_key' === $key ? 'password' : 'text',
			esc_attr( $args['label_for'] ),
			esc_attr( $name ),
			esc_attr( (string) $value )
		);
	}

	public function render_page(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		?>
		<div class="wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
			<form method="post" action="options.php">
				<?php
				settings_fields( self::GROUP );
				do_settings_sections( self::PAGE );
				submit_button();
				?>
			</form>
		</div>
		<?php
	}

	public function action_links( array $links ): array {
		array_unshift(
			$links,
			sprintf(
				'<a href="%s">%s</a>',
				esc_url( admin_url( 'options-general.php?page=' . self::PAGE ) ),
				esc_html__( 'Settings', 'auth-forms-for-elementor' )
			)
		);

		return $links;
	}
}
