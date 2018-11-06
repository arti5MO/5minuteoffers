<?php
/**
 * Product Time Countdown for WooCommerce - Settings
 *
 * @version 1.4.0
 * @since   1.0.0
 * @author  Algoritmika Ltd.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Settings_Product_Countdown' ) ) :

class Alg_WC_Settings_Product_Countdown extends WC_Settings_Page {

	/**
	 * Constructor.
	 *
	 * @version 1.4.0
	 * @since   1.0.0
	 */
	function __construct() {
		$this->id    = 'alg_wc_product_countdown';
		$this->label = __( 'Product Time Countdown', 'product-countdown-for-woocommerce' );
		parent::__construct();
		add_filter( 'woocommerce_admin_settings_sanitize_option', array( $this, 'maybe_unsanitize_option' ), PHP_INT_MAX, 3 );
	}

	/**
	 * maybe_unsanitize_option.
	 *
	 * @version 1.4.0
	 * @since   1.4.0
	 */
	function maybe_unsanitize_option( $value, $option, $raw_value ) {
		return ( ! empty( $option['alg_wc_ptc_raw'] ) ? $raw_value : $value );
	}

	/**
	 * get_settings.
	 *
	 * @version 1.4.0
	 * @since   1.0.0
	 */
	function get_settings() {
		global $current_section;
		return array_merge( apply_filters( 'woocommerce_get_settings_' . $this->id . '_' . $current_section, array() ), array(
			array(
				'title'     => __( 'Reset Settings', 'product-countdown-for-woocommerce' ),
				'type'      => 'title',
				'id'        => $this->id . '_' . $current_section . '_reset_options',
			),
			array(
				'title'     => __( 'Reset section settings', 'product-countdown-for-woocommerce' ),
				'desc_tip'  => __( 'Check this box and save changes to reset section settings.', 'product-countdown-for-woocommerce' ),
				'desc'      => '<strong>' . __( 'Reset', 'product-countdown-for-woocommerce' ) . '</strong>',
				'id'        => $this->id . '_' . $current_section . '_reset',
				'default'   => 'no',
				'type'      => 'checkbox',
			),
			array(
				'type'      => 'sectionend',
				'id'        => $this->id . '_' . $current_section . '_reset_options',
			),
		) );

	}

	/**
	 * maybe_reset_settings.
	 *
	 * @version 1.2.0
	 * @since   1.2.0
	 */
	function maybe_reset_settings() {
		global $current_section;
		if ( 'yes' === get_option( $this->id . '_' . $current_section . '_reset', 'no' ) ) {
			foreach ( $this->get_settings() as $value ) {
				if ( isset( $value['default'] ) && isset( $value['id'] ) ) {
					delete_option( $value['id'] );
					$autoload = isset( $value['autoload'] ) ? ( bool ) $value['autoload'] : true;
					add_option( $value['id'], $value['default'], '', ( $autoload ? 'yes' : 'no' ) );
				}
			}
		}
	}

	/**
	 * Save settings.
	 *
	 * @version 1.2.0
	 * @since   1.2.0
	 */
	function save() {
		parent::save();
		$this->maybe_reset_settings();
	}

}

endif;

return new Alg_WC_Settings_Product_Countdown();
