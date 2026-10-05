<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! afe_registration_allowed() ) {
	?>
	<div class="afe__message afe__message--info">
		<?php echo esc_html( $this->get_text( 'registration_closed_text' ) ); ?>
		<?php if ( current_user_can( 'manage_options' ) ) : ?>
			<a class="afe__link" href="<?php echo esc_url( admin_url( 'options-general.php' ) ); ?>">
				<?php esc_html_e( 'Enable “Anyone can register” in Settings → General.', 'auth-forms-for-elementor' ); ?>
			</a>
		<?php endif; ?>
	</div>
	<?php
	if ( ! $this->is_editor() ) {
		return;
	}
}
?>
<form
	class="afe__form"
	method="post"
	action="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>"
	novalidate
	data-afe-form="register"
	data-afe-success="<?php echo esc_attr( $this->get_text( 'register_success' ) ); ?>"
>
	<?php
	$this->render_hidden_fields( 'afe_register', 'redirect_register' );
	$this->render_heading( 'register_heading' );
	?>
	<div class="afe__message" role="alert" hidden></div>
	<div class="afe__grid">
		<?php
		$this->render_field(
			[
				'form'         => 'register',
				'name'         => 'first_name',
				'label'        => 'first_name_label',
				'placeholder'  => 'first_name_placeholder',
				'autocomplete' => 'given-name',
			]
		);
		$this->render_field(
			[
				'form'         => 'register',
				'name'         => 'last_name',
				'label'        => 'last_name_label',
				'placeholder'  => 'last_name_placeholder',
				'autocomplete' => 'family-name',
			]
		);
		?>
	</div>
	<?php
	$this->render_field(
		[
			'form'         => 'register',
			'name'         => 'email',
			'type'         => 'email',
			'label'        => 'email_label',
			'placeholder'  => 'email_placeholder',
			'autocomplete' => 'email',
		]
	);
	$this->render_field(
		[
			'form'         => 'register',
			'name'         => 'username',
			'label'        => 'username_label',
			'placeholder'  => 'username_placeholder',
			'autocomplete' => 'username',
		]
	);
	?>
	<div class="afe__grid">
		<?php
		$this->render_field(
			[
				'form'         => 'register',
				'name'         => 'password',
				'type'         => 'password',
				'label'        => 'register_password_label',
				'placeholder'  => 'register_password_placeholder',
				'autocomplete' => 'new-password',
			]
		);
		$this->render_field(
			[
				'form'         => 'register',
				'name'         => 'password_confirm',
				'type'         => 'password',
				'label'        => 'confirm_label',
				'placeholder'  => 'confirm_placeholder',
				'autocomplete' => 'new-password',
			]
		);
		?>
	</div>
	<?php $this->render_button( 'register_button' ); ?>
</form>
