<?php
namespace CCFBW\Woocommerce\Checkout\Builder\Frontend\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Widget_Base;
use CCFBW\Woocommerce\Checkout\Builder\Frontend\Woocommerce;
use CCFBW\Woocommerce\Checkout\Builder\Load;
use CCFBW\Woocommerce\Checkout\Builder\Templates;
use WC_Checkout;

class CCFBW_Elementor_Widget extends Widget_Base {
	public function get_icon() {
		return 'ccfbw-widget-checkout';
	}

	public function get_name() {
		return 'ccfbw_form';
	}

	public function get_title() {
		return esc_html__( 'Checkout Form', 'checkout-custom-fields-builder-for-woocommerce' );
	}

	public function get_categories() {
		return array( 'ccfbw-elements' );
	}

	public function get_keywords() {
		return array( 'woocommerce', 'checkout' );
	}

	public function get_script_depends() {
		$widget_scripts = parent::get_script_depends();

		$widget_scripts[] = 'wc-country-select';
		$widget_scripts[] = 'ccfbw-checkout';
		$widget_scripts[] = 'wc-password-strength-meter';
		$widget_scripts[] = 'selectWoo';
		$widget_scripts[] = 'ccfbw-checkout-form';

		return apply_filters( 'ccfbw_elementor_widget_script_depends', $widget_scripts );
	}

	public function get_style_depends() {
		$widget_styles = parent::get_style_depends();

		$widget_styles[] = 'select2';
		$widget_styles[] = 'ccfbw-form-style';

		return apply_filters( 'ccfbw_elementor_widget_style_depends', $widget_styles );
	}

	/**
	 * @return void
	 */
	protected function register_controls() {
		$this->register_sections();
		$this->register_styles();
	}

	protected function register_sections() {
		$this->top_bar();
		$this->checkout_sections();
		$this->coupon();
		$this->payment_gateways();
		$this->buttons();
	}

	protected function register_styles() {
		$this->wrap_style();
		$this->style_top_bar();
		$this->section_title();
		$this->section_subtitle();
		$this->style_sections();
		$this->style_fields();
		$this->style_order_summary();
		$this->style_coupon();
		$this->style_payment_methods();
		$this->style_privacy_policy();
		$this->style_terms_conditions();
		$this->style_checkout_buttons();
	}

	/* Start Content */
	private function top_bar() {
		$this->start_controls_section(
			'ccfbw_form_header',
			array(
				'label' => esc_html__( 'Form Header', 'checkout-custom-fields-builder-for-woocommerce' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'form_header_switch',
			array(
				'label'        => esc_html__( 'Enable', 'checkout-custom-fields-builder-for-woocommerce' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'checkout-custom-fields-builder-for-woocommerce' ),
				'label_off'    => esc_html__( 'No', 'checkout-custom-fields-builder-for-woocommerce' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'form_header_title',
			array(
				'label'   => esc_html__( 'Title', 'checkout-custom-fields-builder-for-woocommerce' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Header text', 'checkout-custom-fields-builder-for-woocommerce' ),
			)
		);

		$this->add_control(
			'form_header_subtitle',
			array(
				'label'   => esc_html__( 'Subtitle', 'checkout-custom-fields-builder-for-woocommerce' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Description text', 'checkout-custom-fields-builder-for-woocommerce' ),
			)
		);

		$this->end_controls_section();
	}

	private function checkout_sections() {
		$_sections    = apply_filters( 'ccfbw_get_sections', array(), true );
		$descriptions = array(
			'billing'  => esc_html__( 'Enter the billing address that matches your payment method.', 'checkout-custom-fields-builder-for-woocommerce' ),
			'shipping' => esc_html__( 'Enter the address where you want your order delivered.', 'checkout-custom-fields-builder-for-woocommerce' ),
			'account'  => esc_html__( 'Enter the create account details.', 'checkout-custom-fields-builder-for-woocommerce' ),
			'order'    => esc_html__( 'Enter the additional details for order.', 'checkout-custom-fields-builder-for-woocommerce' ),
		);

		foreach ( $_sections as $section_id => $section ) {
			$this->start_controls_section(
				'ccfbw_' . $section_id,
				array(
					'label' => $section['title'],
					'tab'   => Controls_Manager::TAB_CONTENT,
				)
			);

			$this->add_control(
				'section_description_' . $section_id,
				array(
					'label'   => esc_html__( 'Description', 'checkout-custom-fields-builder-for-woocommerce' ),
					'type'    => Controls_Manager::TEXT,
					'default' => ( ! empty( $descriptions[ $section_id ] ) ) ? $descriptions[ $section_id ] : '',
					'dynamic' => array(
						'active' => true,
					),
				)
			);

			if ( 'shipping' === $section_id ) {
				$this->add_control(
					'shipping_open_checkbox_text',
					array(
						'label'   => esc_html__( 'Checkbox Text', 'checkout-custom-fields-builder-for-woocommerce' ),
						'type'    => Controls_Manager::TEXT,
						'default' => esc_html__( 'Use same address for shipping', 'checkout-custom-fields-builder-for-woocommerce' ),
						'dynamic' => array(
							'active' => true,
						),
					)
				);
			}

			if ( ! empty( $section['fields'] ) ) {
				$columns = array(
					'full'      => '1/1',
					'one_half'  => '1/2',
					'one_third' => '1/3',
					'two_third' => '2/3',
				);

				foreach ( $section['fields'] as $field_id => $field ) {
					$this->add_control(
						'field_' . $field_id,
						array(
							'label'   => Load::limit_length( $field->label, 21 ),
							'type'    => Controls_Manager::SELECT,
							'options' => $columns,
							'default' => $field->properties['widget_default_value'],
						)
					);
				}
			}

			$this->end_controls_section();
		}
	}

	private function coupon() {
		$this->start_controls_section(
			'ccfbw_coupon',
			array(
				'label' => esc_html__( 'Coupon', 'checkout-custom-fields-builder-for-woocommerce' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'coupon_section_title',
			array(
				'label'   => esc_html__( 'Title', 'checkout-custom-fields-builder-for-woocommerce' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Have a coupon?', 'checkout-custom-fields-builder-for-woocommerce' ),
			)
		);

		$this->add_control(
			'coupon_section_description',
			array(
				'label'   => esc_html__( 'Description', 'checkout-custom-fields-builder-for-woocommerce' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'If you have a coupon code, please apply it below.', 'checkout-custom-fields-builder-for-woocommerce' ),
			)
		);

		$this->add_control(
			'coupon_open_button',
			array(
				'label'   => esc_html__( 'Open Button', 'checkout-custom-fields-builder-for-woocommerce' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Click here to enter your code', 'checkout-custom-fields-builder-for-woocommerce' ),
			)
		);

		$this->add_control(
			'coupon_button_text',
			array(
				'label'   => esc_html__( 'Button text', 'checkout-custom-fields-builder-for-woocommerce' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Apply coupon', 'checkout-custom-fields-builder-for-woocommerce' ),
			)
		);

		$this->end_controls_section();
	}

	private function payment_gateways() {
		$this->start_controls_section(
			'ccfbw_payment_gateways',
			array(
				'label' => esc_html__( 'Payment Gateways', 'checkout-custom-fields-builder-for-woocommerce' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'payment_title',
			array(
				'label'   => esc_html__( 'Title', 'checkout-custom-fields-builder-for-woocommerce' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Payment options', 'checkout-custom-fields-builder-for-woocommerce' ),
			)
		);

		$this->add_control(
			'payment_description',
			array(
				'label'   => esc_html__( 'Description', 'checkout-custom-fields-builder-for-woocommerce' ),
				'type'    => Controls_Manager::TEXTAREA,
				'default' => esc_html__( 'Enter the billing address that matches your payment method.', 'checkout-custom-fields-builder-for-woocommerce' ),
			)
		);

		$this->end_controls_section();
	}

	private function buttons() {
		$this->start_controls_section(
			'ccfbw_checkout_buttons',
			array(
				'label' => esc_html__( 'Checkout Button', 'checkout-custom-fields-builder-for-woocommerce' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'checkout_button_text',
			array(
				'label'   => esc_html__( 'Button Text', 'checkout-custom-fields-builder-for-woocommerce' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Place order', 'checkout-custom-fields-builder-for-woocommerce' ),
			)
		);

		$this->end_controls_section();
	}
	/* End Content */

	/* Start Style */
	private function wrap_style() {
		$this->start_controls_section(
			'ccfbw_form_wrapper',
			array(
				'label' => esc_html__( 'Form Wrapper', 'checkout-custom-fields-builder-for-woocommerce' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'wrapper_font',
			array(
				'name'      => 'wrapper_font',
				'label'     => esc_html__( 'Font Family', 'checkout-custom-fields-builder-for-woocommerce' ),
				'type'      => Controls_Manager::FONT,
				'selectors' => array(
					'{{WRAPPER}} *:not(i)' => 'font-family: {{VALUE}}',
				),
				'default'   => 'Roboto',
			)
		);

		$this->add_control(
			'wrapper_primary_color',
			array(
				'label'     => esc_html__( 'Primary Color', 'checkout-custom-fields-builder-for-woocommerce' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .ccfbw-form-container' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'wrapper_content_color',
			array(
				'label'     => esc_html__( 'Content Color', 'checkout-custom-fields-builder-for-woocommerce' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#212529',
				'selectors' => array(
					'{{WRAPPER}} *:not(i)' => 'color: {{VALUE}};',
				),
			)
		);

		$this->start_controls_tabs( 'tab_form_link_color' );
		$this->start_controls_tab(
			'tab_form_link_color_normal',
			array(
				'label' => esc_html__( 'Normal', 'checkout-custom-fields-builder-for-woocommerce' ),
			)
		);
		$this->add_control(
			'form_link_color',
			array(
				'label'     => esc_html__( 'Link color', 'checkout-custom-fields-builder-for-woocommerce' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} a' => 'color: {{VALUE}}',
				),
			)
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_form_link_color_hover',
			array(
				'label' => esc_html__( 'Hover', 'checkout-custom-fields-builder-for-woocommerce' ),
			)
		);
		$this->add_control(
			'form_link_color_hover',
			array(
				'label'     => esc_html__( 'Link color', 'checkout-custom-fields-builder-for-woocommerce' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} a:hover' => 'color: {{VALUE}}',
				),
			)
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_responsive_control(
			'form_wrapper_padding',
			array(
				'label'          => esc_html__( 'Padding', 'checkout-custom-fields-builder-for-woocommerce' ),
				'type'           => Controls_Manager::DIMENSIONS,
				'size_units'     => array( 'px', 'em', '%' ),
				'default'        => array(
					'top'      => 0,
					'right'    => 0,
					'bottom'   => 0,
					'left'     => 0,
					'unit'     => 'px',
					'isLinked' => true,
				),
				'mobile_default' => array(
					'top'      => 0,
					'right'    => 0,
					'bottom'   => 0,
					'left'     => 0,
					'unit'     => 'px',
					'isLinked' => false,
				),
				'tablet_default' => array(),
				'selectors'      => array(
					'{{WRAPPER}} .ccfbw-form-container' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'form_wrapper_margin',
			array(
				'label'          => esc_html__( 'Margin', 'checkout-custom-fields-builder-for-woocommerce' ),
				'type'           => Controls_Manager::DIMENSIONS,
				'size_units'     => array( 'px', 'em', '%' ),
				'default'        => array(
					'top'      => 0,
					'right'    => 0,
					'bottom'   => 0,
					'left'     => 0,
					'unit'     => 'px',
					'isLinked' => true,
				),
				'mobile_default' => array(
					'top'      => 0,
					'right'    => 0,
					'bottom'   => 0,
					'left'     => 0,
					'unit'     => 'px',
					'isLinked' => false,
				),
				'tablet_default' => array(),
				'selectors'      => array(
					'{{WRAPPER}} .ccfbw-form-container' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}
	/* End Style */

	private function style_top_bar() {
		$this->start_controls_section(
			'ccfbw_style_form_header',
			array(
				'label' => esc_html__( 'Header', 'checkout-custom-fields-builder-for-woocommerce' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'ccfbw_header_typography',
			array(
				'label'     => esc_html__( 'Typography', 'checkout-custom-fields-builder-for-woocommerce' ),
				'separator' => 'before',
				'type'      => Controls_Manager::HEADING,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'header_font_title',
				'label'          => esc_html__( 'Title', 'checkout-custom-fields-builder-for-woocommerce' ),
				'selector'       => '{{WRAPPER}} .ccfbw-form-header .ccfbw-form-header__title',
				'fields_options' => array(
					'typography'  => array( 'default' => 'yes' ),
					'font_weight' => array(
						'default' => 400,
					),
					'font_size'   => array(
						'default' => array( 'size' => 40 ),
					),
					'line_height' => array(
						'default' => array( 'size' => 48 ),
					),
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'header_font_subtitle',
				'label'          => esc_html__( 'Subtitle', 'checkout-custom-fields-builder-for-woocommerce' ),
				'selector'       => '{{WRAPPER}} .ccfbw-form-header .ccfbw-form-header__subtitle',
				'fields_options' => array(
					'typography'  => array( 'default' => 'yes' ),
					'font_weight' => array(
						'default' => 400,
					),
					'font_size'   => array(
						'default' => array( 'size' => 16 ),
					),
					'line_height' => array(
						'default' => array( 'size' => 24 ),
					),
				),
			)
		);

		$this->add_responsive_control(
			'header_alignment',
			array(
				'label'     => esc_html__( 'Alignment', 'checkout-custom-fields-builder-for-woocommerce' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'left'    => array(
						'title' => esc_html__( 'Left', 'checkout-custom-fields-builder-for-woocommerce' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center'  => array(
						'title' => esc_html__( 'Center', 'checkout-custom-fields-builder-for-woocommerce' ),
						'icon'  => 'eicon-text-align-center',
					),
					'right'   => array(
						'title' => esc_html__( 'Right', 'checkout-custom-fields-builder-for-woocommerce' ),
						'icon'  => 'eicon-text-align-right',
					),
					'justify' => array(
						'title' => esc_html__( 'Justified', 'checkout-custom-fields-builder-for-woocommerce' ),
						'icon'  => 'eicon-text-align-justify',
					),
				),
				'default'   => is_rtl() ? 'right' : 'center',
				'selectors' => array(
					'{{WRAPPER}} .ccfbw-form-header .ccfbw-form-header__title' => 'text-align: {{VALUE}};',
					'{{WRAPPER}} .ccfbw-form-header .ccfbw-form-header__subtitle' => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'ccfbw_header_colors',
			array(
				'label' => esc_html__( 'Colors', 'checkout-custom-fields-builder-for-woocommerce' ),
				'type'  => Controls_Manager::HEADING,
			)
		);

		$this->add_control(
			'header_background_color',
			array(
				'label'     => esc_html__( 'Background Color', 'checkout-custom-fields-builder-for-woocommerce' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .ccfbw-form-header' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'header_text_color',
			array(
				'label'     => esc_html__( 'Text Color', 'checkout-custom-fields-builder-for-woocommerce' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .ccfbw-form-header .ccfbw-form-header__title' => 'color: {{VALUE}};',
					'{{WRAPPER}} .ccfbw-form-header .ccfbw-form-header__subtitle' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'ccfbw_header_border',
			array(
				'label' => esc_html__( 'Border', 'checkout-custom-fields-builder-for-woocommerce' ),
				'type'  => Controls_Manager::HEADING,
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'header_border',
				'selector' => '{{WRAPPER}} .ccfbw-form-header',
			)
		);

		$this->add_responsive_control(
			'header_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'checkout-custom-fields-builder-for-woocommerce' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'default'    => array(),
				'selectors'  => array(
					'{{WRAPPER}} .ccfbw-form-header' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'ccfbw_header_advanced',
			array(
				'label' => esc_html__( 'Advanced', 'checkout-custom-fields-builder-for-woocommerce' ),
				'type'  => Controls_Manager::HEADING,
			)
		);

		$this->add_responsive_control(
			'header_padding',
			array(
				'label'      => esc_html__( 'Padding', 'checkout-custom-fields-builder-for-woocommerce' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'default'    => array(),
				'selectors'  => array(
					'{{WRAPPER}} .ccfbw-form-header' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'header_margin',
			array(
				'label'      => esc_html__( 'Margin', 'checkout-custom-fields-builder-for-woocommerce' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'default'    => array(
					'top'    => 0,
					'right'  => 0,
					'bottom' => 30,
					'left'   => 0,
					'unit'   => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .ccfbw-form-header' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	private function section_title() {
		$this->start_controls_section(
			'ccfbw_style_section_title',
			array(
				'label' => esc_html__( 'Heading', 'checkout-custom-fields-builder-for-woocommerce' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'ccfbw_section_title',
			array(
				'label'     => esc_html__( 'Title', 'checkout-custom-fields-builder-for-woocommerce' ),
				'separator' => 'before',
				'type'      => Controls_Manager::HEADING,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'font_section_title',
				'label'          => esc_html__( 'Typography', 'checkout-custom-fields-builder-for-woocommerce' ),
				'selector'       => '{{WRAPPER}} .ccfbw-form-row h3:not(#ship-to-different-address)',
				'fields_options' => array(
					'typography'  => array( 'default' => 'yes' ),
					'font_weight' => array(
						'default' => 700,
					),
					'font_size'   => array(
						'default'        => array( 'size' => 24 ),
						'mobile_default' => array( 'size' => 18 ),
					),
					'line_height' => array(
						'default'        => array( 'size' => 32 ),
						'mobile_default' => array( 'size' => 28 ),
					),
				),
			)
		);

		$this->add_control(
			'color_section_title',
			array(
				'label'     => esc_html__( 'Color', 'checkout-custom-fields-builder-for-woocommerce' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .ccfbw-form-row h3:not(#ship-to-different-address)' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_responsive_control(
			'alignment_section_title',
			array(
				'label'     => esc_html__( 'Alignment', 'checkout-custom-fields-builder-for-woocommerce' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'left'    => array(
						'title' => esc_html__( 'Left', 'checkout-custom-fields-builder-for-woocommerce' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center'  => array(
						'title' => esc_html__( 'Center', 'checkout-custom-fields-builder-for-woocommerce' ),
						'icon'  => 'eicon-text-align-center',
					),
					'right'   => array(
						'title' => esc_html__( 'Right', 'checkout-custom-fields-builder-for-woocommerce' ),
						'icon'  => 'eicon-text-align-right',
					),
					'justify' => array(
						'title' => esc_html__( 'Justified', 'checkout-custom-fields-builder-for-woocommerce' ),
						'icon'  => 'eicon-text-align-justify',
					),
				),
				'default'   => is_rtl() ? 'right' : 'left',
				'selectors' => array(
					'{{WRAPPER}} .ccfbw-form-row h3:not(#ship-to-different-address)' => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'ccfbw_section_advanced',
			array(
				'label'     => esc_html__( 'Advanced', 'checkout-custom-fields-builder-for-woocommerce' ),
				'separator' => 'before',
				'type'      => Controls_Manager::HEADING,
			)
		);

		$this->add_control(
			'background_section_title',
			array(
				'label'     => esc_html__( 'Background Color', 'checkout-custom-fields-builder-for-woocommerce' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .ccfbw-form-row h3:not(#ship-to-different-address)' => 'background-color:{{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'padding_section_title',
			array(
				'label'          => esc_html__( 'Padding', 'checkout-custom-fields-builder-for-woocommerce' ),
				'type'           => Controls_Manager::DIMENSIONS,
				'size_units'     => array( 'px', 'em', '%' ),
				'default'        => array(),
				'mobile_default' => array(),
				'tablet_default' => array(),
				'selectors'      => array(
					'{{WRAPPER}} .ccfbw-form-row h3:not(#ship-to-different-address)' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'margin_section_title',
			array(
				'label'      => esc_html__( 'Margin', 'checkout-custom-fields-builder-for-woocommerce' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'default'    => array(
					'top'    => 0,
					'right'  => 0,
					'bottom' => 8,
					'left'   => 0,
					'unit'   => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .ccfbw-form-row h3:not(#ship-to-different-address)' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'border_section_title',
				'label'    => esc_html__( 'Border', 'checkout-custom-fields-builder-for-woocommerce' ),
				'selector' => '{{WRAPPER}} .ccfbw-form-row h3:not(#ship-to-different-address)',

			)
		);

		$this->add_responsive_control(
			'border_section_title',
			array(
				'label'      => esc_html__( 'Border Radius', 'checkout-custom-fields-builder-for-woocommerce' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'default'    => array(
					'top'    => 0,
					'right'  => 0,
					'bottom' => 0,
					'left'   => 0,
					'unit'   => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .ccfbw-form-row h3:not(#ship-to-different-address)' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	private function section_subtitle() {
		$this->start_controls_section(
			'ccfbw_style_section_subtitle',
			array(
				'label' => esc_html__( 'Description', 'checkout-custom-fields-builder-for-woocommerce' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'ccfbw_section_subtitle',
			array(
				'label'     => esc_html__( 'Text', 'checkout-custom-fields-builder-for-woocommerce' ),
				'separator' => 'before',
				'type'      => Controls_Manager::HEADING,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'font_section_subtitle',
				'label'          => esc_html__( 'Typography', 'checkout-custom-fields-builder-for-woocommerce' ),
				'selector'       => '{{WRAPPER}} .ccfbw-form-row .ccfbw-section-subtitle',
				'fields_options' => array(
					'typography'  => array( 'default' => 'yes' ),
					'font_weight' => array(
						'default' => 400,
					),
					'font_size'   => array(
						'default'        => array( 'size' => 14 ),
						'mobile_default' => array( 'size' => 13 ),
					),
					'line_height' => array(
						'default' => array( 'size' => 16 ),
					),
				),
			)
		);

		$this->add_control(
			'color_section_subtitle',
			array(
				'label'     => esc_html__( 'Color', 'checkout-custom-fields-builder-for-woocommerce' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(18, 18, 18, 0.5)',
				'selectors' => array(
					'{{WRAPPER}} .ccfbw-form-row .ccfbw-section-subtitle' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_responsive_control(
			'alignment_section_subtitle',
			array(
				'label'     => esc_html__( 'Alignment', 'checkout-custom-fields-builder-for-woocommerce' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'left'    => array(
						'title' => esc_html__( 'Left', 'checkout-custom-fields-builder-for-woocommerce' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center'  => array(
						'title' => esc_html__( 'Center', 'checkout-custom-fields-builder-for-woocommerce' ),
						'icon'  => 'eicon-text-align-center',
					),
					'right'   => array(
						'title' => esc_html__( 'Right', 'checkout-custom-fields-builder-for-woocommerce' ),
						'icon'  => 'eicon-text-align-right',
					),
					'justify' => array(
						'title' => esc_html__( 'Justified', 'checkout-custom-fields-builder-for-woocommerce' ),
						'icon'  => 'eicon-text-align-justify',
					),
				),
				'default'   => is_rtl() ? 'right' : 'left',
				'selectors' => array(
					'{{WRAPPER}} .ccfbw-form-row .ccfbw-section-subtitle' => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'ccfbw_section_subheading_advanced',
			array(
				'label'     => esc_html__( 'Advanced', 'checkout-custom-fields-builder-for-woocommerce' ),
				'separator' => 'before',
				'type'      => Controls_Manager::HEADING,
			)
		);

		$this->add_control(
			'background_section_subtitle',
			array(
				'label'     => esc_html__( 'Background Color', 'checkout-custom-fields-builder-for-woocommerce' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .ccfbw-form-row .ccfbw-section-subtitle' => 'background-color:{{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'padding_section_subtitle',
			array(
				'label'          => esc_html__( 'Padding', 'checkout-custom-fields-builder-for-woocommerce' ),
				'type'           => Controls_Manager::DIMENSIONS,
				'size_units'     => array( 'px', 'em', '%' ),
				'default'        => array(),
				'mobile_default' => array(),
				'tablet_default' => array(),
				'selectors'      => array(
					'{{WRAPPER}} .ccfbw-form-row .ccfbw-section-subtitle' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'margin_section_subtitle',
			array(
				'label'      => esc_html__( 'Margin', 'checkout-custom-fields-builder-for-woocommerce' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'default'    => array(
					'top'    => 0,
					'right'  => 0,
					'bottom' => 25,
					'left'   => 0,
					'unit'   => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .ccfbw-form-row .ccfbw-section-subtitle' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'border_section_subtitle',
				'label'    => esc_html__( 'Border', 'checkout-custom-fields-builder-for-woocommerce' ),
				'selector' => '{{WRAPPER}} .ccfbw-form-row .ccfbw-section-subtitle',

			)
		);

		$this->add_responsive_control(
			'border_section_subtitle',
			array(
				'label'      => esc_html__( 'Border Radius', 'checkout-custom-fields-builder-for-woocommerce' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'default'    => array(
					'top'    => 0,
					'right'  => 0,
					'bottom' => 0,
					'left'   => 0,
					'unit'   => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .ccfbw-form-row .ccfbw-section-subtitle' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	private function style_fields() {
		$this->start_controls_section(
			'ccfbw_style_fields',
			array(
				'label' => esc_html__( 'Fields', 'checkout-custom-fields-builder-for-woocommerce' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'ccfbw_field_label',
			array(
				'label'     => esc_html__( 'Label', 'checkout-custom-fields-builder-for-woocommerce' ),
				'separator' => 'before',
				'type'      => Controls_Manager::HEADING,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'field_label_font',
				'label'          => esc_html__( 'Typography', 'checkout-custom-fields-builder-for-woocommerce' ),
				'selector'       => '{{WRAPPER}} .ccfbw-form-container .form-row label, {{WRAPPER}} .ccfbw-form-container .form-row label .optional, {{WRAPPER}} .ccfbw-form-container .form-row .required',
				'fields_options' => array(
					'typography'  => array( 'default' => 'yes' ),
					'font_weight' => array(
						'default' => 400,
					),
					'font_size'   => array(
						'default'        => array( 'size' => 16 ),
						'mobile_default' => array( 'size' => 13 ),
					),
					'line_height' => array(
						'default' => array( 'size' => 24 ),
					),
				),
			)
		);

		$this->add_control(
			'field_label_color',
			array(
				'label'     => esc_html__( 'Color', 'checkout-custom-fields-builder-for-woocommerce' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .ccfbw-form-container .form-row label' => 'color: {{VALUE}}',
				),
				'default'   => '#2B2D2F',
			)
		);

		$this->add_control(
			'ccfbw_field',
			array(
				'label'     => esc_html__( 'Field', 'checkout-custom-fields-builder-for-woocommerce' ),
				'separator' => 'before',
				'type'      => Controls_Manager::HEADING,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'field_font',
				'label'          => esc_html__( 'Typography', 'checkout-custom-fields-builder-for-woocommerce' ),
				'selector'       => '{{WRAPPER}} .ccfbw-form-container .form-row .input-text, {{WRAPPER}} .ccfbw-form-container .form-row .select2-selection__rendered, {{WRAPPER}} .ccfbw-form-container .form-row .select2-selection__placeholder, .select2-container--open .select2-dropdown .select2-results__option',
				'fields_options' => array(
					'typography'  => array( 'default' => 'yes' ),
					'font_weight' => array(
						'default' => 400,
					),
					'font_size'   => array(
						'default'        => array( 'size' => 16 ),
						'mobile_default' => array( 'size' => 13 ),
					),
					'line_height' => array(
						'default' => array( 'size' => 24 ),
					),
				),
			)
		);

		$this->add_control(
			'field_color',
			array(
				'label'     => esc_html__( 'Color', 'checkout-custom-fields-builder-for-woocommerce' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .ccfbw-form-container .form-row .input-text, {{WRAPPER}} .ccfbw-form-container .form-row .select2, {{WRAPPER}} .ccfbw-form-container .form-row .select2-selection__rendered, {{WRAPPER}} .ccfbw-form-container .form-row .select2-selection__placeholder, .select2-container--open .select2-dropdown .select2-results__option' => 'color: {{VALUE}}',
				),
				'default'   => '#121212',
			)
		);

		$this->add_control(
			'field_background_color',
			array(
				'label'     => esc_html__( 'Background Color', 'checkout-custom-fields-builder-for-woocommerce' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .ccfbw-form-container .form-row .input-text, {{WRAPPER}} .ccfbw-form-container .form-row .select2' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'ccfbw_field_placeholder',
			array(
				'label'     => esc_html__( 'Placeholder', 'checkout-custom-fields-builder-for-woocommerce' ),
				'separator' => 'before',
				'type'      => Controls_Manager::HEADING,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'field_placeholder_font',
				'label'          => esc_html__( 'Typography', 'checkout-custom-fields-builder-for-woocommerce' ),
				'selector'       => '{{WRAPPER}} .ccfbw-form-container .form-row .input-text::placeholder, {{WRAPPER}} .ccfbw-form-container .form-row .input-text::-webkit-input-placeholder, {{WRAPPER}} .ccfbw-form-container .form-row .select2-selection__placeholder, .woocommerce-page {{WRAPPER}} .ccfbw-form-row-multi-select .select2-container .select2-search__field::placeholder',
				'fields_options' => array(
					'typography'  => array( 'default' => 'yes' ),
					'font_weight' => array(
						'default' => 400,
					),
					'font_size'   => array(
						'default'        => array( 'size' => 16 ),
						'mobile_default' => array( 'size' => 13 ),
					),
					'line_height' => array(
						'default' => array( 'size' => 24 ),
					),
				),
			)
		);

		$this->add_control(
			'field_placeholder_color',
			array(
				'label'     => esc_html__( 'Color', 'checkout-custom-fields-builder-for-woocommerce' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .ccfbw-form-container .form-row .input-text::placeholder, {{WRAPPER}} .ccfbw-form-container .form-row .input-text::-webkit-input-placeholder, {{WRAPPER}} .ccfbw-form-container .form-row .select2-selection__placeholder' => 'color: {{VALUE}}',
					'.woocommerce-page {{WRAPPER}} .ccfbw-form-row-multi-select .select2-container .select2-search__field::placeholder, .woocommerce-page {{WRAPPER}} .ccfbw-form-row-multi-select .select2-container .select2-search__field::-webkit-input-placeholder' => 'color: {{VALUE}}',
				),
				'default'   => 'rgba(18, 18, 18, 0.5)',
			)
		);

		$this->add_control(
			'ccfbw_field_description',
			array(
				'label'     => esc_html__( 'Description', 'checkout-custom-fields-builder-for-woocommerce' ),
				'separator' => 'before',
				'type'      => Controls_Manager::HEADING,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'field_description_font',
				'label'          => esc_html__( 'Typography', 'checkout-custom-fields-builder-for-woocommerce' ),
				'selector'       => '{{WRAPPER}} .form-row .woocommerce-input-wrapper .description',
				'fields_options' => array(
					'typography'  => array( 'default' => 'yes' ),
					'font_weight' => array(
						'default' => 400,
					),
					'font_size'   => array(
						'default'        => array( 'size' => 16 ),
						'mobile_default' => array( 'size' => 13 ),
					),
					'line_height' => array(
						'default' => array( 'size' => 24 ),
					),
				),
			)
		);

		$this->add_control(
			'field_description_color',
			array(
				'label'     => esc_html__( 'Color', 'checkout-custom-fields-builder-for-woocommerce' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .form-row .woocommerce-input-wrapper .description' => 'color: {{VALUE}}',
				),
				'default'   => 'rgba(18, 18, 18, 0.5)',
			)
		);

		$this->add_control(
			'ccfbw_field_border',
			array(
				'label'     => esc_html__( 'Border', 'checkout-custom-fields-builder-for-woocommerce' ),
				'separator' => 'before',
				'type'      => Controls_Manager::HEADING,
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'           => 'field_border',
				'selector'       => '{{WRAPPER}} .ccfbw-form-container .form-row .input-text, {{WRAPPER}} .ccfbw-form-container .form-row .select2 .select2-selection, .select2-container .select2-dropdown',
				'fields_options' => array(
					'border' => array(
						'default' => 'solid',
					),
					'width'  => array(
						'default' => array(
							'top'      => '1',
							'right'    => '1',
							'bottom'   => '1',
							'left'     => '1',
							'isLinked' => true,
						),
					),
					'color'  => array(
						'default' => 'rgba(18, 18, 18, 0.8)',
					),
				),

			)
		);

		$this->add_responsive_control(
			'field_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'checkout-custom-fields-builder-for-woocommerce' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'default'    => array(
					'top'    => 4,
					'right'  => 4,
					'bottom' => 4,
					'left'   => 4,
					'unit'   => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .ccfbw-form-container .form-row .input-text' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .ccfbw-form-container .form-row .select2 .select2-selection' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'ccfbw_field_advanced',
			array(
				'label'     => esc_html__( 'Advanced', 'checkout-custom-fields-builder-for-woocommerce' ),
				'separator' => 'before',
				'type'      => Controls_Manager::HEADING,
			)
		);

		$this->add_responsive_control(
			'field_padding',
			array(
				'label'      => esc_html__( 'Padding', 'checkout-custom-fields-builder-for-woocommerce' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'default'    => array(
					'top'    => 11,
					'right'  => 12,
					'bottom' => 11,
					'left'   => 12,
					'unit'   => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .ccfbw-form-container .form-row .input-text' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .ccfbw-form-container .form-row .select2 .select2-selection' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'field_focus_color',
			array(
				'label'     => esc_html__( 'Border Focus Color', 'checkout-custom-fields-builder-for-woocommerce' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .ccfbw-form-container .form-row .input-text:focus, {{WRAPPER}} .ccfbw-form-container .form-row .select2 .select2-selection:focus' => 'border-color: {{VALUE}}',
				),
				'default'   => '#121212',
			)
		);

		$this->add_control(
			'field_validation_text',
			array(
				'label'     => esc_html__( 'Validation text color', 'checkout-custom-fields-builder-for-woocommerce' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .ccfbw-form-container .woocommerce-invalid .input-text, {{WRAPPER}} .ccfbw-form-container .woocommerce-invalid textarea, {{WRAPPER}} .ccfbw-form-container .woocommerce-invalid .select2 .select2-selection' => 'color: {{VALUE}}',
					'{{WRAPPER}} .ccfbw-form-container .woocommerce-invalid input.input-text, {{WRAPPER}} .ccfbw-form-container .woocommerce-invalid textarea, {{WRAPPER}} .ccfbw-form-container .woocommerce-invalid .select2 .select2-selection, {{WRAPPER}} .ccfbw-form-coupon-wrapper .checkout_coupon.woocommerce-invalid #coupon_code' => 'border-color: {{VALUE}}',
					'{{WRAPPER}} .ccfbw-form-container .woocommerce-invalid label' => 'color: {{VALUE}}',
					'{{WRAPPER}} .ccfbw-form-container #ccfbw-form-coupon-message' => 'color: {{VALUE}}',
				),
				'default'   => '#CC1818',
			)
		);

		$this->end_controls_section();
	}

	private function style_sections() {
		$this->start_controls_section(
			'ccfbw_style_sections',
			array(
				'label' => esc_html__( 'Sections', 'checkout-custom-fields-builder-for-woocommerce' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'section_background_color',
			array(
				'label'     => esc_html__( 'Background Color', 'checkout-custom-fields-builder-for-woocommerce' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .woocommerce-billing-fields' => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .woocommerce-account-fields' => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .woocommerce-shipping-fields .shipping_address' => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .woocommerce-additional-fields' => 'background-color: {{VALUE}}',
					'{{WRAPPER}} #order_review .ccfbw-checkout-review-order-wrapper' => 'background-color: {{VALUE}}',
					'{{WRAPPER}} #order_review .ccfbw-checkout-review-payment-wrapper' => 'background-color: {{VALUE}}',
					'{{WRAPPER}} #order_review .ccfbw-form-coupon-wrapper' => 'background-color: {{VALUE}}',
				),
				'default'   => '#fff',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'section_box_shadow',
				'label'    => esc_html__( 'Box Shadow', 'checkout-custom-fields-builder-for-woocommerce' ),
				'selector' => '{{WRAPPER}} .woocommerce-billing-fields, {{WRAPPER}} .woocommerce-shipping-fields .shipping_address, {{WRAPPER}} .woocommerce-account-fields, {{WRAPPER}} .woocommerce-additional-fields, {{WRAPPER}} #order_review .ccfbw-checkout-review-order-wrapper, {{WRAPPER}} #order_review .ccfbw-checkout-review-payment-wrapper, {{WRAPPER}} #order_review .ccfbw-form-coupon-wrapper',
			)
		);

		$this->add_control(
			'ccfbw_section_border',
			array(
				'label'     => esc_html__( 'Border', 'checkout-custom-fields-builder-for-woocommerce' ),
				'separator' => 'before',
				'type'      => Controls_Manager::HEADING,
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'           => 'section_border',
				'label'          => esc_html__( 'Border', 'checkout-custom-fields-builder-for-woocommerce' ),
				'selector'       => '{{WRAPPER}} .woocommerce-billing-fields, {{WRAPPER}} .woocommerce-account-fields, {{WRAPPER}} .woocommerce-shipping-fields .shipping_address, {{WRAPPER}} .woocommerce-additional-fields, {{WRAPPER}} #order_review .ccfbw-checkout-review-order-wrapper, {{WRAPPER}} #order_review .ccfbw-checkout-review-payment-wrapper, {{WRAPPER}} #order_review .ccfbw-form-coupon-wrapper',
				'fields_options' => array(
					'border' => array(
						'default' => 'solid',
					),
					'width'  => array(
						'default' => array(
							'top'      => '1',
							'right'    => '1',
							'bottom'   => '1',
							'left'     => '1',
							'isLinked' => true,
						),
					),
					'color'  => array(
						'default' => '#dddddd',
					),
				),
			)
		);

		$this->add_responsive_control(
			'section_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'checkout-custom-fields-builder-for-woocommerce' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'default'    => array(
					'top'    => 8,
					'right'  => 8,
					'bottom' => 8,
					'left'   => 8,
					'unit'   => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .woocommerce-billing-fields' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .woocommerce-account-fields' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .woocommerce-shipping-fields .shipping_address' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .woocommerce-additional-fields' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} #order_review .ccfbw-checkout-review-order-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} #order_review .ccfbw-checkout-review-payment-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} #order_review .ccfbw-form-coupon-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'ccfbw_section_indents',
			array(
				'type'      => Controls_Manager::DIVIDER,
				'separator' => 'none',
			)
		);

		$this->add_responsive_control(
			'section_padding',
			array(
				'label'          => esc_html__( 'Padding', 'checkout-custom-fields-builder-for-woocommerce' ),
				'type'           => Controls_Manager::DIMENSIONS,
				'size_units'     => array( 'px', 'em', '%' ),
				'default'        => array(
					'top'    => 25,
					'right'  => 25,
					'bottom' => 25,
					'left'   => 25,
					'unit'   => 'px',
				),
				'mobile_default' => array(
					'top'    => 15,
					'right'  => 15,
					'bottom' => 15,
					'left'   => 15,
					'unit'   => 'px',
				),
				'selectors'      => array(
					'{{WRAPPER}} .woocommerce-billing-fields' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .woocommerce-account-fields' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .woocommerce-shipping-fields .shipping_address' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .woocommerce-additional-fields' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} #order_review .ccfbw-checkout-review-order-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} #order_review .ccfbw-checkout-review-payment-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} #order_review .ccfbw-form-coupon-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'section_margin',
			array(
				'label'      => esc_html__( 'Margin', 'checkout-custom-fields-builder-for-woocommerce' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'default'    => array(
					'top'    => 0,
					'right'  => 0,
					'bottom' => 20,
					'left'   => 0,
					'unit'   => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .woocommerce-billing-fields' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .woocommerce-account-fields' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .woocommerce-shipping-fields .shipping_address' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .woocommerce-additional-fields' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} #order_review .ccfbw-checkout-review-order-wrapper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} #order_review .ccfbw-checkout-review-payment-wrapper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} #order_review .ccfbw-form-coupon-wrapper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'ccfbw_sections_advanced',
			array(
				'label'     => esc_html__( 'Checkbox for shipping fields', 'checkout-custom-fields-builder-for-woocommerce' ),
				'separator' => 'before',
				'type'      => Controls_Manager::HEADING,
			)
		);

		$this->add_control(
			'open_checkbox_section_color',
			array(
				'label'     => esc_html__( 'Color', 'checkout-custom-fields-builder-for-woocommerce' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} #ship-to-different-address label span' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'open_checkbox_section_font',
				'label'          => esc_html__( 'Typography', 'checkout-custom-fields-builder-for-woocommerce' ),
				'selector'       => '{{WRAPPER}} #ship-to-different-address label span',
				'fields_options' => array(
					'typography'  => array( 'default' => 'yes' ),
					'font_weight' => array(
						'default' => 400,
					),
					'font_size'   => array(
						'default'        => array( 'size' => 16 ),
						'mobile_default' => array( 'size' => 13 ),
					),
					'line_height' => array(
						'default' => array( 'size' => 24 ),
					),
				),
			)
		);

		$this->end_controls_section();
	}

	private function style_order_summary() {
		$this->start_controls_section(
			'ccfbw_style_order_summary',
			array(
				'label' => esc_html__( 'Order Summary', 'checkout-custom-fields-builder-for-woocommerce' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'ccfbw_os_product',
			array(
				'label'     => esc_html__( 'Product', 'checkout-custom-fields-builder-for-woocommerce' ),
				'separator' => 'before',
				'type'      => Controls_Manager::HEADING,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'os_product_font',
				'label'          => esc_html__( 'Typography', 'checkout-custom-fields-builder-for-woocommerce' ),
				'selector'       => '{{WRAPPER}} .woocommerce-checkout-review-order-table tr.cart_item td, {{WRAPPER}} .woocommerce-checkout-review-order-table tr.cart_item .woocommerce-Price-amount bdi, {{WRAPPER}} .woocommerce-checkout-review-order-table tr.cart_item .woocommerce-Price-currencySymbol, {{WRAPPER}} .woocommerce-checkout-review-order-table tr.cart_item .product-quantity',
				'fields_options' => array(
					'typography'  => array( 'default' => 'yes' ),
					'font_weight' => array(
						'default' => 400,
					),
					'font_size'   => array(
						'default'        => array( 'size' => 16 ),
						'mobile_default' => array( 'size' => 13 ),
					),
					'line_height' => array(
						'default' => array( 'size' => 24 ),
					),
				),
			)
		);

		$this->add_control(
			'os_product_color',
			array(
				'label'     => esc_html__( 'Color', 'checkout-custom-fields-builder-for-woocommerce' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .woocommerce-checkout-review-order-table tr.cart_item td' => 'color: {{VALUE}}',
					'{{WRAPPER}} .woocommerce-checkout-review-order-table tr.cart_item .woocommerce-Price-amount bdi' => 'color: {{VALUE}}',
					'{{WRAPPER}} .woocommerce-checkout-review-order-table tr.cart_item .woocommerce-Price-currencySymbol' => 'color: {{VALUE}}',
					'{{WRAPPER}} .woocommerce-checkout-review-order-table tr.cart_item .product-quantity' => 'color: {{VALUE}}',
				),
				'default'   => '',
			)
		);

		$this->add_control(
			'ccfbw_os_subtotal',
			array(
				'label'     => esc_html__( 'Subtotal', 'checkout-custom-fields-builder-for-woocommerce' ),
				'separator' => 'before',
				'type'      => Controls_Manager::HEADING,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'os_subtotal_font',
				'label'          => esc_html__( 'Typography', 'checkout-custom-fields-builder-for-woocommerce' ),
				'selector'       => '{{WRAPPER}} .woocommerce-checkout-review-order-table tr.cart-subtotal th, {{WRAPPER}} .woocommerce-checkout-review-order-table tr.cart-subtotal td, {{WRAPPER}} .woocommerce-checkout-review-order-table tr.order-total th, {{WRAPPER}} .woocommerce-checkout-review-order-table tr.cart-subtotal .woocommerce-Price-amount bdi, {{WRAPPER}} .woocommerce-checkout-review-order-table tr.cart-subtotal .woocommerce-Price-currencySymbol',
				'fields_options' => array(
					'typography'  => array( 'default' => 'yes' ),
					'font_weight' => array(
						'default' => 700,
					),
					'font_size'   => array(
						'default'        => array( 'size' => 16 ),
						'mobile_default' => array( 'size' => 13 ),
					),
					'line_height' => array(
						'default' => array( 'size' => 24 ),
					),
				),
			)
		);

		$this->add_control(
			'os_subtotal_color',
			array(
				'label'     => esc_html__( 'Color', 'checkout-custom-fields-builder-for-woocommerce' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .woocommerce-checkout-review-order-table tr.cart-subtotal td, {{WRAPPER}} .woocommerce-checkout-review-order-table tr.cart-subtotal th, {{WRAPPER}} .woocommerce-checkout-review-order-table tr.cart-subtotal .woocommerce-Price-amount bdi, {{WRAPPER}} .woocommerce-checkout-review-order-table tr.cart-subtotal .woocommerce-Price-currencySymbol' => 'color: {{VALUE}}',
				),
				'default'   => '',
			)
		);

		$this->add_control(
			'ccfbw_os_coupon_code',
			array(
				'label'     => esc_html__( 'Coupon code', 'checkout-custom-fields-builder-for-woocommerce' ),
				'separator' => 'before',
				'type'      => Controls_Manager::HEADING,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'os_coupon_font',
				'label'          => esc_html__( 'Typography', 'checkout-custom-fields-builder-for-woocommerce' ),
				'selector'       => '{{WRAPPER}} .woocommerce-checkout-review-order-table tr.cart-discount *',
				'fields_options' => array(
					'typography'  => array( 'default' => 'yes' ),
					'font_weight' => array(
						'default' => 700,
					),
					'font_size'   => array(
						'default'        => array( 'size' => 16 ),
						'mobile_default' => array( 'size' => 13 ),
					),
					'line_height' => array(
						'default' => array( 'size' => 24 ),
					),
				),
			)
		);

		$this->add_control(
			'os_coupon_color',
			array(
				'label'     => esc_html__( 'Text Color', 'checkout-custom-fields-builder-for-woocommerce' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .woocommerce-checkout-review-order-table tr.cart-discount th, {{WRAPPER}} .woocommerce-checkout-review-order-table tr.cart-discount td, {{WRAPPER}} .woocommerce-checkout-review-order-table tr.cart-discount td *' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'os_coupon_code_color',
			array(
				'label'     => esc_html__( 'Code Color', 'checkout-custom-fields-builder-for-woocommerce' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .woocommerce-checkout-review-order-table tr.cart-discount th span' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'ccfbw_os_shipping',
			array(
				'label'     => esc_html__( 'Shipping', 'checkout-custom-fields-builder-for-woocommerce' ),
				'separator' => 'before',
				'type'      => Controls_Manager::HEADING,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'os_shipping_font',
				'label'          => esc_html__( 'Heading Typography', 'checkout-custom-fields-builder-for-woocommerce' ),
				'selector'       => '{{WRAPPER}} .woocommerce-checkout-review-order-table tr.shipping th, {{WRAPPER}} .woocommerce-checkout-review-order-table tr.shipping td',
				'fields_options' => array(
					'typography'  => array( 'default' => 'yes' ),
					'font_weight' => array(
						'default' => 700,
					),
					'font_size'   => array(
						'default'        => array( 'size' => 16 ),
						'mobile_default' => array( 'size' => 13 ),
					),
					'line_height' => array(
						'default' => array( 'size' => 24 ),
					),
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'os_shipping_list_item_font',
				'label'          => esc_html__( 'List Item Typography', 'checkout-custom-fields-builder-for-woocommerce' ),
				'selector'       => '{{WRAPPER}} .woocommerce-checkout-review-order-table tr.shipping .woocommerce-shipping-methods label, {{WRAPPER}} .woocommerce-checkout-review-order-table ul#shipping_method .amount, {{WRAPPER}} .woocommerce-checkout-review-order-table tr.shipping .woocommerce-shipping-methods label .woocommerce-Price-currencySymbol, {{WRAPPER}} .woocommerce-checkout-review-order-table tr.shipping .woocommerce-shipping-methods label .woocommerce-Price-amount bdi',
				'fields_options' => array(
					'typography'  => array( 'default' => 'yes' ),
					'font_weight' => array(
						'default' => 400,
					),
					'font_size'   => array(
						'default'        => array( 'size' => 16 ),
						'mobile_default' => array( 'size' => 13 ),
					),
					'line_height' => array(
						'default' => array( 'size' => 20 ),
					),
				),
			)
		);

		$this->add_control(
			'os_shipping_color',
			array(
				'label'     => esc_html__( 'Color', 'checkout-custom-fields-builder-for-woocommerce' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .woocommerce-checkout-review-order-table tr.shipping th' => 'color: {{VALUE}}',
					'{{WRAPPER}} .woocommerce-checkout-review-order-table tr.shipping .woocommerce-shipping-methods label' => 'color: {{VALUE}}',
					'{{WRAPPER}} .woocommerce-checkout-review-order-table ul#shipping_method .amount' => 'color: {{VALUE}}',
					'{{WRAPPER}} .woocommerce-checkout-review-order-table tr.shipping .woocommerce-shipping-methods label .woocommerce-Price-currencySymbol' => 'color: {{VALUE}}',
					'{{WRAPPER}} .woocommerce-checkout-review-order-table tr.shipping .woocommerce-shipping-methods label .woocommerce-Price-amount bdi' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'ccfbw_os_total',
			array(
				'label'     => esc_html__( 'Total', 'checkout-custom-fields-builder-for-woocommerce' ),
				'separator' => 'before',
				'type'      => Controls_Manager::HEADING,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'os_total_font',
				'label'          => esc_html__( 'Typography', 'checkout-custom-fields-builder-for-woocommerce' ),
				'selector'       => '{{WRAPPER}} .woocommerce-checkout-review-order-table tr.order-total th, {{WRAPPER}} .woocommerce-checkout-review-order-table tr.order-total td, {{WRAPPER}} .woocommerce-checkout-review-order-table tr.order-total .woocommerce-Price-amount bdi, {{WRAPPER}} .woocommerce-checkout-review-order-table tr.order-total .woocommerce-Price-currencySymbol',
				'fields_options' => array(
					'typography'  => array( 'default' => 'yes' ),
					'font_weight' => array(
						'default' => 700,
					),
					'font_size'   => array(
						'default'        => array( 'size' => 16 ),
						'mobile_default' => array( 'size' => 13 ),
					),
					'line_height' => array(
						'default' => array( 'size' => 24 ),
					),
				),
			)
		);

		$this->add_control(
			'os_total_color',
			array(
				'label'     => esc_html__( 'Color', 'checkout-custom-fields-builder-for-woocommerce' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .woocommerce-checkout-review-order-table tr.order-total td, {{WRAPPER}} .woocommerce-checkout-review-order-table tr.order-total th, {{WRAPPER}} .woocommerce-checkout-review-order-table tr.order-total .woocommerce-Price-amount bdi, {{WRAPPER}} .woocommerce-checkout-review-order-table tr.order-total .woocommerce-Price-currencySymbol' => 'color: {{VALUE}}',
				),
				'default'   => '',
			)
		);

		$this->add_control(
			'ccfbw_os_divider',
			array(
				'label'     => esc_html__( 'Divider', 'checkout-custom-fields-builder-for-woocommerce' ),
				'separator' => 'before',
				'type'      => Controls_Manager::HEADING,
			)
		);

		$this->add_control(
			'os_divider_color',
			array(
				'label'     => esc_html__( 'Color', 'checkout-custom-fields-builder-for-woocommerce' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .woocommerce-checkout-review-order-table tfoot th, {{WRAPPER}} .woocommerce-checkout-review-order-table tfoot td' => 'border-top-color: {{VALUE}}',
				),
				'default'   => 'rgba(18, 18, 18, 0.12)',
			)
		);

		$this->end_controls_section();
	}

	private function style_coupon() {
		$this->start_controls_section(
			'ccfbw_style_coupon',
			array(
				'label' => esc_html__( 'Coupon', 'checkout-custom-fields-builder-for-woocommerce' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'ccfbw_coupon_link',
			array(
				'label'     => esc_html__( 'Link', 'checkout-custom-fields-builder-for-woocommerce' ),
				'separator' => 'before',
				'type'      => Controls_Manager::HEADING,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'coupon_link_font',
				'label'          => esc_html__( 'Typography', 'checkout-custom-fields-builder-for-woocommerce' ),
				'selector'       => '{{WRAPPER}} .woocommerce-form-coupon-toggle .showcoupon',
				'fields_options' => array(
					'typography'  => array( 'default' => 'yes' ),
					'font_weight' => array(
						'default' => 500,
					),
					'font_size'   => array(
						'default'        => array( 'size' => 16 ),
						'mobile_default' => array( 'size' => 14 ),
					),
					'line_height' => array(
						'default' => array( 'size' => 24 ),
					),
				),
			)
		);

		$this->add_control(
			'coupon_link_color',
			array(
				'label'     => esc_html__( 'Color', 'checkout-custom-fields-builder-for-woocommerce' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .woocommerce-form-coupon-toggle .showcoupon' => 'color: {{VALUE}}',
				),
				'default'   => '#1B81AC',
			)
		);

		$this->add_control(
			'ccfbw_coupon_field',
			array(
				'label'     => esc_html__( 'Field', 'checkout-custom-fields-builder-for-woocommerce' ),
				'separator' => 'before',
				'type'      => Controls_Manager::HEADING,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'coupon_field_typography',
				'label'          => esc_html__( 'Typography', 'checkout-custom-fields-builder-for-woocommerce' ),
				'selector'       => '{{WRAPPER}} .ccfbw-form-coupon-wrapper .checkout_coupon #coupon_code, {{WRAPPER}} .ccfbw-form-container #ccfbw-form-coupon-message',
				'fields_options' => array(
					'typography'  => array( 'default' => 'yes' ),
					'font_weight' => array(
						'default' => 400,
					),
					'font_size'   => array(
						'default' => array( 'size' => 16 ),
					),
					'line_height' => array(
						'default' => array( 'size' => 24 ),
					),
				),
			)
		);

		$this->add_control(
			'coupon_field_color',
			array(
				'label'     => esc_html__( 'Color', 'checkout-custom-fields-builder-for-woocommerce' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .ccfbw-form-coupon-wrapper .checkout_coupon #coupon_code' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'coupon_field_focus_color',
			array(
				'label'     => esc_html__( 'Focus Color', 'checkout-custom-fields-builder-for-woocommerce' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .ccfbw-form-coupon-wrapper .checkout_coupon #coupon_code' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'           => 'coupon_field_border',
				'selector'       => '{{WRAPPER}} .ccfbw-form-coupon-wrapper .checkout_coupon #coupon_code',
				'fields_options' => array(
					'border' => array(
						'default' => 'solid',
					),
					'width'  => array(
						'default' => array(
							'top'      => '1',
							'right'    => '1',
							'bottom'   => '1',
							'left'     => '1',
							'isLinked' => true,
						),
					),
					'color'  => array(
						'default' => 'rgba(18, 18, 18, 0.8)',
					),
				),
			)
		);

		$this->add_responsive_control(
			'coupon_field_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'checkout-custom-fields-builder-for-woocommerce' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'default'    => array(
					'top'    => 4,
					'right'  => 4,
					'bottom' => 4,
					'left'   => 4,
					'unit'   => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .ccfbw-form-coupon-wrapper .checkout_coupon #coupon_code' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'coupon_field_padding',
			array(
				'label'      => esc_html__( 'Padding', 'checkout-custom-fields-builder-for-woocommerce' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'default'    => array(
					'top'    => 12,
					'right'  => 12,
					'bottom' => 12,
					'left'   => 12,
					'unit'   => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .ccfbw-form-coupon-wrapper .checkout_coupon #coupon_code' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'ccfbw_coupon_button',
			array(
				'label'     => esc_html__( 'Button', 'checkout-custom-fields-builder-for-woocommerce' ),
				'separator' => 'before',
				'type'      => Controls_Manager::HEADING,
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'coupon_button_border',
				'selector' => '{{WRAPPER}} .ccfbw-form-coupon-wrapper .checkout_coupon .button',
			)
		);

		$this->add_responsive_control(
			'coupon_button_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'checkout-custom-fields-builder-for-woocommerce' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'default'    => array(
					'top'    => 4,
					'right'  => 4,
					'bottom' => 4,
					'left'   => 4,
					'unit'   => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .ccfbw-form-coupon-wrapper .checkout_coupon .button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->start_controls_tabs( 'tab_coupon_background' );
		$this->start_controls_tab(
			'tab_coupon_button',
			array(
				'label' => esc_html__( 'Normal', 'checkout-custom-fields-builder-for-woocommerce' ),
			)
		);
		$this->add_control(
			'coupon_button_background',
			array(
				'label'     => esc_html__( 'Background', 'checkout-custom-fields-builder-for-woocommerce' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .ccfbw-form-coupon-wrapper .checkout_coupon .button' => 'background-color: {{VALUE}}',
				),
				'default'   => 'rgba(33, 37, 41, 0.12)',
			)
		);
		$this->add_control(
			'coupon_button_text_color',
			array(
				'label'     => esc_html__( 'Button Text Color', 'checkout-custom-fields-builder-for-woocommerce' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .ccfbw-form-coupon-wrapper .checkout_coupon .button' => 'color: {{VALUE}}',
				),
				'default'   => '#2B2D2F',
			)
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_coupon_button_hover',
			array(
				'label' => esc_html__( 'Hover', 'checkout-custom-fields-builder-for-woocommerce' ),
			)
		);
		$this->add_control(
			'coupon_button_background_hover',
			array(
				'label'     => esc_html__( 'Background', 'checkout-custom-fields-builder-for-woocommerce' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .ccfbw-form-coupon-wrapper .checkout_coupon .button:hover' => 'background-color: {{VALUE}}',
				),
				'default'   => 'rgba(33, 37, 41, 0.09)',
			)
		);
		$this->add_control(
			'coupon_button_text_color_hover',
			array(
				'label'     => esc_html__( 'Button Text Color', 'checkout-custom-fields-builder-for-woocommerce' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .ccfbw-form-coupon-wrapper .checkout_coupon .button:hover' => 'color: {{VALUE}}',
				),
			)
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'coupon_button_typography',
				'label'          => esc_html__( 'Typography', 'checkout-custom-fields-builder-for-woocommerce' ),
				'selector'       => '{{WRAPPER}} .ccfbw-form-coupon-wrapper .checkout_coupon .button',
				'fields_options' => array(
					'typography'  => array( 'default' => 'yes' ),
					'font_weight' => array(
						'default' => 500,
					),
					'font_size'   => array(
						'default' => array( 'size' => 16 ),
					),
					'line_height' => array(
						'default' => array( 'size' => 24 ),
					),
				),
			)
		);

		$this->add_responsive_control(
			'coupon_button_alignment',
			array(
				'label'     => esc_html__( 'Alignment', 'checkout-custom-fields-builder-for-woocommerce' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'left'    => array(
						'title' => esc_html__( 'Left', 'checkout-custom-fields-builder-for-woocommerce' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center'  => array(
						'title' => esc_html__( 'Center', 'checkout-custom-fields-builder-for-woocommerce' ),
						'icon'  => 'eicon-text-align-center',
					),
					'right'   => array(
						'title' => esc_html__( 'Right', 'checkout-custom-fields-builder-for-woocommerce' ),
						'icon'  => 'eicon-text-align-right',
					),
					'justify' => array(
						'title' => esc_html__( 'Justified', 'checkout-custom-fields-builder-for-woocommerce' ),
						'icon'  => 'eicon-text-align-justify',
					),
				),
				'default'   => 'center',
				'selectors' => array(
					'{{WRAPPER}} .ccfbw-form-coupon-wrapper .checkout_coupon .button' => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'coupon_button_padding',
			array(
				'label'      => esc_html__( 'Padding', 'checkout-custom-fields-builder-for-woocommerce' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'default'    => array(
					'top'    => 12,
					'right'  => 32,
					'bottom' => 12,
					'left'   => 32,
					'unit'   => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .ccfbw-form-coupon-wrapper .checkout_coupon .button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	private function style_payment_methods() {
		$this->start_controls_section(
			'ccfbw_style_payment_methods',
			array(
				'label' => esc_html__( 'Payment Methods', 'checkout-custom-fields-builder-for-woocommerce' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'ccfbw_pm_heading_name',
			array(
				'label'     => esc_html__( 'Method Name', 'checkout-custom-fields-builder-for-woocommerce' ),
				'separator' => 'before',
				'type'      => Controls_Manager::HEADING,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'pm_text_typography',
				'label'          => esc_html__( 'Typography', 'checkout-custom-fields-builder-for-woocommerce' ),
				'selector'       => '{{WRAPPER}} .wc_payment_methods .wc_payment_method label',
				'fields_options' => array(
					'typography'  => array( 'default' => 'yes' ),
					'font_weight' => array(
						'default' => 700,
					),
					'font_size'   => array(
						'default' => array( 'size' => 16 ),
					),
					'line_height' => array(
						'default' => array( 'size' => 24 ),
					),
				),
			)
		);

		$this->add_control(
			'pm_text_color',
			array(
				'label'     => esc_html__( 'Color', 'checkout-custom-fields-builder-for-woocommerce' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wc_payment_methods .wc_payment_method label' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'ccfbw_pm_description_name',
			array(
				'label'     => esc_html__( 'Method Description', 'checkout-custom-fields-builder-for-woocommerce' ),
				'separator' => 'before',
				'type'      => Controls_Manager::HEADING,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'pm_description_typography',
				'label'          => esc_html__( 'Typography', 'checkout-custom-fields-builder-for-woocommerce' ),
				'selector'       => '{{WRAPPER}} .wc_payment_methods .wc_payment_method .payment_box, {{WRAPPER}} .wc_payment_methods .wc_payment_method .payment_box p',
				'fields_options' => array(
					'typography'  => array( 'default' => 'yes' ),
					'font_weight' => array(
						'default' => 400,
					),
					'font_size'   => array(
						'default' => array( 'size' => 14 ),
					),
					'line_height' => array(
						'default' => array( 'size' => 21 ),
					),
				),
			)
		);

		$this->add_control(
			'pm_description_color',
			array(
				'label'     => esc_html__( 'Color', 'checkout-custom-fields-builder-for-woocommerce' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wc_payment_methods .wc_payment_method .payment_box, {{WRAPPER}} .wc_payment_methods .wc_payment_method .payment_box p' => 'color: {{VALUE}}',
				),
			)
		);

		$this->start_controls_tabs( 'tabs_pm_background' );
		$this->start_controls_tab(
			'tab_pm_background',
			array(
				'label' => esc_html__( 'Normal', 'checkout-custom-fields-builder-for-woocommerce' ),
			)
		);
		$this->add_control(
			'pm_background_color',
			array(
				'label'     => esc_html__( 'Background Color', 'checkout-custom-fields-builder-for-woocommerce' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wc_payment_methods .wc_payment_method' => 'background-color: {{VALUE}}',
				),
				'default'   => '#fff',
			)
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_pm_background_opened',
			array(
				'label' => esc_html__( 'Opened', 'checkout-custom-fields-builder-for-woocommerce' ),
			)
		);
		$this->add_control(
			'pm_opened_background_color',
			array(
				'label'     => esc_html__( 'Background Color', 'checkout-custom-fields-builder-for-woocommerce' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wc_payment_methods .wc_payment_method.opened' => 'background-color: {{VALUE}}',
				),
				'default'   => 'rgba(33, 37, 41, 0.05)',
			)
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	private function style_privacy_policy() {
		$this->start_controls_section(
			'ccfbw_style_privacy_policy',
			array(
				'label' => esc_html__( 'Privacy Policy', 'checkout-custom-fields-builder-for-woocommerce' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'pp_typography',
				'label'          => esc_html__( 'Typography', 'checkout-custom-fields-builder-for-woocommerce' ),
				'selector'       => '{{WRAPPER}} .woocommerce-privacy-policy-text, {{WRAPPER}} .woocommerce-privacy-policy-text p, {{WRAPPER}} .woocommerce-privacy-policy-link',
				'fields_options' => array(
					'typography'  => array( 'default' => 'yes' ),
					'font_weight' => array(
						'default' => 400,
					),
					'font_size'   => array(
						'default'        => array( 'size' => 16 ),
						'mobile_default' => array( 'size' => 14 ),
					),
					'line_height' => array(
						'default' => array( 'size' => 24 ),
					),
				),
			)
		);

		$this->add_control(
			'pp_text_color',
			array(
				'label'     => esc_html__( 'Color', 'checkout-custom-fields-builder-for-woocommerce' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .woocommerce-privacy-policy-text, {{WRAPPER}} .woocommerce-privacy-policy-text p' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'pp_link_color',
			array(
				'label'     => esc_html__( 'Link Color', 'checkout-custom-fields-builder-for-woocommerce' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .woocommerce-privacy-policy-link' => 'color: {{VALUE}}',
				),
				'default'   => '#1B81AC',
			)
		);

		$this->end_controls_section();
	}

	public function style_terms_conditions() {
		$this->start_controls_section(
			'ccfbw_tc_style_terms_conditions',
			array(
				'label' => esc_html__( 'Terms & Conditions', 'checkout-custom-fields-builder-for-woocommerce' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'tc_typography',
				'label'          => esc_html__( 'Typography', 'checkout-custom-fields-builder-for-woocommerce' ),
				'selector'       => '{{WRAPPER}} .woocommerce-privacy-policy-text, {{WRAPPER}} .woocommerce-privacy-policy-text p, {{WRAPPER}} .woocommerce-privacy-policy-link',
				'fields_options' => array(
					'typography'  => array( 'default' => 'yes' ),
					'font_weight' => array(
						'default' => 400,
					),
					'font_size'   => array(
						'default'        => array( 'size' => 16 ),
						'mobile_default' => array( 'size' => 14 ),
					),
					'line_height' => array(
						'default' => array( 'size' => 24 ),
					),
				),
			)
		);

		$this->add_control(
			'tc_text_color',
			array(
				'label'     => esc_html__( 'Color', 'checkout-custom-fields-builder-for-woocommerce' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .woocommerce-terms-and-conditions-checkbox-text' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'tc_link_color',
			array(
				'label'     => esc_html__( 'Link Color', 'checkout-custom-fields-builder-for-woocommerce' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .woocommerce-terms-and-conditions-link' => 'color: {{VALUE}}',
				),
				'default'   => '#1B81AC',
			)
		);

		$this->end_controls_section();
	}

	private function style_checkout_buttons() {
		$this->start_controls_section(
			'ccfbw_style_checkout_buttons',
			array(
				'label' => esc_html__( 'Checkout Button', 'checkout-custom-fields-builder-for-woocommerce' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'cb_button_width',
			array(
				'label'      => esc_html__( 'Button Width (in %)', 'checkout-custom-fields-builder-for-woocommerce' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => '%',
				'range'      => array(
					'min'  => 0,
					'max'  => 100,
					'step' => 1,
				),
				'default'    => array(
					'unit' => '%',
					'size' => 100,
				),
				'selectors'  => array(
					'{{WRAPPER}} #place_order' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'cb_button_alignment',
			array(
				'label'     => esc_html__( 'Alignment', 'checkout-custom-fields-builder-for-woocommerce' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'left'    => array(
						'title' => esc_html__( 'Left', 'checkout-custom-fields-builder-for-woocommerce' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center'  => array(
						'title' => esc_html__( 'Center', 'checkout-custom-fields-builder-for-woocommerce' ),
						'icon'  => 'eicon-text-align-center',
					),
					'right'   => array(
						'title' => esc_html__( 'Right', 'checkout-custom-fields-builder-for-woocommerce' ),
						'icon'  => 'eicon-text-align-right',
					),
					'justify' => array(
						'title' => esc_html__( 'Justified', 'checkout-custom-fields-builder-for-woocommerce' ),
						'icon'  => 'eicon-text-align-justify',
					),
				),
				'default'   => is_rtl() ? 'right' : 'center',
				'selectors' => array(
					'{{WRAPPER}} #place_order' => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'           => 'cb_button_typography',
				'label'          => esc_html__( 'Typography', 'checkout-custom-fields-builder-for-woocommerce' ),
				'selector'       => '{{WRAPPER}} #place_order',
				'fields_options' => array(
					'typography'     => array( 'default' => 'yes' ),
					'font_weight'    => array(
						'default' => 500,
					),
					'font_size'      => array(
						'default' => array( 'size' => 16 ),
					),
					'line_height'    => array(
						'default' => array( 'size' => 24 ),
					),
					'text_transform' => array(
						'default' => 'uppercase',
					),
				),
			)
		);

		$this->add_control(
			'ccfbw_button_indents',
			array(
				'type'      => Controls_Manager::DIVIDER,
				'separator' => 'none',
			)
		);

		$this->add_responsive_control(
			'cb_padding',
			array(
				'label'          => esc_html__( 'Padding', 'checkout-custom-fields-builder-for-woocommerce' ),
				'type'           => Controls_Manager::DIMENSIONS,
				'size_units'     => array( 'px', 'em', '%' ),
				'default'        => array(
					'top'    => 16,
					'right'  => 16,
					'bottom' => 16,
					'left'   => 16,
					'unit'   => 'px',
				),
				'mobile_default' => array(),
				'tablet_default' => array(),
				'selectors'      => array(
					'{{WRAPPER}} #place_order' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'cb_margin',
			array(
				'label'          => esc_html__( 'Margin', 'checkout-custom-fields-builder-for-woocommerce' ),
				'type'           => Controls_Manager::DIMENSIONS,
				'size_units'     => array( 'px', 'em', '%' ),
				'default'        => array(),
				'mobile_default' => array(),
				'tablet_default' => array(),
				'selectors'      => array(
					'{{WRAPPER}} #place_order' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'ccfbw_button_border',
			array(
				'type'      => Controls_Manager::DIVIDER,
				'separator' => 'none',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'cb_border',
				'selector' => '{{WRAPPER}} #place_order',
			)
		);

		$this->add_responsive_control(
			'cb_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'checkout-custom-fields-builder-for-woocommerce' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'default'    => array(
					'top'    => 4,
					'right'  => 4,
					'bottom' => 4,
					'left'   => 4,
					'unit'   => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} #place_order' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'ccfbw_button_colors',
			array(
				'type'      => Controls_Manager::DIVIDER,
				'separator' => 'none',
			)
		);

		$this->add_control(
			'cb_button_background',
			array(
				'label'     => esc_html__( 'Background', 'checkout-custom-fields-builder-for-woocommerce' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} #place_order' => 'background-color: {{VALUE}}',
				),
				'default'   => '#212529',
			)
		);
		$this->add_control(
			'cb_button_text_color',
			array(
				'label'     => esc_html__( 'Button Text Color', 'checkout-custom-fields-builder-for-woocommerce' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} #place_order' => 'color: {{VALUE}}',
				),
				'default'   => '#fff',
			)
		);

		$this->end_controls_section();
	}

	public function start_container_column_one() {
		$settings = $this->get_settings_for_display();

		Templates::get_ccfbw_template( 'checkout/wrapper-start', true, array( 'settings' => $settings ) );
	}

	public function end_column_one() {
		Templates::get_ccfbw_template( 'checkout/column-one-end', true );
	}

	public function start_column_two() {
		Templates::get_ccfbw_template( 'checkout/column-two-start', true );
	}

	public function end_container_column_two() {
		Templates::get_ccfbw_template( 'checkout/wrapper-end', true );
	}

	public function field_args( $args, $key ) {
		$settings = $this->get_settings_for_display();

		if ( ! empty( $settings[ 'field_' . $key ] ) ) {
			$field_value = $settings[ 'field_' . $key ];

			$args['class'] = array_filter(
				$args['class'],
				function( $value ) {
					if ( 'form-row-first' === $value || 'form-row-last' === $value || 'form-row-wide' === $value ) {
						return false;
					}

					return true;
				}
			);

			$args['class'][] = 'ccfbw-form-row_' . $field_value;
		}

		if ( ! empty( $args['custom_class_name'] ) ) {
			$args['custom_class_name'] = explode( ' ', $args['custom_class_name'] );

			$args['input_class'] = array_merge( $args['input_class'], $args['custom_class_name'] );
		}

		if ( ! empty( $args['default_value'] ) ) {
			$args['default'] = $args['default_value'];
		}

		if ( ! empty( $args['properties'] ) && ! empty( $args['properties']['placeholder'] ) ) {
			$args['placeholder'] = $args['properties']['placeholder'];
		}

		return $args;
	}

	public function checkout_billing() {
		$settings = $this->get_settings_for_display();
		$checkout = WC_Checkout::instance();

		Templates::get_ccfbw_template(
			'checkout/form-billing',
			true,
			array(
				'settings' => $settings,
				'checkout' => $checkout,
			)
		);
	}

	public function checkout_shipping() {
		$settings = $this->get_settings_for_display();

		Templates::get_ccfbw_template(
			'checkout/form-shipping',
			true,
			array(
				'settings' => $settings,
				'checkout' => WC()->checkout(),
			)
		);
	}

	public function checkout_account_details() {
		$settings = $this->get_settings_for_display();

		Templates::get_ccfbw_template(
			'checkout/form-account-fields',
			true,
			array(
				'settings' => $settings,
				'checkout' => WC()->checkout(),
			)
		);
	}

	public function checkout_order_details() {
		$settings = $this->get_settings_for_display();

		Templates::get_ccfbw_template(
			'checkout/form-additional-fields',
			true,
			array(
				'settings' => $settings,
				'checkout' => WC()->checkout(),
			)
		);

	}

	public function coupon_form() {
		$settings = $this->get_settings_for_display();

		Templates::get_ccfbw_template(
			'checkout/form-coupon',
			true,
			array( 'settings' => $settings )
		);
	}

	public function add_render_elements() {
		add_action( 'woocommerce_checkout_before_customer_details', array( $this, 'start_container_column_one' ) );
		add_action( 'woocommerce_checkout_after_customer_details', array( $this, 'end_column_one' ) );

		add_action( 'woocommerce_checkout_before_order_review_heading', array( $this, 'start_column_two' ) );
		add_action( 'woocommerce_checkout_after_order_review', array( $this, 'end_container_column_two' ) );

		remove_action( 'woocommerce_checkout_billing', array( WC_Checkout::instance(), 'checkout_form_billing' ) );
		add_action( 'woocommerce_checkout_billing', array( $this, 'checkout_billing' ) );

		remove_action( 'woocommerce_checkout_shipping', array( WC_Checkout::instance(), 'checkout_form_shipping' ) );
		add_action( 'woocommerce_checkout_shipping', array( $this, 'checkout_shipping' ) );

		add_action( 'ccfbw_checkout_account_details', array( $this, 'checkout_account_details' ) );

		add_action( 'ccfbw_checkout_order_details', array( $this, 'checkout_order_details' ) );

		remove_action( 'woocommerce_checkout_order_review', 'woocommerce_order_review' );
		add_action( 'woocommerce_checkout_order_review', array( Woocommerce::class, 'order_review' ) );

		remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form' );
		add_action( 'ccfbw_checkout_coupon_form', array( $this, 'coupon_form' ), 10 );

		add_filter( 'woocommerce_form_field_args', array( $this, 'field_args' ), 20, 2 );
	}

	protected function remove_render_elements() {
		remove_action( 'woocommerce_checkout_before_customer_details', array( $this, 'start_container_column_one' ) );
		remove_action( 'woocommerce_checkout_after_customer_details', array( $this, 'end_column_one' ) );

		remove_action( 'woocommerce_checkout_before_order_review_heading', array( $this, 'start_column_two' ) );
		remove_action( 'woocommerce_checkout_after_order_review', array( $this, 'end_container_column_two' ) );

		remove_action( 'woocommerce_checkout_billing', array( $this, 'checkout_billing' ) );
		remove_action( 'woocommerce_checkout_shipping', array( $this, 'checkout_shipping' ) );
		remove_action( 'ccfbw_checkout_coupon_form', array( $this, 'coupon_form' ) );

		remove_action( 'woocommerce_checkout_order_review', array( Woocommerce::class, 'order_review' ) );
		add_action( 'woocommerce_checkout_order_review', 'woocommerce_order_review' );

		remove_filter( 'woocommerce_form_field_args', array( $this, 'field_args' ), 20 );
	}

	protected function render() {
		/* Render Woocommerce & Current plugin hooks/filters */
		$this->add_render_elements();

		/* Get woocommerce checkout form */
		echo do_shortcode( '[woocommerce_checkout]' );

		/* Remove Woocommerce & Current plugin hooks/filters */
		$this->remove_render_elements();
	}
}
