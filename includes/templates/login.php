<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$afe_show_remember = $this->is_enabled( 'show_remember' );
$afe_show_lost     = $this->is_enabled( 'show_lost_password' );
?>
<form
	class="afe__form"
	method="post"
	action="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>"
	novalidate
	data-afe-form="login"
	data-afe-success="<?php echo esc_attr( $this->get_text( 'login_success' ) ); ?>"
>
	<?php
	$this->render_hidden_fields( 'afe_login', 'redirect_login' );
	$this->render_heading( 'login_heading' );
	?>
	<div class="afe__message" role="alert" hidden></div>
	<?php
	$this->render_field(
		[
			'form'         => 'login',
			'name'         => 'user_login',
			'label'        => 'login_user_label',
			'placeholder'  => 'login_user_placeholder',
			'autocomplete' => 'username',
		]
	);
	$this->render_field(
		[
			'form'         => 'login',
			'name'         => 'password',
			'type'         => 'password',
			'label'        => 'login_password_label',
			'placeholder'  => 'login_password_placeholder',
			'autocomplete' => 'current-password',
		]
	);
	?>
	<?php if ( $afe_show_remember || $afe_show_lost ) : ?>
		<div class="afe__row">
			<?php if ( $afe_show_remember ) : ?>
				<label class="afe__checkbox">
					<input type="checkbox" name="remember" value="1">
					<span><?php echo esc_html( $this->get_text( 'login_remember_label' ) ); ?></span>
				</label>
			<?php endif; ?>
			<?php if ( $afe_show_lost ) : ?>
				<a class="afe__link" href="<?php echo esc_url( wp_lostpassword_url() ); ?>" data-afe-show="lost"><?php echo esc_html( $this->get_text( 'login_lost_link' ) ); ?></a>
			<?php endif; ?>
		</div>
	<?php endif; ?>
	<?php $this->render_button( 'login_button' ); ?>
</form>
