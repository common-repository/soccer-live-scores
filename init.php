<?php

/*
Plugin Name: Soccer Live Scores
Description: Generates live soccer scores in your posts, pages and custom posts.
Version: 1.05
Author: DAEXT
Author URI: https://daext.com
Text Domain: soccer-live-scores
*/

//Prevent direct access to this file
if ( ! defined( 'WPINC' ) ) {
	die();
}

//Class shared across public and admin
require_once( plugin_dir_path( __FILE__ ) . 'shared/class-daextsolisc-shared.php' );

//Public
require_once( plugin_dir_path( __FILE__ ) . 'public/class-daextsolisc-public.php' );
add_action( 'plugins_loaded', array( 'Daextsolisc_Public', 'get_instance' ) );

//Admin
if ( is_admin() ) {

	require_once( plugin_dir_path( __FILE__ ) . 'admin/class-daextsolisc-admin.php' );

	// If this is not an AJAX request, create a new singleton instance of the admin class.
	if(! defined( 'DOING_AJAX' ) || ! DOING_AJAX ){
		add_action( 'plugins_loaded', array( 'Daextsolisc_Admin', 'get_instance' ) );
	}

	// Activate the plugin using only the class static methods.
	register_activation_hook( __FILE__, array( 'Daextsolisc_Admin', 'ac_activate' ) );

}

//Ajax
if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {

	//Admin
	require_once( plugin_dir_path( __FILE__ ) . 'class-daextsolisc-ajax.php' );
	add_action( 'plugins_loaded', array( 'Daextsolisc_Ajax', 'get_instance' ) );

}

//Customize the action links in the "Plugins" menu
function daextsolisc_customize_action_links( $actions ) {
	$actions[] = '<a href="https://daext.com/soccer-engine/">' . esc_html__('Buy the Pro Version', 'soccer-live-scores') . '</a>';
	return $actions;
}
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'daextsolisc_customize_action_links' );