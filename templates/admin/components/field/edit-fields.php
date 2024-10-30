<?php
/**
 * List fields for edit
 *
 * @var array $args
 * */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$_type = $args['type'];

use CCFBW\Woocommerce\Checkout\Builder\Templates;

$field_set = apply_filters( 'ccfbw_field_set', array(), $_type );

if ( ! empty( $field_set ) ) :
	$group_counter = 0;

	foreach ( $field_set as $group => $fields ) :
		if ( ! empty( $fields ) ) {
			$count_fields = count( $fields );
			$increment    = 0;

			if ( 'general' !== $group ) {
				$group_counter++;

				Templates::get_settings_template(
					'components/field/group-start',
					true,
					array(
						'group'   => $group,
						'counter' => $group_counter,
					)
				);
			}

			foreach ( $fields as $field_key => $field_data ) {
				$increment++;

				$column_class        = 'ccfbw-field__column';
				$column_start_exists = ( ! empty( $field_data['column'] ) && 'start' === $field_data['column'] );
				$column_end_exists   = ( ! empty( $field_data['column'] ) && 'end' === $field_data['column'] );

				if ( $column_start_exists ) {
					$column_class .= ' ccfbw-field__column--two';
				}

				if ( ! $column_end_exists ) {
					echo '<div class="' . esc_attr( $column_class ) . '">';
				}

				Templates::get_settings_template(
					'components/field/types/' . $field_data['type'],
					true,
					array(
						'field_key'  => $field_key,
						'field_data' => $field_data,
					)
				);

				if ( ! $column_start_exists ) {
					echo '</div>';
				}
			}

			if ( 'general' !== $group ) {
				Templates::get_settings_template( 'components/field/group-end', true );
			}
		}
	endforeach;
endif;
