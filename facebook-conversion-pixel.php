<?php
/*
Plugin Name: Facebook Conversion Pixel
Plugin URI: https://github.com/kellenmace/facebook-conversion-pixel
Description: Facebook's recommended plugin for adding Facebook Conversion Pixel code to WordPress sites.
Version: 1.1
Author: Kellen Mace
Author URI: http://kellenmace.com/
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

/**
 * Insert Facebook Conversion Pixel
 * @since 1.0
 */
if ( ! is_admin() ) {
	function fb_pxl_head() {
		 global $post;
		 $the_id = $post->ID;
		 $fb_pxl_options = get_option( 'fb_pxl_options' );

		 // If user has enabled this post type
		 if ( 'on' == $fb_pxl_options[ $post->post_type ] ) {
		 	$fb_pxl_switch = get_post_meta( $the_id, 'fb_pxl_checkbox', true);

		 	// If user has chosen to insert code, insert it
			if ( 'on' == $fb_pxl_switch ) {
			 	$fb_pxl_code = get_post_meta( $the_id, 'fb_pxl_conversion_code', true);
			 	echo $fb_pxl_code;
			 }
		 }
	}
	add_action( 'wp_head', 'fb_pxl_head' );
}

elseif ( is_admin() ) {
	/**
	 * Include plugin options page
	 * @since 1.0
	 */
	include_once( plugin_dir_path( __FILE__ ) . 'includes/admin.php' );

	/**
	 * Display meta box in admin
	 * @since 1.1
	 */
	function fb_pxl_meta( array $meta_boxes ) {
		$prefix = 'fb_pxl_';
		$options = get_option( 'fb_pxl_options' );
		$pages = array();
		foreach ( $options as $option_key => $option_value ) {
			if ( 'on' == $option_value )
				array_push( $pages, $option_key );
		}

		$meta_boxes[] = array(
			'id'         => 'fb_pxl_metabox',
			'title'      => 'Facebook Conversion Pixel Code',
			'pages'      => $pages,
			'context'    => 'normal',
			'priority'   => 'high',
			'show_names' => true,
			'fields'     => array(     
				array(
					'name' => 'Insert Code',
					'desc' => 'Insert Facebook Conversion Pixel code',
					'id'   => $prefix . 'checkbox',
					'type' => 'checkbox',
				),
				array(
					'name' => 'Conversion Pixel JavaScript',
					'desc' => 'Paste your Facebook Conversion Pixel code here',
					'id'   => $prefix . 'conversion_code',
					'type' => 'textarea_code',
				),
			),
		);
		return $meta_boxes;
	}
	add_filter( 'cmb_meta_boxes', 'fb_pxl_meta' );

	/**
	 * Include Custom Metaboxes and Fields Library
	 * @since 1.0
	 */
	function fb_pxl_init_mtbxs() {
		if ( ! class_exists( 'cmb_Meta_Box' ) )
			require_once( plugin_dir_path( __FILE__ ) . 'includes/init.php' );
	}
	add_action( 'init', 'fb_pxl_init_mtbxs', 9999 );
	
	/**
	 * Display settings link on WP plugin page
	 * @since 1.0
	 */
	function fb_pxl_plugin_action_links( $links, $file ) {
		$plugin_file = 'facebook-conversion-pixel/facebook-conversion-pixel.php';
		if ( $file == $plugin_file ) {
			$settings_link = '<a href="' . admin_url( 'admin.php?page=fb_pxl_options' ) . '">' . __( 'Settings', 'facebook-conversion-pixel' ) . '</a>';
			array_unshift( $links, $settings_link );
		}
		return $links;
	}
	add_filter( "plugin_action_links", 'fb_pxl_plugin_action_links', 10, 4 );
}

/**
 * Set default options on activation
 * @since 1.1
 */
function fb_pxl_activate() {
	$options = array(
		'post'			=> 'on',
	    'page' 			=> 'on'
	);
	update_option( 'fb_pxl_options', $options );
}
register_activation_hook( __FILE__, 'fb_pxl_activate' );
?>