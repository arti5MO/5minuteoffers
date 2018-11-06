<?php
/**
 * Product Time Countdown for WooCommerce - Core Class
 *
 * @version 1.4.0
 * @since   1.0.0
 * @author  Algoritmika Ltd.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Product_Countdown_Core' ) ) :

class Alg_WC_Product_Countdown_Core {

	/**
	 * Constructor.
	 *
	 * @version 1.4.0
	 * @since   1.0.0
	 * @todo    [feature] maybe add countdown per variation
	 */
	function __construct() {

		if ( 'yes' === get_option( 'alg_wc_product_countdown_enabled', 'yes' ) ) {

			$this->is_wc_version_below_3 = version_compare( get_option( 'woocommerce_version', null ), '3.0.0', '<' );

			// Admin settings
			if ( is_admin() ) {
				require_once( 'settings/class-alg-wc-product-countdown-metaboxes.php' );
			}

			// Scripts
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles_and_scripts' ) );

			// AJAX
			add_action( 'wp_ajax_alg_product_countdown',        array( $this, 'ajax_alg_product_countdown' ) );
			add_action( 'wp_ajax_nopriv_alg_product_countdown', array( $this, 'ajax_alg_product_countdown' ) );

			// Frontend - Single product page
			if ( 'disable' != ( $position = apply_filters( 'alg_wc_product_countdown', 'woocommerce_single_product_summary', 'value_position' ) ) ) {
				add_action( $position, array( $this, 'add_counter_to_frontend' ), apply_filters( 'alg_wc_product_countdown', 10, 'value_position_priority' ) );
			}

			// Frontend - Archives
			if ( 'disable' != ( $position = apply_filters( 'alg_wc_product_countdown', 'disable', 'value_loop_position' ) ) ) {
				add_action( $position, array( $this, 'add_counter_to_frontend' ), apply_filters( 'alg_wc_product_countdown', 10, 'value_loop_position_priority' ) );
			}

			// "Disable product" action
			if ( 'yes' === get_option( 'alg_wc_product_countdown_disable_product_action_purchasable', 'yes' ) ) {
				add_filter( 'woocommerce_is_purchasable',     array( $this, 'purchasable_check_date' ), PHP_INT_MAX, 2 );
			}
			if ( 'yes' === apply_filters( 'alg_wc_product_countdown', 'no', 'value_disable_product_action_visibility' ) ) {
				add_filter( 'woocommerce_product_is_visible', array( $this, 'visibility_check_date' ), PHP_INT_MAX, 2 );
			}
			if ( 'yes' === apply_filters( 'alg_wc_product_countdown', 'no', 'value_disable_product_action_query' ) ) {
				add_action( 'pre_get_posts',                  array( $this, 'pre_get_posts_check_date' ) );
			}

			// Cancel sale
			$price_filter      = ( $this->is_wc_version_below_3 ? 'woocommerce_get_price'      : 'woocommerce_product_get_price'      );
			$sale_price_filter = ( $this->is_wc_version_below_3 ? 'woocommerce_get_sale_price' : 'woocommerce_product_get_sale_price' );
			add_filter( $price_filter,                                         array( $this, 'change_price' ),              PHP_INT_MAX, 2 );
			add_filter( $sale_price_filter,                                    array( $this, 'change_sale_price' ),         PHP_INT_MAX, 2 );
			add_filter( 'woocommerce_variation_prices_price',                  array( $this, 'change_price' ),              PHP_INT_MAX, 2 );
			add_filter( 'woocommerce_variation_prices_sale_price',             array( $this, 'change_sale_price' ),         PHP_INT_MAX, 2 );
			add_filter( 'woocommerce_get_variation_prices_hash',               array( $this, 'get_variation_prices_hash' ), PHP_INT_MAX, 3 );
			if ( ! $this->is_wc_version_below_3 ) {
				add_filter( 'woocommerce_product_variation_get_price',         array( $this, 'change_price' ),              PHP_INT_MAX, 2 );
				add_filter( 'woocommerce_product_variation_get_sale_price',    array( $this, 'change_sale_price' ),         PHP_INT_MAX, 2 );
			}

			// Out of stock
			add_filter( 'woocommerce_product_is_in_stock', array( $this, 'stock_check_date' ), PHP_INT_MAX, 2 );

			// Shortcode
			if ( apply_filters( 'alg_wc_product_countdown', false, 'shortcode' ) ) {
				add_shortcode( 'product_time_counter', array( $this, 'get_counter_shortcode' ) );
			}

			// Admin products list columns
			if ( 'yes' === get_option( 'alg_wc_product_countdown_add_admin_column', 'no' ) ) {
				add_filter( 'manage_edit-product_columns',        array( $this, 'add_product_column' ),    PHP_INT_MAX );
				add_action( 'manage_product_posts_custom_column', array( $this, 'render_product_column' ), PHP_INT_MAX );
			}

			// Language shortcode
			add_shortcode( 'alg_wc_ptc_translate', array( $this, 'language_shortcode' ) );

		}
	}

	/**
	 * language_shortcode.
	 *
	 * @version 1.4.0
	 * @since   1.4.0
	 * @see     https://wpml.org/errata/unable-to-get-site-language-on-the-front-end-ajax-calls-when-user-is-not-logged-in/
	 */
	function language_shortcode( $atts, $content = '' ) {
		// E.g.: `[alg_wc_ptc_translate lang="EN,DE" lang_text="Text for EN & DE" not_lang_text="Text for other languages"]`
		if ( isset( $atts['lang_text'] ) && isset( $atts['not_lang_text'] ) && ! empty( $atts['lang'] ) ) {
			return ( ! defined( 'ICL_LANGUAGE_CODE' ) || ! in_array( strtolower( ICL_LANGUAGE_CODE ), array_map( 'trim', explode( ',', strtolower( $atts['lang'] ) ) ) ) ) ?
				$atts['not_lang_text'] : $atts['lang_text'];
		}
		// E.g.: `[alg_wc_ptc_translate lang="EN,DE"]Text for EN & DE[/alg_wc_ptc_translate][alg_wc_ptc_translate not_lang="EN,DE"]Text for other languages[/alg_wc_ptc_translate]`
		return (
			( ! empty( $atts['lang'] )     && ( ! defined( 'ICL_LANGUAGE_CODE' ) || ! in_array( strtolower( ICL_LANGUAGE_CODE ), array_map( 'trim', explode( ',', strtolower( $atts['lang'] ) ) ) ) ) ) ||
			( ! empty( $atts['not_lang'] ) &&     defined( 'ICL_LANGUAGE_CODE' ) &&   in_array( strtolower( ICL_LANGUAGE_CODE ), array_map( 'trim', explode( ',', strtolower( $atts['not_lang'] ) ) ) ) )
		) ? '' : $content;
	}

	/**
	 * is_product_countdown_enabled.
	 *
	 * @version 1.4.0
	 * @since   1.4.0
	 */
	function is_product_countdown_enabled( $product_id ) {
		return ( 'yes' === get_post_meta( $product_id, '_' . 'alg_product_countdown_enabled', true ) );
	}

	/**
	 * is_product_sale_canceled.
	 *
	 * @version 1.4.0
	 * @since   1.4.0
	 * @todo    [dev] maybe remove sale price HTML with JS (if "Reload page" option is not enabled)
	 */
	function is_product_sale_canceled( $product_id ) {
		return (
			$this->is_product_countdown_enabled( $product_id ) &&
			'cancel_sale' === get_post_meta( $product_id, '_' . 'alg_product_countdown_action', true ) &&
			$this->get_time_left( $product_id ) <= 0
		);
	}

	/**
	 * get_main_product_id.
	 *
	 * @version 1.4.0
	 * @since   1.4.0
	 */
	function get_main_product_id( $_product ) {
		return ( $this->is_wc_version_below_3 ? $_product->id : ( $_product->is_type( 'variation' ) ? $_product->get_parent_id() : $_product->get_id() ) );
	}

	/**
	 * change_price.
	 *
	 * @version 1.4.0
	 * @since   1.4.0
	 */
	function change_price( $price, $_product ) {
		return ( $this->is_product_sale_canceled( $this->get_main_product_id( $_product ) ) ? $_product->get_regular_price() : $price );
	}

	/**
	 * change_sale_price.
	 *
	 * @version 1.4.0
	 * @since   1.4.0
	 */
	function change_sale_price( $price, $_product ) {
		return ( $this->is_product_sale_canceled( $this->get_main_product_id( $_product ) ) ? '' : $price );
	}

	/**
	 * get_variation_prices_hash.
	 *
	 * @version 1.4.0
	 * @since   1.4.0
	 */
	function get_variation_prices_hash( $price_hash, $_product, $display ) {
		$price_hash['alg_wc_product_countdown'] = array(
			'is_product_sale_canceled' => $this->is_product_sale_canceled( $this->get_main_product_id( $_product ) ),
		);
		return $price_hash;
	}

	/**
	 * add_product_column.
	 *
	 * @version 1.3.0
	 * @since   1.3.0
	 */
	function add_product_column( $columns ) {
		$columns[ 'alg_wc_product_countdown_column' ] = __( 'Countdown', 'product-countdown-for-woocommerce' );
		return $columns;
	}

	/**
	 * render_product_column.
	 *
	 * @version 1.4.0
	 * @since   1.3.0
	 */
	function render_product_column( $column ) {
		if ( 'alg_wc_product_countdown_column' === $column ) {
			$product_id = get_the_ID();
			$result     = '';
			if ( $this->is_product_countdown_enabled( $product_id ) ) {
				$result .= get_post_meta( $product_id, '_' . 'alg_product_countdown_date', true ) . ' ' .
					get_post_meta( $product_id, '_' . 'alg_product_countdown_time', true );
			}
			echo $result;
		}
	}

	/**
	 * is_product_disabled.
	 *
	 * @version 1.4.0
	 * @since   1.3.0
	 */
	function is_product_disabled( $product_id ) {
		return (
			$this->is_product_countdown_enabled( $product_id ) &&
			'disable_product' === get_post_meta( $product_id, '_' . 'alg_product_countdown_action',  true ) &&
			$this->get_time_left( $product_id ) <= 0
		);
	}

	/**
	 * pre_get_posts_check_date.
	 *
	 * @version 1.4.0
	 * @since   1.3.0
	 */
	function pre_get_posts_check_date( $query ) {
		if ( is_admin() ) {
			return;
		}
		remove_action( 'pre_get_posts', array( $this, 'pre_get_posts_check_date' ) );
		$post__not_in   = $query->get( 'post__not_in' );
		$args           = array( 'post_type' => 'product', 'posts_per_page' => -1, 'fields' => 'ids', 'post__not_in' => $post__not_in );
		$loop           = new WP_Query( $args );
		foreach ( $loop->posts as $product_id ) {
			if ( $this->is_product_disabled( $product_id ) ) {
				$post__not_in[] = $product_id;
			}
		}
		$query->set( 'post__not_in', $post__not_in );
		add_action( 'pre_get_posts', array( $this, 'pre_get_posts_check_date' ) );
	}

	/**
	 * visibility_check_date.
	 *
	 * @version 1.3.0
	 * @since   1.3.0
	 */
	function visibility_check_date( $visible, $product_id ) {
		return ( $this->is_product_disabled( $product_id ) ? false : $visible );
	}

	/**
	 * get_time_left_formatted.
	 *
	 * @version 1.4.0
	 * @since   1.0.0
	 * @todo    [feature] maybe customizable upper time period, e.g.: days, minutes, seconds; also maybe: years, months, weeks (currently hours)
	 * @todo    [feature] maybe customizable width (now `2`)
	 */
	function get_time_left_formatted( $product_id ) {
		if ( ( $result = $this->get_time_left( $product_id ) ) <= 0 ) {
			return '';
		} else {
			if ( 'no' === get_option( 'alg_wc_product_countdown_format_human_time_diff', 'no' ) ) {
				$hours    = floor( $result / 3600 );
				$minutes  = floor( ( $result / 60 ) % 60 );
				$seconds  = $result % 60;
				$format   = get_option( 'alg_wc_product_countdown_time_format', '{hours}:{minutes}:{seconds}' );
				$replace  = array(
					'{hours}'   => sprintf( '%02d', $hours ),
					'{minutes}' => sprintf( '%02d', $minutes ),
					'{seconds}' => sprintf( '%02d', $seconds ),
				);
				$the_time = str_replace( array_keys( $replace ), $replace, $format );
			} else {
				$the_time = human_time_diff( 0, $result );
			}
			return do_shortcode( sprintf( get_option( 'alg_wc_product_countdown_format', '%s left' ), $the_time ) );
		}
	}

	/**
	 * get_time_left.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function get_time_left( $product_id ) {
		$finish_time  = get_post_meta( $product_id, '_' . 'alg_product_countdown_date', true ) . ' ' .
			get_post_meta( $product_id, '_' . 'alg_product_countdown_time', true );
		$finish_time  = strtotime( $finish_time );
		$current_time = (int) current_time( 'timestamp' );
		return ( $finish_time - $current_time );
	}

	/**
	 * ajax_alg_product_countdown.
	 *
	 * @version 1.4.0
	 * @since   1.0.0
	 */
	function ajax_alg_product_countdown() {
		if ( isset( $_POST['product_ids'] ) && is_array( $_POST['product_ids'] ) ) {
			$result = array();
			foreach ( $_POST['product_ids'] as $product_id ) {
				if ( $this->is_product_countdown_enabled( $product_id ) ) {
					$result[ $product_id ] = $this->get_time_left_formatted( $product_id );
				}
			}
			if ( ! empty( $result ) ) {
				echo json_encode( $result );
			}
		}
		die();
	}

	/**
	 * purchasable_check_date.
	 *
	 * @version 1.4.0
	 * @since   1.0.0
	 */
	function purchasable_check_date( $purchasable, $_product ) {
		return ( $this->is_product_disabled( $this->get_main_product_id( $_product ) ) ? false : $purchasable );
	}

	/**
	 * is_product_out_of_stock.
	 *
	 * @version 1.4.0
	 * @since   1.4.0
	 */
	function is_product_out_of_stock( $product_id ) {
		return (
			$this->is_product_countdown_enabled( $product_id ) &&
			'sell_out' === get_post_meta( $product_id, '_' . 'alg_product_countdown_action',  true ) &&
			$this->get_time_left( $product_id ) <= 0
		);
	}

	/**
	 * stock_check_date.
	 *
	 * @version 1.4.0
	 * @since   1.4.0
	 */
	function stock_check_date( $is_in_stock, $_product ) {
		return ( $this->is_product_out_of_stock( $this->get_main_product_id( $_product ) ) ? false : $is_in_stock );
	}

	/**
	 * get_counter_html.
	 *
	 * @version 1.4.0
	 * @since   1.2.0
	 * @todo    [feature] maybe add option to set different styling for `alg_product_countdown_time_ended`
	 */
	function get_counter_html( $product_id = 0 ) {
		if ( 0 == $product_id ) {
			$product_id = get_the_ID();
		}
		if ( $this->is_product_countdown_enabled( $product_id ) ) {
			if ( $this->get_time_left( $product_id ) > 0 ) {
				return '<span ' .
					'class="alg_product_countdown" ' .
					'product_id="' . $product_id . '" ' .
					'style="'      . get_option( 'alg_wc_product_countdown_style', 'font-size: xx-large; font-weight: bold;' ). '"' .
				'>' . '</span>';
			} else {
				return '<span ' .
					'class="alg_product_countdown_time_ended" ' .
					'product_id="' . $product_id . '" ' .
					'style="'      . get_option( 'alg_wc_product_countdown_style', 'font-size: xx-large; font-weight: bold;' ). '"' .
				'>' . get_option( 'alg_wc_product_countdown_message_on_time_finished', '' ) . '</span>';
			}
		}
	}

	/**
	 * add_counter_to_frontend.
	 *
	 * @version 1.2.0
	 * @since   1.0.0
	 */
	function add_counter_to_frontend() {
		echo $this->get_counter_html();
	}

	/**
	 * get_counter_shortcode.
	 *
	 * @version 1.2.0
	 * @since   1.2.0
	 * @todo    [dev] maybe rename to `[alg_product_time_counter]`
	 */
	function get_counter_shortcode( $atts ) {
		$product_id = ( isset( $atts['product_id'] ) ? $atts['product_id'] : 0 );
		return $this->get_counter_html();
	}

	/**
	 * enqueue_styles_and_scripts.
	 *
	 * @version 1.4.0
	 * @since   1.0.0
	 * @todo    [dev] maybe optional simple JS (i.e. no AJAX)
	 * @todo    [dev] maybe pass initial `time_left` to JS (so first update would happen faster, i.e. without first call to AJAX) or maybe just output `$this->get_time_left_formatted( $product_id )` inside of `<span>` in `get_counter_html()`
	 */
	function enqueue_styles_and_scripts() {
		wp_enqueue_script( 'alg-wc-product-time-countdown-ajax',
			untrailingslashit( plugin_dir_url( __FILE__ ) ) . '/js/alg-wc-product-time-countdown-ajax.js',
			array( 'jquery' ),
			alg_wc_product_countdown()->version,
			true
		);
		wp_localize_script(
			'alg-wc-product-time-countdown-ajax',
			'alg_data_countdown',
			array(
				'update_rate_ms' => get_option( 'alg_wc_product_countdown_update_rate_ms', 1000 ),
				'do_reload'      => (
					( 'yes'        === get_option( 'alg_wc_product_countdown_reload_page', 'no' ) ) ||
					( 'yes_single' === get_option( 'alg_wc_product_countdown_reload_page', 'no' ) && is_product() )
				),
				'end_message'    => get_option( 'alg_wc_product_countdown_message_on_time_finished', '' ),
				'ajax_url'       => admin_url( 'admin-ajax.php' ),
			)
		);
	}

}

endif;

return new Alg_WC_Product_Countdown_Core();
