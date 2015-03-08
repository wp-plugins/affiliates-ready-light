<?php
/**
 * affiliates-ready-light.php
 * 
 * Copyright (c) 2012 "kento" Karim Rahimpur www.itthinx.com
 * 
 * This code is released under the GNU General Public License.
 * See COPYRIGHT.txt and LICENSE.txt.
 * 
 * This code is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * This header and all notices must be kept intact.
 * 
 * @author Karim Rahimpur
 * @package affiliates-ready-light
 * @since affiliates-ready-light 1.0.0
 *
 * Plugin Name: Affiliates Ready! Ecommerce Integration Light
 * Plugin URI: http://www.itthinx.com/plugins/affiliates-ready-light/
 * Description: Integrates Affiliates with Ready! Ecommerce
 * Author: itthinx
 * Author URI: http://www.itthinx.com/
 * Version: 1.0.3
 */
define( 'AFF_READY_LIGHT_PLUGIN_DOMAIN', 'affiliates-ready-light' );

/**
 * Light integration class.
 */
class Affiliates_Ready_Light_Integration {

	const SHOP_ORDER_POST_TYPE  = 'shop_order';
	const PLUGIN_OPTIONS        = 'affiliates_ready_light';
	const AUTO_ADJUST_DEFAULT   = true;
	const NONCE                 = 'aff_ready_light_admin_nonce';
	const SET_ADMIN_OPTIONS     = 'set_admin_options';
	const REFERRAL_RATE         = "referral-rate";
	const REFERRAL_RATE_DEFAULT = "0";
	const USAGE_STATS           = 'usage_stats';
	const USAGE_STATS_DEFAULT   = true;

	private static $admin_messages = array();

	/**
	 * Prints admin notices.
	 */
	public static function admin_notices() {
		if ( !empty( self::$admin_messages ) ) {
			foreach ( self::$admin_messages as $msg ) {
				echo $msg;
			}
		}
	}

	/**
	 * Checks dependencies and adds appropriate actions and filters.
	 */
	public static function init() {

		add_action( 'admin_notices', array( __CLASS__, 'admin_notices' ) );

		$verified = true;
		$disable = false;
		$active_plugins = get_option( 'active_plugins', array() );
		if ( is_multisite() ) {
			$active_sitewide_plugins = get_site_option( 'active_sitewide_plugins', array() );
			$active_sitewide_plugins = array_keys( $active_sitewide_plugins );
			$active_plugins = array_merge( $active_plugins, $active_sitewide_plugins );
		}
		$affiliates_is_active = in_array( 'affiliates/affiliates.php', $active_plugins ) || in_array( 'affiliates-pro/affiliates-pro.php', $active_plugins ) || in_array( 'affiliates-enterprise/affiliates-enterprise.php', $active_plugins );
		$ready_is_active = in_array( 'ready-ecommerce/ecommerce.php', $active_plugins );
		$affiliates_ready_is_active = in_array( 'affiliates-ready-ecommerce/affiliates-ready-ecommerce.php', $active_plugins );
		if ( !$affiliates_is_active ) {
			self::$admin_messages[] = "<div class='error'>" . __( 'The <strong>Affiliates Ready! Ecommerce Integration Light</strong> plugin requires an Affiliates plugin to be activated: <a href="http://www.itthinx.com/plugins/affiliates" target="_blank">Visit the Affiliates plugin page</a>', AFF_READY_LIGHT_PLUGIN_DOMAIN ) . "</div>";
		}
		if ( !$ready_is_active ) {
			self::$admin_messages[] = "<div class='error'>" . __( 'The <strong>Affiliates Ready! Ecommerce Integration Light</strong> plugin requires the <a href="http://wordpress.org/extend/plugins/ready-ecommerce" target="_blank">Ready! Ecommerce</a> plugin to be activated.', AFF_READY_LIGHT_PLUGIN_DOMAIN ) . "</div>";
		}
		if ( $affiliates_ready_is_active ) {
			self::$admin_messages[] = "<div class='error'>" . __( 'You do not need to use the <srtrong>Affiliates Ready! Ecommerce Integration Light</strong> plugin because you are already using the advanced Affiliates Ready! Ecommerce Integration plugin. Please deactivate the <strong>Affiliates Ready! Ecommerce Integration Light</strong> plugin now.', AFF_READY_LIGHT_PLUGIN_DOMAIN ) . "</div>";
		}
		if ( !$affiliates_is_active || !$ready_is_active || $affiliates_ready_is_active ) {
			if ( $disable ) {
				include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
				deactivate_plugins( array( __FILE__ ) );
			}
			$verified = false;
		}

		if ( $verified ) {
			$options = get_option( self::PLUGIN_OPTIONS , array() );
			add_action( 'init', array( __CLASS__, 'wp_init' ) );
			add_action( 'affiliates_admin_menu', array( __CLASS__, 'affiliates_admin_menu' ) );
			add_action( 'affiliates_footer', array( __CLASS__, 'affiliates_footer' ) );
		}
	}
	
	/**
	 * Initialize order hook.
	 */
	public static function wp_init() {
		if ( class_exists( 'dispatcher' ) ) {
			dispatcher::addAction( 'onSuccessOrder', array( __CLASS__, 'onSuccessOrder' ) );
			dispatcher::addAction( 'orderPost', array( __CLASS__, 'orderPost' ) );
		}
	}

	/**
	 * Adds a submenu item to the Affiliates menu for the Ready! Ecommerce integration options.
	 */
	public static function affiliates_admin_menu() {
		$page = add_submenu_page(
			'affiliates-admin',
			__( 'Affiliates Ready! Ecommerce Integration Light', AFF_READY_LIGHT_PLUGIN_DOMAIN ),
			__( 'Ready! Light', AFF_READY_LIGHT_PLUGIN_DOMAIN ),
			AFFILIATES_ADMINISTER_OPTIONS,
			'affiliates-admin-ready-light',
			array( __CLASS__, 'affiliates_admin_ready_light' )
		);
		$pages[] = $page;
		add_action( 'admin_print_styles-' . $page, 'affiliates_admin_print_styles' );
		add_action( 'admin_print_scripts-' . $page, 'affiliates_admin_print_scripts' );
	}

	/**
	 * Affiliates Ready! Ecommerce Integration Light : admin section.
	 */
	public static function affiliates_admin_ready_light() {
		$output = '';
		if ( !current_user_can( AFFILIATES_ADMINISTER_OPTIONS ) ) {
			wp_die( __( 'Access denied.', AFF_READY_LIGHT_PLUGIN_DOMAIN ) );
		}
		$options = get_option( self::PLUGIN_OPTIONS , array() );
		if ( isset( $_POST['submit'] ) ) {
			if ( wp_verify_nonce( $_POST[self::NONCE], self::SET_ADMIN_OPTIONS ) ) {
				$options[self::REFERRAL_RATE]  = floatval( $_POST[self::REFERRAL_RATE] );
				if ( $options[self::REFERRAL_RATE] > 1.0 ) {
					$options[self::REFERRAL_RATE] = 1.0;
				} else if ( $options[self::REFERRAL_RATE] < 0 ) {
					$options[self::REFERRAL_RATE] = 0.0;
				}
				$options[self::USAGE_STATS] = !empty( $_POST[self::USAGE_STATS] );
			}
			update_option( self::PLUGIN_OPTIONS, $options );
		}

		$referral_rate = isset( $options[self::REFERRAL_RATE] ) ? $options[self::REFERRAL_RATE] : self::REFERRAL_RATE_DEFAULT; 
		$usage_stats   = isset( $options[self::USAGE_STATS] ) ? $options[self::USAGE_STATS] : self::USAGE_STATS_DEFAULT;

		$output .=
			'<div>' .
			'<h2>' .
			__( 'Affiliates Ready! Ecommerce Integration Light', AFF_READY_LIGHT_PLUGIN_DOMAIN ) .
			'</h2>' .
			'</div>';

		$output .= '<p class="manage" style="padding:1em;margin-right:1em;font-weight:bold;font-size:1em;line-height:1.62em">';
		$output .= __( 'You can support the development of the Affiliates plugin and get additional features with <em style="color:#00c000;">Affiliates Pro</em> available on <a href="http://www.itthinx.com/plugins/affiliates-pro/" target="_blank">itthinx.com</a>.', AFF_READY_LIGHT_PLUGIN_DOMAIN );
		$output .= '</p>';

		$output .= '<div class="manage" style="padding:2em;margin-right:1em;">';
		$output .= '<form action="" name="options" method="post">';        
		$output .= '<div>';
		$output .= '<h3>' . __( 'Referral Rate', AFF_READY_LIGHT_PLUGIN_DOMAIN ) . '</h3>';
		$output .= '<p>';
		$output .= '<label for="' . self::REFERRAL_RATE . '">' . __( 'Referral rate', AFF_READY_LIGHT_PLUGIN_DOMAIN) . '</label>';
		$output .= '&nbsp;';
		$output .= '<input name="' . self::REFERRAL_RATE . '" type="text" value="' . esc_attr( $referral_rate ) . '"/>';
		$output .= '</p>';
		$output .= '<p>';
		$output .= __( 'The referral rate determines the referral amount based on the net sale made.', AFF_READY_LIGHT_PLUGIN_DOMAIN );
		$output .= '</p>';
		$output .= '<p class="description">';
		$output .= __( 'Example: Set the referral rate to <strong>0.1</strong> if you want your affiliates to get a <strong>10%</strong> commission on each sale.', AFF_READY_LIGHT_PLUGIN_DOMAIN );
		$output .= '</p>';

		$output .= '<h3>' . __( 'Usage stats', AFF_READY_LIGHT_PLUGIN_DOMAIN ) . '</h3>';
		$output .= '<p>';
		$output .= '<input name="' . self::USAGE_STATS . '" type="checkbox" ' . ( $usage_stats ? ' checked="checked" ' : '' ) . '/>';
		$output .= ' ';
		$output .= '<label for="' . self::USAGE_STATS . '">' . __( 'Allow the plugin to provide usage stats.', AFF_READY_LIGHT_PLUGIN_DOMAIN ) . '</label>';
		$output .= '<br/>';
		$output .= '<span class="description">' . __( 'This will allow the plugin to help in computing how many installations are actually using it. No personal or site data is transmitted, this simply embeds an icon on the bottom of the Affiliates admin pages, so that the number of visits to these can be counted. This is useful to help prioritize development.', AFF_READY_LIGHT_PLUGIN_DOMAIN ) . '</span>';
		$output .= '</p>';

		$output .= '<p>';
		$output .= wp_nonce_field( self::SET_ADMIN_OPTIONS, self::NONCE, true, false );
		$output .= '<input type="submit" name="submit" value="' . __( 'Save', AFF_READY_LIGHT_PLUGIN_DOMAIN ) . '"/>';
		$output .= '</p>';

		$output .= '</div>';
		$output .= '</form>';
		$output .= '</div>';

		echo $output;

		affiliates_footer();
	}
	
	/**
	 * Add a notice to the footer that the integration is active.
	 * @param string $footer
	 */
	public static function affiliates_footer( $footer ) {
		$options = get_option( self::PLUGIN_OPTIONS , array() );
		$usage_stats   = isset( $options[self::USAGE_STATS] ) ? $options[self::USAGE_STATS] : self::USAGE_STATS_DEFAULT;
		return
			'<div style="font-size:0.9em">' .
			'<p>' .
			( $usage_stats ? "<img src='http://www.itthinx.com/img/affiliates-ready/affiliates-ready-light.png' alt='Logo'/>" : '' ) .
			__( "Powered by <a href='http://www.itthinx.com/plugins/affiliates-ready-light' target='_blank'>Affiliates Ready! Ecommerce Integration Light</a>.", AFF_READY_LIGHT_PLUGIN_DOMAIN ) .
			' ' .
			__( 'Get additional features with <a href="http://www.itthinx.com/plugins/affiliates-pro/" target="_blank">Affiliates Pro</a>.', AFF_READY_LIGHT_PLUGIN_DOMAIN ) .
			'</p>' .
			'</div>' .
			$footer;
	}

	public static function orderPost( $order_id ) {
		$order = new orderModel();
		if ( $order = $order->get( $order_id ) ) {
			self::onSuccessOrder( $order );
		}
	}

	/**
	 * Record a referral on successful order.
	 * 
	 * @param array $order
	 */
	public static function onSuccessOrder( $order ) {

		$order_id       = isset( $order['id'] ) ? $order['id'] : null;
		$order_subtotal = isset( $order['sub_total'] ) ? $order['sub_total'] : 0;
		$currency       = isset( $order['currency'] ) && isset( $order['currency']['code'] ) ? $order['currency']['code'] : 'USD';

		$data = array(
			'order_id' => array(
				'title' => 'Order #',
				'domain' => AFF_READY_LIGHT_PLUGIN_DOMAIN,
				'value' => esc_sql( $order_id )
			),
			'order_total' => array(
				'title' => 'Total',
				'domain' =>  AFF_READY_LIGHT_PLUGIN_DOMAIN,
				'value' => esc_sql( $order_subtotal )
			),
			'order_currency' => array(
				'title' => 'Currency',
				'domain' =>  AFF_READY_LIGHT_PLUGIN_DOMAIN,
				'value' => esc_sql( $currency )
			)
		);

		$options        = get_option( self::PLUGIN_OPTIONS , array() );
		$referral_rate  = isset( $options[self::REFERRAL_RATE] ) ? $options[self::REFERRAL_RATE] : self::REFERRAL_RATE_DEFAULT;
		$amount         = round( floatval( $referral_rate ) * floatval( $order_subtotal ), AFFILIATES_REFERRAL_AMOUNT_DECIMALS );
		$description    = sprintf( 'Order #%s', $order_id );
		$status = null;
		$order_status = isset( $order['status'] ) ? $order['status'] : null;
		switch ( $order_status ) {
			case 'created' :
			case 'pending' :
				$status = AFFILIATES_REFERRAL_STATUS_PENDING;
				break;
			case 'paid' :
			case 'confirmed' :
			case 'delivered' :
				$status = AFFILIATES_REFERRAL_STATUS_ACCEPTED;
				break;
			case 'cancelled' :
				$status = AFFILIATES_REFERRAL_STATUS_REJECTED;
				break;
		}
		global $post, $post_id;
		$_post_id = 0;// get_the_ID(); empty at this point
		affiliates_suggest_referral( $_post_id, $description, $data, $amount, $currency, $status, null, $order_id );
	}
}
Affiliates_Ready_Light_Integration::init();
