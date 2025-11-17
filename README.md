# Quick User Switching For Wordpress

A secure, standalone WordPress module that allows administrators to quickly switch to another user's account for debugging and testing purposes, and then switch back, directly from the admin bar.

## 🌟 Features

* **Admin Bar Integration:** Adds a "Quick Switch" menu to the WordPress admin bar.
* **Secure Switching:** Utilizes WordPress nonces (`_wpnonce`) for all switch actions to prevent CSRF attacks.
* **Admin-Only:** The functionality is only available to users with the `manage_options` capability (Administrators).
* **Easy "Switch Back":** When an admin switches to another user, a secure cookie is set. The admin bar then displays a simple "Switch Back to [Admin Name]" link.
* **Safe User List:** The user list automatically excludes other administrators to prevent accidental privilege escalation or lockouts.

## 🚀 Installation

This module is designed to be included within a WordPress theme.

1.  **Copy the Module:**
    Place the entire `wp-quick-user-switching` directory into your theme's folder. A good location would be inside a `modules` or `inc` directory.
    *Example Structure:*
    ```
    /wp-content/themes/your-theme/
    ├── functions.php
    ├── style.css
    └── wp-quick-user-switching/
        ├── quick-user-switching.php
        └── ...

2.  **Include the Module:**
    Open your theme's `functions.php` file and add the following line to require the main loader file. Ensure you adjust the path based on where you placed the directory.

    ```php
    // Load the Quick User Switching Module
    require_once get_template_directory() . '/wp-quick-user-switching/quick-user-switching.php';
    ```

## Usage

1.  **Log in** as an Administrator.
2.  Hover over the **"Quick Switch"** item in the admin bar.
3.  A dropdown will appear listing the 20 most recently registered non-admin users.
4.  **Click on a user** you wish to log in as. You will be redirected and instantly logged in as that user.
5.  You will now see the site exactly as that user sees it.
6.  To return to your admin account, simply click the **"Switch Back to [Your Admin Name]"** link in the admin bar.

## 🛡️ Security Note

This is a powerful development tool. While built with security in mind (nonce verification, capability checks), it provides privileged access. It is strongly recommended to use this on **development** or **staging** environments. If used in production, ensure it is only accessible to trusted administrators.

