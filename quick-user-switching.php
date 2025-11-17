<?php
/**
 * Quick User Switching functionality.
 *
 * This file defines constants, checks for core requirements, and includes the
 * necessary files to power the user switching functionality.
 *
 * @package Hussainas_Quick_User_Switching
 * @version     1.0.0
 * @author      Hussain Ahmed Shrabon
 * @license     GPLv2 or later
 * @link        https://github.com/iamhussaina
 * @textdomain  hussainas
 */

// Do not allow direct access to this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct access is not allowed.' );
}

// Define module constants.
define( 'HUSSAINAS_QUS_PATH', trailingslashit( __DIR__ ) );
define( 'HUSSAINAS_QUS_VERSION', '1.0.0' );

// Include required module files.
require_once HUSSAINAS_QUS_PATH . 'includes/core-functions.php';
require_once HUSSAINAS_QUS_PATH . 'includes/admin-bar.php';

/**
 * Initializes the user switching functionality by hooking into WordPress.
 */
function hussainas_qus_initialize() {
	// Hook the admin bar menu creation.
	add_action( 'admin_bar_menu', 'hussainas_qus_add_admin_bar_menu', 999 );

	// Hook the listener for switch requests.
	add_action( 'init', 'hussainas_qus_handle_switch_requests' );
}

// Start the module.
hussainas_qus_initialize();
