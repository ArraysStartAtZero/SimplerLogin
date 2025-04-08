<?php
/**
 * Plugin Name: Simple Login Redirects
 * Description: Redirects users to different pages based on their role after login
 * Version: 1.0
 * Author: Filip Tomczak
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Redirect users after login based on their role
 */
function simple_login_redirect($redirect_to, $request, $user) {
    // If there's no user, return default redirect
    if (!isset($user->roles) || !is_array($user->roles)) {
        return $redirect_to;
    }
    
    // Define role-based redirects
    $role_redirects = array(
        'administrator' => '/admin-dashboard/',
        'medarbajder' => '/medarbajder-dashboard/',
        'leder' => '/leder-dashboard/',
    );
    
    // Check each role the user has
    foreach ($user->roles as $role) {
        // If role has a custom redirect, use it
        if (isset($role_redirects[$role])) {
            return home_url($role_redirects[$role]);
        }
    }
    
    // If no custom redirect found, return default
    return $redirect_to;
}
add_filter('login_redirect', 'simple_login_redirect', 10, 3);

/**
 * Add a simple admin page to configure redirects
 */
function simple_login_redirects_menu() {
    add_options_page(
        'Login Redirects',
        'Login Redirects',
        'manage_options',
        'simple-login-redirects',
        'simple_login_redirects_page'
    );
}
add_action('admin_menu', 'simple_login_redirects_menu');

/**
 * Admin page content
 */
function simple_login_redirects_page() {
    // Check user capabilities
    if (!current_user_can('manage_options')) {
        return;
    }
    
    // Save settings if form is submitted
    if (isset($_POST['submit'])) {
        // Get the form data
        $administrator_redirect = isset($_POST['administrator_redirect']) ? sanitize_text_field($_POST['administrator_redirect']) : '';
        $medarbajder_redirect = isset($_POST['medarbajder_redirect']) ? sanitize_text_field($_POST['medarbajder_redirect']) : '';
        $leder_redirect = isset($_POST['leder_redirect']) ? sanitize_text_field($_POST['leder_redirect']) : '';
        
        // Save to options
        update_option('simple_login_redirect_administrator', $administrator_redirect);
        update_option('simple_login_redirect_medarbajder', $medarbajder_redirect);
        update_option('simple_login_redirect_leder', $leder_redirect);
        
        echo '<div class="updated"><p>Settings saved.</p></div>';
    }
    
    // Get saved values
    $administrator_redirect = get_option('simple_login_redirect_administrator', '/admin-dashboard/');
    $medarbajder_redirect = get_option('simple_login_redirect_medarbajder', '/medarbajder-dashboard/');
    $leder_redirect = get_option('simple_login_redirect_leder', '/leder-dashboard/');
    
    // Display the form
    ?>
    <div class="wrap">
        <h1>Login Redirect Settings</h1>
        <p>Configure where users are redirected after login based on their role.</p>
        
        <form method="post" action="">
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="administrator_redirect">Administrator Redirect:</label>
                    </th>
                    <td>
                        <input type="text" id="administrator_redirect" name="administrator_redirect" 
                               value="<?php echo esc_attr($administrator_redirect); ?>" class="regular-text">
                        <p class="description">Path to redirect administrators to (e.g., /admin-dashboard/)</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="medarbajder_redirect">Medarbajder Redirect:</label>
                    </th>
                    <td>
                        <input type="text" id="medarbajder_redirect" name="medarbajder_redirect" 
                               value="<?php echo esc_attr($medarbajder_redirect); ?>" class="regular-text">
                        <p class="description">Path to redirect medarbajder to (e.g., /medarbajder-dashboard/)</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="leder_redirect">Leder Redirect:</label>
                    </th>
                    <td>
                        <input type="text" id="leder_redirect" name="leder_redirect" 
                               value="<?php echo esc_attr($leder_redirect); ?>" class="regular-text">
                        <p class="description">Path to redirect leder to (e.g., /leder-dashboard/)</p>
                    </td>
                </tr>
            </table>
            
            <?php submit_button('Save Settings'); ?>
        </form>
    </div>
    <?php
}

/**
 * Override the login redirect function to use saved options
 */
function custom_login_redirect_with_options($redirect_to, $request, $user) {
    // If there's no user, return default redirect
    if (!isset($user->roles) || !is_array($user->roles)) {
        return $redirect_to;
    }
    
    // Check each role the user has
    foreach ($user->roles as $role) {
        // Get the saved redirect for this role
        $saved_redirect = get_option('simple_login_redirect_' . $role, '');
        
        // If a redirect is set, use it
        if (!empty($saved_redirect)) {
            return home_url($saved_redirect);
        }
    }
    
    // If no custom redirect found, return default
    return $redirect_to;
}
add_filter('login_redirect', 'custom_login_redirect_with_options', 20, 3); 