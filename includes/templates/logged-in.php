<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$afe_user     = wp_get_current_user();
$afe_redirect = get_permalink();
$afe_message  = str_replace(
	'%s',
	'<strong>' . esc_html( $afe_user->display_name ) . '</strong>',
	esc_html( $this->get_text( 'logged_in_text' ) )
);
?>
<div class="afe afe--logged-in">
	<p class="afe__logged-in">
		<?php echo wp_kses( $afe_message, [ 'strong' => [] ] ); ?>
		<a class="afe__link" href="<?php echo esc_url( wp_logout_url( $afe_redirect ? $afe_redirect : home_url( '/' ) ) ); ?>"><?php echo esc_html( $this->get_text( 'logout_text' ) ); ?></a>
	</p>
</div>
