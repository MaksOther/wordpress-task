<?php

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AFE_Widget extends Widget_Base {

	private const ROOT = '{{WRAPPER}} .afe';

	public function get_name(): string {
		return 'afe-auth-form';
	}

	public function get_title(): string {
		return __( 'Auth Form', 'auth-forms-for-elementor' );
	}

	public function get_icon(): string {
		return 'eicon-lock-user';
	}

	public function get_categories(): array {
		return [ 'general' ];
	}

	public function get_keywords(): array {
		return [ 'login', 'register', 'registration', 'auth', 'sign in', 'sign up', 'form', 'user' ];
	}

	public function get_style_depends(): array {
		return [ AFE_Assets::HANDLE ];
	}

	public function get_script_depends(): array {
		return [ AFE_Assets::HANDLE ];
	}

	protected function is_dynamic_content(): bool {
		return true;
	}

	protected function register_controls(): void {
		$this->register_form_controls();
		$this->register_text_controls();

		$this->register_container_style();
		$this->register_tabs_style();
		$this->register_label_style();
		$this->register_field_style();
		$this->register_button_style();
		$this->register_link_style();
		$this->register_message_style();
	}

	protected function render(): void {
		$is_editor = \Elementor\Plugin::$instance->editor->is_edit_mode() || \Elementor\Plugin::$instance->preview->is_preview_mode();

		( new AFE_Form( $this->get_settings_for_display(), $this->get_id(), $is_editor ) )->render();
	}

	private function register_form_controls(): void {
		$this->start_controls_section(
			'section_form',
			[
				'label' => __( 'Form', 'auth-forms-for-elementor' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'form_type',
			[
				'label'   => __( 'Form type', 'auth-forms-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'both',
				'options' => [
					'login'    => __( 'Login form', 'auth-forms-for-elementor' ),
					'register' => __( 'Registration form', 'auth-forms-for-elementor' ),
					'both'     => __( 'Login + Registration (tabs)', 'auth-forms-for-elementor' ),
				],
			]
		);

		$this->add_control(
			'default_tab',
			[
				'label'     => __( 'Active tab', 'auth-forms-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'login',
				'options'   => [
					'login'    => __( 'Login', 'auth-forms-for-elementor' ),
					'register' => __( 'Registration', 'auth-forms-for-elementor' ),
				],
				'condition' => [ 'form_type' => 'both' ],
			]
		);

		$redirect_description = __( 'Leave empty to reload the current page. Only URLs on this site are allowed.', 'auth-forms-for-elementor' );

		$this->add_control(
			'redirect_login',
			[
				'label'       => __( 'Redirect after login', 'auth-forms-for-elementor' ),
				'type'        => Controls_Manager::URL,
				'options'     => false,
				'placeholder' => home_url( '/' ),
				'description' => $redirect_description,
				'dynamic'     => [ 'active' => true ],
				'separator'   => 'before',
				'condition'   => [ 'form_type' => [ 'login', 'both' ] ],
			]
		);

		$this->add_control(
			'redirect_register',
			[
				'label'       => __( 'Redirect after registration', 'auth-forms-for-elementor' ),
				'type'        => Controls_Manager::URL,
				'options'     => false,
				'placeholder' => home_url( '/' ),
				'description' => $redirect_description,
				'dynamic'     => [ 'active' => true ],
				'condition'   => [ 'form_type' => [ 'register', 'both' ] ],
			]
		);

		$this->add_switcher( 'show_labels', __( 'Field labels', 'auth-forms-for-elementor' ), [ 'separator' => 'before' ] );
		$this->add_switcher( 'show_required_mark', __( 'Required mark', 'auth-forms-for-elementor' ), [ 'condition' => [ 'show_labels' => 'yes' ] ] );
		$this->add_switcher( 'show_password_toggle', __( 'Show/hide password button', 'auth-forms-for-elementor' ) );
		$this->add_switcher( 'show_remember', __( 'Remember Me', 'auth-forms-for-elementor' ), [ 'condition' => [ 'form_type' => [ 'login', 'both' ] ] ] );
		$this->add_switcher( 'show_lost_password', __( 'Forgot password link', 'auth-forms-for-elementor' ), [ 'condition' => [ 'form_type' => [ 'login', 'both' ] ] ] );

		$this->add_control(
			'logged_in_view',
			[
				'label'     => __( 'For logged-in users', 'auth-forms-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'message',
				'options'   => [
					'message' => __( 'Show message with a log out link', 'auth-forms-for-elementor' ),
					'hide'    => __( 'Show nothing', 'auth-forms-for-elementor' ),
				],
				'separator' => 'before',
			]
		);

		$this->end_controls_section();
	}

	private function register_text_controls(): void {
		foreach ( afe_get_texts() as $section_id => $section ) {
			$section_args = [
				'label' => $section['title'],
				'tab'   => Controls_Manager::TAB_CONTENT,
			];

			if ( ! empty( $section['condition'] ) ) {
				$section_args['condition'] = $section['condition'];
			}

			$this->start_controls_section( 'section_text_' . $section_id, $section_args );

			foreach ( $section['fields'] as $key => $field ) {
				$this->add_control(
					$key,
					[
						'label'       => $field['label'],
						'type'        => 'textarea' === ( $field['type'] ?? '' ) ? Controls_Manager::TEXTAREA : Controls_Manager::TEXT,
						'default'     => $field['default'],
						'description' => $field['description'] ?? '',
						'label_block' => true,
						'dynamic'     => [ 'active' => true ],
					]
				);
			}

			$this->end_controls_section();
		}
	}

	private function register_container_style(): void {
		$this->start_controls_section(
			'section_style_container',
			[
				'label' => __( 'Form container', 'auth-forms-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'container_max_width',
			[
				'label'      => __( 'Max width', 'auth-forms-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'      => [
					'px' => [
						'min' => 240,
						'max' => 1200,
					],
					'%'  => [
						'min' => 10,
						'max' => 100,
					],
				],
				'selectors'  => [ self::ROOT => 'max-width: {{SIZE}}{{UNIT}};' ],
			]
		);

		$this->add_responsive_control(
			'container_align',
			[
				'label'                => __( 'Alignment', 'auth-forms-for-elementor' ),
				'type'                 => Controls_Manager::CHOOSE,
				'options'              => [
					'left'   => [
						'title' => __( 'Left', 'auth-forms-for-elementor' ),
						'icon'  => 'eicon-h-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'auth-forms-for-elementor' ),
						'icon'  => 'eicon-h-align-center',
					],
					'right'  => [
						'title' => __( 'Right', 'auth-forms-for-elementor' ),
						'icon'  => 'eicon-h-align-right',
					],
				],
				'selectors_dictionary' => [
					'left'   => 'margin-left: 0; margin-right: auto;',
					'center' => 'margin-left: auto; margin-right: auto;',
					'right'  => 'margin-left: auto; margin-right: 0;',
				],
				'selectors'            => [ self::ROOT => '{{VALUE}}' ],
			]
		);

		$this->add_responsive_control(
			'container_gap',
			[
				'label'      => __( 'Space between fields', 'auth-forms-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 60,
					],
				],
				'selectors'  => [ self::ROOT => '--afe-gap: {{SIZE}}{{UNIT}};' ],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'container_background',
				'types'    => [ 'classic', 'gradient' ],
				'selector' => self::ROOT,
			]
		);

		$this->add_responsive_control(
			'container_padding',
			[
				'label'      => __( 'Padding', 'auth-forms-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [ self::ROOT => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'container_border',
				'selector' => self::ROOT,
			]
		);

		$this->add_responsive_control(
			'container_radius',
			[
				'label'      => __( 'Border radius', 'auth-forms-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [ self::ROOT => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'container_shadow',
				'selector' => self::ROOT,
			]
		);

		$this->add_color( 'text_color', __( 'Text color', 'auth-forms-for-elementor' ), '--afe-text', [ 'separator' => 'before' ] );

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'heading_typography',
				'label'    => __( 'Heading typography', 'auth-forms-for-elementor' ),
				'selector' => self::ROOT . ' .afe__heading',
			]
		);

		$this->add_color( 'heading_color', __( 'Heading color', 'auth-forms-for-elementor' ), '--afe-heading' );

		$this->end_controls_section();
	}

	private function register_tabs_style(): void {
		$this->start_controls_section(
			'section_style_tabs',
			[
				'label'     => __( 'Tabs', 'auth-forms-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [ 'form_type' => 'both' ],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'tab_typography',
				'selector' => self::ROOT . ' .afe__tab',
			]
		);

		$this->add_color( 'tab_color', __( 'Color', 'auth-forms-for-elementor' ), '--afe-tab' );
		$this->add_color( 'tab_active_color', __( 'Active color', 'auth-forms-for-elementor' ), '--afe-tab-active' );
		$this->add_color( 'tab_indicator_color', __( 'Active indicator color', 'auth-forms-for-elementor' ), '--afe-tab-indicator' );
		$this->add_color( 'tab_border_color', __( 'Divider color', 'auth-forms-for-elementor' ), '--afe-tab-border' );

		$this->add_responsive_control(
			'tab_spacing',
			[
				'label'      => __( 'Space below tabs', 'auth-forms-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 80,
					],
				],
				'selectors'  => [ self::ROOT . ' .afe__tabs' => 'margin-bottom: {{SIZE}}{{UNIT}};' ],
			]
		);

		$this->end_controls_section();
	}

	private function register_label_style(): void {
		$this->start_controls_section(
			'section_style_labels',
			[
				'label'     => __( 'Labels', 'auth-forms-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [ 'show_labels' => 'yes' ],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'label_typography',
				'selector' => self::ROOT . ' .afe__label',
			]
		);

		$this->add_color( 'label_color', __( 'Color', 'auth-forms-for-elementor' ), '--afe-label' );
		$this->add_color( 'required_color', __( 'Required mark color', 'auth-forms-for-elementor' ), '--afe-required' );

		$this->add_responsive_control(
			'label_spacing',
			[
				'label'      => __( 'Space below label', 'auth-forms-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 30,
					],
				],
				'selectors'  => [ self::ROOT . ' .afe__label' => 'margin-bottom: {{SIZE}}{{UNIT}};' ],
			]
		);

		$this->end_controls_section();
	}

	private function register_field_style(): void {
		$input = self::ROOT . ' .afe__input';

		$this->start_controls_section(
			'section_style_fields',
			[
				'label' => __( 'Fields', 'auth-forms-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'input_typography',
				'selector' => $input,
			]
		);

		$this->add_color( 'input_color', __( 'Text color', 'auth-forms-for-elementor' ), '--afe-input-text' );
		$this->add_color( 'input_placeholder_color', __( 'Placeholder color', 'auth-forms-for-elementor' ), '--afe-placeholder' );
		$this->add_color( 'input_background', __( 'Background color', 'auth-forms-for-elementor' ), '--afe-input-bg' );
		$this->add_color( 'input_border_color', __( 'Border color', 'auth-forms-for-elementor' ), '--afe-border' );
		$this->add_color( 'input_focus_color', __( 'Focus color', 'auth-forms-for-elementor' ), '--afe-focus' );

		$this->add_responsive_control(
			'input_border_width',
			[
				'label'      => __( 'Border width', 'auth-forms-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'selectors'  => [ $input => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
			]
		);

		$this->add_responsive_control(
			'input_radius',
			[
				'label'      => __( 'Border radius', 'auth-forms-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [ $input => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
			]
		);

		$this->add_responsive_control(
			'input_padding',
			[
				'label'      => __( 'Padding', 'auth-forms-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'selectors'  => [ $input => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
			]
		);

		$this->end_controls_section();
	}

	private function register_button_style(): void {
		$button = self::ROOT . ' .afe__button';

		$this->start_controls_section(
			'section_style_button',
			[
				'label' => __( 'Button', 'auth-forms-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'button_typography',
				'selector' => $button,
			]
		);

		$this->add_responsive_control(
			'button_align',
			[
				'label'     => __( 'Alignment', 'auth-forms-for-elementor' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'flex-start' => [
						'title' => __( 'Left', 'auth-forms-for-elementor' ),
						'icon'  => 'eicon-h-align-left',
					],
					'center'     => [
						'title' => __( 'Center', 'auth-forms-for-elementor' ),
						'icon'  => 'eicon-h-align-center',
					],
					'flex-end'   => [
						'title' => __( 'Right', 'auth-forms-for-elementor' ),
						'icon'  => 'eicon-h-align-right',
					],
					'stretch'    => [
						'title' => __( 'Full width', 'auth-forms-for-elementor' ),
						'icon'  => 'eicon-h-align-stretch',
					],
				],
				'selectors' => [ $button => 'align-self: {{VALUE}};' ],
			]
		);

		$this->start_controls_tabs( 'button_states' );

		$this->start_controls_tab( 'button_state_normal', [ 'label' => __( 'Normal', 'auth-forms-for-elementor' ) ] );
		$this->add_color( 'button_color', __( 'Text color', 'auth-forms-for-elementor' ), '--afe-button-text' );
		$this->add_color( 'button_background', __( 'Background color', 'auth-forms-for-elementor' ), '--afe-accent' );
		$this->end_controls_tab();

		$this->start_controls_tab( 'button_state_hover', [ 'label' => __( 'Hover', 'auth-forms-for-elementor' ) ] );
		$this->add_color( 'button_hover_color', __( 'Text color', 'auth-forms-for-elementor' ), '--afe-button-text-hover' );
		$this->add_color( 'button_hover_background', __( 'Background color', 'auth-forms-for-elementor' ), '--afe-accent-hover' );
		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'      => 'button_border',
				'selector'  => $button,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'button_radius',
			[
				'label'      => __( 'Border radius', 'auth-forms-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [ $button => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
			]
		);

		$this->add_responsive_control(
			'button_padding',
			[
				'label'      => __( 'Padding', 'auth-forms-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'selectors'  => [ $button => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
			]
		);

		$this->end_controls_section();
	}

	private function register_link_style(): void {
		$this->start_controls_section(
			'section_style_links',
			[
				'label' => __( 'Links and checkbox', 'auth-forms-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'link_typography',
				'selector' => self::ROOT . ' .afe__link, ' . self::ROOT . ' .afe__checkbox',
			]
		);

		$this->add_color( 'link_color', __( 'Link color', 'auth-forms-for-elementor' ), '--afe-link' );
		$this->add_color( 'link_hover_color', __( 'Link hover color', 'auth-forms-for-elementor' ), '--afe-link-hover' );

		$this->end_controls_section();
	}

	private function register_message_style(): void {
		$this->start_controls_section(
			'section_style_messages',
			[
				'label' => __( 'Messages', 'auth-forms-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'message_typography',
				'selector' => self::ROOT . ' .afe__message',
			]
		);

		$this->add_color( 'error_color', __( 'Error text color', 'auth-forms-for-elementor' ), '--afe-error' );
		$this->add_color( 'error_background', __( 'Error background color', 'auth-forms-for-elementor' ), '--afe-error-bg' );
		$this->add_color( 'success_color', __( 'Success text color', 'auth-forms-for-elementor' ), '--afe-success' );
		$this->add_color( 'success_background', __( 'Success background color', 'auth-forms-for-elementor' ), '--afe-success-bg' );

		$this->end_controls_section();
	}

	private function add_color( string $id, string $label, string $css_var, array $args = [] ): void {
		$this->add_control(
			$id,
			array_merge(
				[
					'label'     => $label,
					'type'      => Controls_Manager::COLOR,
					'selectors' => [ self::ROOT => $css_var . ': {{VALUE}};' ],
				],
				$args
			)
		);
	}

	private function add_switcher( string $id, string $label, array $args = [] ): void {
		$this->add_control(
			$id,
			array_merge(
				[
					'label'        => $label,
					'type'         => Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => 'yes',
				],
				$args
			)
		);
	}
}
