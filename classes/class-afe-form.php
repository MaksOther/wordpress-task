<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AFE_Form {

	private array $settings;
	private array $defaults;
	private string $widget_id;
	private bool $is_editor;

	public function __construct( array $settings, string $widget_id, bool $is_editor = false ) {
		$this->settings  = $settings;
		$this->widget_id = $widget_id;
		$this->is_editor = $is_editor;
		$this->defaults  = afe_get_default_texts();
	}

	public function render(): void {
		if ( is_user_logged_in() && ! $this->is_editor ) {
			if ( 'hide' !== ( $this->settings['logged_in_view'] ?? 'message' ) ) {
				$this->load_template( 'logged-in' );
			}
			return;
		}

		$this->load_template( 'wrapper' );
	}

	public function is_editor(): bool {
		return $this->is_editor;
	}

	public function get_type(): string {
		$type = $this->settings['form_type'] ?? 'both';

		return in_array( $type, [ 'login', 'register', 'both' ], true ) ? $type : 'both';
	}

	public function get_panels(): array {
		$type   = $this->get_type();
		$panels = [];

		if ( 'register' !== $type ) {
			$panels[] = 'login';
		}

		if ( 'login' !== $type ) {
			$panels[] = 'register';
		}

		if ( 'register' !== $type && $this->is_enabled( 'show_lost_password' ) ) {
			$panels[] = 'lost';
		}

		return $panels;
	}

	public function get_active_panel(): string {
		$type = $this->get_type();

		if ( 'both' !== $type ) {
			return $type;
		}

		return 'register' === ( $this->settings['default_tab'] ?? 'login' ) ? 'register' : 'login';
	}

	public function is_enabled( string $key ): bool {
		return 'yes' === ( $this->settings[ $key ] ?? '' );
	}

	public function get_text( string $key ): string {
		$value = $this->settings[ $key ] ?? null;

		return is_string( $value ) ? $value : ( $this->defaults[ $key ] ?? '' );
	}

	public function get_id( string $suffix ): string {
		return 'afe-' . $this->widget_id . '-' . $suffix;
	}

	public function get_redirect( string $key ): string {
		$url = $this->settings[ $key ]['url'] ?? '';

		return is_string( $url ) ? $url : '';
	}

	public function render_hidden_fields( string $action, string $redirect_key = '' ): void {
		printf( '<input type="hidden" name="action" value="%s">', esc_attr( $action ) );
		printf( '<input type="hidden" name="afe_nonce" value="%s">', esc_attr( wp_create_nonce( $action ) ) );

		if ( '' !== $redirect_key ) {
			printf( '<input type="hidden" name="redirect_to" value="%s">', esc_url( $this->get_redirect( $redirect_key ) ) );
		}
	}

	public function render_heading( string $text_key ): void {
		$text = $this->get_text( $text_key );

		if ( '' !== $text ) {
			printf( '<h3 class="afe__heading">%s</h3>', esc_html( $text ) );
		}
	}

	public function render_button( string $text_key ): void {
		printf(
			'<button type="submit" class="afe__button"><span class="afe__button-text">%s</span><span class="afe__spinner" aria-hidden="true"></span></button>',
			esc_html( $this->get_text( $text_key ) )
		);
	}

	public function render_field( array $args ): void {
		$type        = $args['type'] ?? 'text';
		$id          = $this->get_id( $args['form'] . '-' . $args['name'] );
		$label       = $this->get_text( $args['label'] );
		$show_label  = '' !== $label && $this->is_enabled( 'show_labels' );
		$has_toggle  = 'password' === $type && $this->is_enabled( 'show_password_toggle' );
		$plain_input = in_array( $type, [ 'password', 'email' ], true ) || in_array( $args['name'], [ 'user_login', 'username' ], true );
		?>
		<div class="afe__field<?php echo $has_toggle ? ' afe__field--has-toggle' : ''; ?>" data-afe-field="<?php echo esc_attr( $args['name'] ); ?>">
			<?php if ( $show_label ) : ?>
				<label class="afe__label" for="<?php echo esc_attr( $id ); ?>">
					<?php echo esc_html( $label ); ?>
					<?php if ( $this->is_enabled( 'show_required_mark' ) ) : ?>
						<span class="afe__required" aria-hidden="true">*</span>
					<?php endif; ?>
				</label>
			<?php endif; ?>
			<div class="afe__control">
				<input
					class="afe__input"
					type="<?php echo esc_attr( $type ); ?>"
					id="<?php echo esc_attr( $id ); ?>"
					name="<?php echo esc_attr( $args['name'] ); ?>"
					placeholder="<?php echo esc_attr( $this->get_text( $args['placeholder'] ) ); ?>"
					autocomplete="<?php echo esc_attr( $args['autocomplete'] ?? 'off' ); ?>"
					aria-describedby="<?php echo esc_attr( $id . '-error' ); ?>"
					<?php if ( ! $show_label ) : ?>
						aria-label="<?php echo esc_attr( '' !== $label ? $label : ( $this->defaults[ $args['label'] ] ?? '' ) ); ?>"
					<?php endif; ?>
					<?php if ( $plain_input ) : ?>
						autocapitalize="none" spellcheck="false"
					<?php endif; ?>
					required
				>
				<?php if ( $has_toggle ) : ?>
					<button
						type="button"
						class="afe__toggle"
						data-afe-toggle
						data-afe-show-label="<?php echo esc_attr( $this->get_text( 'show_password_text' ) ); ?>"
						data-afe-hide-label="<?php echo esc_attr( $this->get_text( 'hide_password_text' ) ); ?>"
						aria-label="<?php echo esc_attr( $this->get_text( 'show_password_text' ) ); ?>"
						aria-pressed="false"
					>
						<svg class="afe__icon afe__icon--show" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z"/><circle cx="12" cy="12" r="3"/></svg>
						<svg class="afe__icon afe__icon--hide" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M17.94 17.94A10.94 10.94 0 0 1 12 19c-7 0-11-7-11-7a19.77 19.77 0 0 1 5.06-5.94M9.9 4.24A10.94 10.94 0 0 1 12 4c7 0 11 7 11 7a19.86 19.86 0 0 1-3.17 4.19M14.12 14.12a3 3 0 1 1-4.24-4.24"/><path d="M1 1l22 22"/></svg>
					</button>
				<?php endif; ?>
			</div>
			<p class="afe__error" id="<?php echo esc_attr( $id . '-error' ); ?>" hidden></p>
		</div>
		<?php
	}

	private function load_template( string $name ): void {
		include AFE_PATH . 'includes/templates/' . $name . '.php';
	}
}
