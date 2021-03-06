<?php

if ( !defined( 'ABSPATH' ) )
	die( '-1' );

/**
 * WC_Wishlist_Settings class.
 *
 * @extends WC_Settings_API
 */
class WC_Wishlist_Settings extends WC_Settings_API {

	private $default;
	protected static $instance;

	const PREFIX = 'woocommerce_wishlist_';

	function __construct() {

		$this->default['css_colors_enabled'] = 'yes';

		// color defaults
		$this->default[ 'css_colors'] = array(
			'border' => '#bbbbbb',
			'header_bg' => '#eeeeee',
			'header_bg_notify' => '#e6db55',
			'header_text' => '#666666',
			'background' => '#ffffff',
			'text' => '#777777',
			'product_icon' => '#ffffff',
		);

		$this->default['unique_url_length'] = 6;

		$this->default['store_only'] = 'no';

		$this->default['wp_footer_enabled'] = 'yes';

		$this->default['frontend_label'] = __('Saved Items');

		// limit the amount of products someone can add to their wishlist
		$this->default[ 'limit_add_amount' ] = apply_filters( 'woocommerce_wishlist_setting_default_limit_add_amount', 20 );

		do_action( 'woocommerce_wishlist_settings' );

		add_action( 'woocommerce_general_settings', array( $this, 'add_general_fields' ) );
		add_action( 'woocommerce_admin_field_woocommerce_wishlist_styles', array( $this, 'style_picker' ) );
		add_action( 'woocommerce_update_options_general', array( $this, 'update_options' ) );
	}

	function get_defaults(){
		return self::instance()->default;
	}

	function get_option( $key = null ) {
		if ( is_null( $key ) ) {
			return false;
		} else {
			$option = get_option( self::PREFIX . $key );
			if ( empty( $option ) ) {
				if( isset( self::instance()->default[ $key ] )) {
					return self::instance()->default[ $key ];
				} else {
					return false;
				}
			} else {
				return $option;
			}
		}
	}

	function update_options() {

		// Handle Colour Settings
		$new_colors = array(
			'border'  => ! empty( $_POST['woocommerce_wishlist_css_border'] ) ? woocommerce_format_hex( $_POST['woocommerce_wishlist_css_border'] ) : '',
			'header_bg' => ! empty( $_POST['woocommerce_wishlist_css_header_bg'] ) ? woocommerce_format_hex( $_POST['woocommerce_wishlist_css_header_bg'] ) : '',
			'header_bg_notify' => ! empty( $_POST['woocommerce_wishlist_css_header_bg_notify'] ) ? woocommerce_format_hex( $_POST['woocommerce_wishlist_css_header_bg_notify'] ) : '',
			'header_text' => ! empty( $_POST['woocommerce_wishlist_css_header_text'] ) ? woocommerce_format_hex( $_POST['woocommerce_wishlist_css_header_text'] ) : '',
			'background' => ! empty( $_POST['woocommerce_wishlist_css_bg'] ) ? woocommerce_format_hex( $_POST['woocommerce_wishlist_css_bg'] ) : '',
			'text' => ! empty( $_POST['woocommerce_wishlist_css_text'] ) ? woocommerce_format_hex( $_POST['woocommerce_wishlist_css_text'] ) : '',
			'product_icon' => ! empty( $_POST['woocommerce_wishlist_css_product_icon'] ) ? woocommerce_format_hex( $_POST['woocommerce_wishlist_css_product_icon'] ) : ''
		);

		$old_colors = get_option( self::PREFIX . 'css_colors' );

		if ( $old_colors != $new_colors ) {
			update_option( self::PREFIX . 'css_colors', $new_colors );
			// WC_compile_less_styles();
		}
	}

	function add_general_fields( $fields ) {

		$fields = array_merge( $fields, array(
				array( 'name' => __( 'Save For Later Options', 'woocommerce_wishlist' ), 'type' => 'title', 'desc' => '', 'id' => 'woocommerce_wishlist_options' ),
				array(
					'name' => __( "Enable Styles", 'woocommerce_wishlist' ),
					'desc'   => __( "Enable the wishlist css styles", 'woocommerce_wishlist' ),
					'id'   => 'woocommerce_wishlist_css_colors_enabled',
					'std'   => $this->default['css_colors_enabled'],
					'type'   => 'checkbox',
				),
				array(
					'type' => 'woocommerce_wishlist_styles'
				),
				array(
					'name' => __( "Enable Dock", 'woocommerce_wishlist' ),
					'desc'   => __( "", 'woocommerce_wishlist' ),
					'id'   => 'woocommerce_wishlist_wp_footer_enabled',
					'std'   => $this->default['wp_footer_enabled'],
					'type'   => 'checkbox',
				),
				array(
					'name' => __( "Show In Store Only", 'woocommerce_wishlist' ),
					'desc'   => __( "Show the wishlist dock only on store pages, otherwise will show through whole site", 'woocommerce_wishlist' ),
					'id'   => 'woocommerce_wishlist_store_only',
					'std'   => $this->default['store_only'],
					'type'   => 'checkbox',
				),
				array( 'type' => 'sectionend', 'id' => 'woocommerce_wishlist_options' ) ) );

		return $fields;
	}

	function style_picker() {

		// Get settings & defaults
		$colors = wp_parse_args( (array) get_option( 'woocommerce_wishlist_css_colors' ), $this->default['css_colors'] );

		?><tr valign="top" class="woocommerce_css_colors">
		<th scope="row" class="titledesc">
			<label><?php _e( 'Styles', 'woocommerce_wishlist' ); ?></label>
		</th>
	    <td class="forminp"><?php

		// Show inputs
		woocommerce_frontend_css_color_picker( __( 'Borders', 'woocommerce_wishlist' ), 'woocommerce_wishlist_css_border', $colors['border'], __( 'The border between the clickable header and the rest of the page, and border around product images', 'woocommerce_wishlist' ) );
		woocommerce_frontend_css_color_picker( __( 'Header Bg', 'woocommerce_wishlist' ), 'woocommerce_wishlist_css_header_bg', $colors['header_bg'], __( 'Clickable header background', 'woocommerce_wishlist' ) );
		woocommerce_frontend_css_color_picker( __( 'Notify Bg', 'woocommerce_wishlist' ), 'woocommerce_wishlist_css_header_bg_notify', $colors['header_bg_notify'], __( 'Header notify background', 'woocommerce_wishlist' ) );
		woocommerce_frontend_css_color_picker( __( 'Header Text', 'woocommerce_wishlist' ), 'woocommerce_wishlist_css_header_text', $colors['header_text'], __( 'Clickable header text color', 'woocommerce_wishlist' ) );
		woocommerce_frontend_css_color_picker( __( 'List Bg', 'woocommerce_wishlist' ), 'woocommerce_wishlist_css_bg', $colors['background'], __( 'Product list background', 'woocommerce_wishlist' ) );
		woocommerce_frontend_css_color_picker( __( 'List Text', 'woocommerce_wishlist' ), 'woocommerce_wishlist_css_text', $colors['text'], __( 'Product list text color', 'woocommerce_wishlist' ) );
		woocommerce_frontend_css_color_picker( __( 'Icon', 'woocommerce_wishlist' ), 'woocommerce_wishlist_css_product_icon', $colors['product_icon'], __( 'The color of icons that overlay over the product images in the shop and wishlist', 'woocommerce_wishlist' ) );

		echo '</td></tr>';
	}

	/* Static Singleton Factory Method */
	public static function instance() {
		if ( !isset( self::$instance ) ) {
			$class_name = __CLASS__;
			self::$instance = new $class_name;
		}
		return self::$instance;
	}

}
