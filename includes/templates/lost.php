<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$afe_description = $this->get_text( 'lost_description' );
?>
<form
	class="afe__form"
	method="post"
	action="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>"
	novalidate
	data-afe-form="lost"
	data-afe-success="<?php echo esc_attr( $this->get_text( 'lost_success' ) ); ?>"
>
	<?php
	$this->render_hidden_fields( 'afe_lost_password' );
	$this->render_heading( 'lost_heading' );
	?>
	<?php if ( '' !== $afe_description ) : ?>
		<p class="afe__description"><?php echo esc_html( $afe_description ); ?></p>
	<?php endif; ?>
	<div class="afe__message" role="alert" hidden></div>
	<?php
	$this->render_field(
		[
			'form'         => 'lost',
			'name'         => 'user_login',
			'label'        => 'lost_user_label',
			'placeholder'  => 'lost_user_placeholder',
			'autocomplete' => 'username',
		]
	);
	$this->render_button( 'lost_button' );
	?>
	<div class="afe__row afe__row--center">
		<a class="afe__link" href="#<?php echo esc_attr( $this->get_id( 'panel-login' ) ); ?>" data-afe-show="login"><?php echo esc_html( $this->get_text( 'lost_back_link' ) ); ?></a>
	</div>
</form>
