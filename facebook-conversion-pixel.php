<?php
/*
Plugin Name: Facebook Conversion Pixel
Plugin URI: https://github.com/kellenmace/facebook-conversion-pixel
Description: Facebook's recommended plugin for adding Facebook Conversion Pixel code to WordPress sites.
Version: 1.2
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
	 * Include Custom Metaboxes and Fields Library
	 * @since 1.2
	 */
	if ( file_exists(  __DIR__ . '/includes/cmb2/init.php' ) ) {
	  require_once  __DIR__ . '/includes/cmb2/init.php';
	}

	/**
	 * Display meta box in admin
	 * @since 1.2
	 */
	function fb_pxl_display_meta_box() {
		$prefix = 'fb_pxl_';

		$options = get_option( 'fb_pxl_options' );
		$post_types = array();
		foreach ( $options as $post_type => $checkbox ) {
			if ( 'on' == $checkbox )
				array_push( $post_types, $post_type );
		}

		$metabox = new_cmb2_box( array(
			'id'            => $prefix . 'metabox',
			'title'         => 'Facebook Conversion Pixel',
			'object_types'  => $post_types,
			'context'       => 'normal',
			'priority'      => 'high',
			'show_names'    => true,
		) );

		$metabox->add_field( array(
			'name' => 'Insert Code',
			'desc' => 'Insert Facebook Conversion Pixel code on this page',
			'id'   => $prefix . 'checkbox',
			'type' => 'checkbox',
		) );

		$metabox->add_field( array(
			'name' => 'Conversion Pixel',
			'desc' => 'Paste your Facebook Conversion Pixel code here',
			'id'   => $prefix . 'conversion_code',
			'type' => 'textarea_code',
		) );
	}
	add_filter( 'cmb2_init', 'fb_pxl_display_meta_box' );
	
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