<?php
/**
 * Admin Bar functionality for Quick User Switching.
 *
 * This file adds the "Quick Switch" menu to the WordPress admin bar,
 * populating it with a list of users or a "Switch Back" link.
 *
 * @package Hussainas_Quick_User_Switching
 */

// Do not allow direct access to this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct access is not allowed.' );
}

/**
 * Adds the "Quick Switch" menu to the admin bar.
 *
 * This function checks if the current user is an admin or is currently
 * switched. It will either display a "Switch Back" link or a list of
 * users to switch to.
 *
 * @param WP_Admin_Bar $wp_admin_bar The WP_Admin_Bar instance.
 */
function hussainas_qus_add_admin_bar_menu( $wp_admin_bar ) {

	$cookie_name = 'hussainas_qus_original_admin';

	// Case 1: The user is currently switched (the cookie is set).
	if ( isset( $_COOKIE[ $cookie_name ] ) ) {
		$original_admin_id = absint( $_COOKIE[ $cookie_name ] );
		$admin_data        = get_userdata( $original_admin_id );

		if ( $admin_data ) {
			// Generate a "Switch Back" link.
			$switch_back_url = add_query_arg(
				array(
					'hussainas_qus_switch_back' => 1,
					'_wpnonce'                   => wp_create_nonce( 'hussainas_qus_switch_back_nonce' ),
				),
				home_url()
			);

			$wp_admin_bar->add_node(
				array(
					'id'    => 'hussainas-quick-switch-back',
					'title' => sprintf(
						/* translators: %s: Admin's display name */
						__( 'Switch Back to %s', 'hussainas' ),
						esc_html( $admin_data->display_name )
					),
					'href'  => $switch_back_url,
					'meta'  => array(
						'class' => 'hussainas-qus-switch-back',
					),
				)
			);
		}
		// This user is switched, so they shouldn't see the user list.
		return;
	}

	// Case 2: The user is an admin (cookie is not set, check capabilities).
	if ( ! current_user_can( 'manage_options' ) ) {
		return; // Not an admin, do nothing.
	}

	// Add the main "Quick Switch" parent menu.
	$wp_admin_bar->add_node(
		array(
			'id'    => 'hussainas-quick-switch',
			'title' => __( 'Quick Switch', 'hussainas' ),
			'href'  => '#',
		)
	);

	// Get recent users to populate the list.
	// We exclude administrators for security.
	$users = get_users(
		array(
			'number'  => 20, // Fetch 20 recent users.
			'orderby' => 'user_registered',
			'order'   => 'DESC',
			'role__not_in' => array( 'administrator' ),
		)
	);

	if ( empty( $users ) ) {
		$wp_admin_bar->add_node(
			array(
				'id'     => 'hussainas-qus-no-users',
				'title'  => __( 'No recent non-admin users found.', 'hussainas' ),
				'parent' => 'hussainas-quick-switch',
			)
		);
		return;
	}

	// Populate the sub-menu with users.
	foreach ( $users as $user ) {
		// Generate a secure, nonced URL for switching.
		$switch_to_url = add_query_arg(
			array(
				'hussainas_qus_switch_to' => $user->ID,
				'_wpnonce'                 => wp_create_nonce( 'hussainas_qus_switch_to_nonce_' . $user->ID ),
			),
			home_url()
		);

		$wp_admin_bar->add_node(
			array(
				'id'     => 'hussainas-qus-user-' . $user->ID,
				'parent' => 'hussainas-quick-switch',
				'title'  => esc_html( $user->display_name ) . ' (' . esc_html( $user->user_email ) . ')',
				'href'   => $switch_to_url,
			)
		);
	}
}
