<?php
/**
 * Core functionality for handling user switching logic.
 *
 * This file contains the functions responsible for processing switch-to-user
 * and switch-back-to-admin requests, including nonce verification,
 * cookie handling, and redirection.
 *
 * @package Hussainas_Quick_User_Switching
 */

// Do not allow direct access to this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct access is not allowed.' );
}

/**
 * Handles all incoming user switch requests (both switch-to and switch-back).
 *
 * This function checks for specific query arguments on 'init', verifies nonces
 * and permissions, and performs the user switch or switch-back action.
 */
function hussainas_qus_handle_switch_requests() {
	// 1. Check for a "Switch To User" request.
	if ( isset( $_GET['hussainas_qus_switch_to'] ) && isset( $_GET['_wpnonce'] ) ) {
		hussainas_qus_process_switch_to();
	}

	// 2. Check for a "Switch Back" request.
	if ( isset( $_GET['hussainas_qus_switch_back'] ) && isset( $_GET['_wpnonce'] ) ) {
		hussainas_qus_process_switch_back();
	}
}

/**
 * Processes the "Switch To User" action.
 *
 * Verifies the nonce, checks admin capabilities, sets the new user's auth cookie,
 * stores the original admin's ID in a cookie, and redirects.
 */
function hussainas_qus_process_switch_to() {
	$target_user_id = absint( $_GET['hussainas_qus_switch_to'] );

	// Verify nonce.
	if ( ! wp_verify_nonce( $_GET['_wpnonce'], 'hussainas_qus_switch_to_nonce_' . $target_user_id ) ) {
		wp_die( __( 'Security check failed. Please try again.', 'hussainas' ) );
	}

	// Verify capability. Only admins can switch.
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( __( 'You do not have permission to perform this action.', 'hussainas' ) );
	}

	// Get the original admin ID.
	$original_admin_id = get_current_user_id();

	// Log in the new user.
	wp_set_auth_cookie( $target_user_id );

	// Store the original admin ID in a cookie.
	// This cookie stores the ID of the admin who initiated the switch.
	$cookie_name = 'hussainas_qus_original_admin';
	$cookie_value = $original_admin_id;
	$cookie_expiry = time() + HOUR_IN_SECONDS; // Expires in 1 hour.
	
	// Set the cookie securely.
	setcookie( $cookie_name, $cookie_value, $cookie_expiry, COOKIEPATH, COOKIE_DOMAIN, is_ssl(), true );

	// Redirect to the admin dashboard.
	wp_safe_redirect( admin_url() );
	exit;
}

/**
 * Processes the "Switch Back" action.
 *
 * Verifies the nonce, retrieves the original admin ID from the cookie,
 * sets the admin's auth cookie, clears the tracking cookie, and redirects.
 */
function hussainas_qus_process_switch_back() {
	$cookie_name = 'hussainas_qus_original_admin';

	// Check if the cookie is set.
	if ( ! isset( $_COOKIE[ $cookie_name ] ) ) {
		wp_die( __( 'No original user to switch back to. Cookie not found.', 'hussainas' ) );
	}

	// Verify nonce.
	if ( ! wp_verify_nonce( $_GET['_wpnonce'], 'hussainas_qus_switch_back_nonce' ) ) {
		wp_die( __( 'Security check failed. Please try again.', 'hussainas' ) );
	}

	$original_admin_id = absint( $_COOKIE[ $cookie_name ] );

	// Log the original admin back in.
	wp_set_auth_cookie( $original_admin_id );

	// Clear the "original admin" cookie.
	setcookie( $cookie_name, '', time() - 3600, COOKIEPATH, COOKIE_DOMAIN, is_ssl(), true );

	// Redirect to the admin dashboard.
	wp_safe_redirect( admin_url() );
	exit;
}
