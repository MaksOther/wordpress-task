<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$afe_type     = $this->get_type();
$afe_active   = $this->get_active_panel();
$afe_has_tabs = 'both' === $afe_type;
$afe_tabs     = [
	'login'    => 'login_tab_title',
	'register' => 'register_tab_title',
];
?>
<div class="afe afe--<?php echo esc_attr( $afe_type ); ?>" data-afe<?php echo $this->is_editor() ? ' data-afe-preview' : ''; ?>>
	<?php if ( $afe_has_tabs ) : ?>
		<div class="afe__tabs" role="tablist">
			<?php foreach ( $afe_tabs as $afe_panel => $afe_text_key ) : ?>
				<button
					type="button"
					class="afe__tab<?php echo $afe_active === $afe_panel ? ' is-active' : ''; ?>"
					role="tab"
					id="<?php echo esc_attr( $this->get_id( 'tab-' . $afe_panel ) ); ?>"
					aria-controls="<?php echo esc_attr( $this->get_id( 'panel-' . $afe_panel ) ); ?>"
					aria-selected="<?php echo $afe_active === $afe_panel ? 'true' : 'false'; ?>"
					tabindex="<?php echo $afe_active === $afe_panel ? '0' : '-1'; ?>"
					data-afe-tab="<?php echo esc_attr( $afe_panel ); ?>"
				><?php echo esc_html( $this->get_text( $afe_text_key ) ); ?></button>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>

	<?php foreach ( $this->get_panels() as $afe_panel ) : ?>
		<div
			class="afe__panel"
			id="<?php echo esc_attr( $this->get_id( 'panel-' . $afe_panel ) ); ?>"
			data-afe-panel="<?php echo esc_attr( $afe_panel ); ?>"
			<?php if ( $afe_has_tabs && isset( $afe_tabs[ $afe_panel ] ) ) : ?>
				role="tabpanel"
				aria-labelledby="<?php echo esc_attr( $this->get_id( 'tab-' . $afe_panel ) ); ?>"
			<?php endif; ?>
			<?php echo $afe_active === $afe_panel ? '' : 'hidden'; ?>
		>
			<?php $this->load_template( $afe_panel ); ?>
		</div>
	<?php endforeach; ?>
</div>
