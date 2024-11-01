<?php

//exit if this file is called outside wordpress
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die();
}

require_once( plugin_dir_path( __FILE__ ) . 'shared/class-daextsolisc-shared.php' );
require_once( plugin_dir_path( __FILE__ ) . 'admin/class-daextsolisc-admin.php' );

//delete options and tables
daextsolisc_Admin::un_delete();
