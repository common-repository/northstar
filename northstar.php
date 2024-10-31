<?php
/*
Plugin Name: NorthStar
Description: A plugin to display your "North Star" in the admin bar. What are you working toward?
Version: 1.0
Author: Mark Praschan
Author URI: https://twitter.com/MarkPraschan
License: GPL-2.0-or-later
*/

// Register the plugin settings
function northstar_register_settings() {
    add_settings_section('northstar_settings_section', 'NorthStar Settings', 'northstar_settings_section_callback', 'northstar');
    add_settings_field('northstar_activate', 'Activate NorthStar', 'northstar_activate_callback', 'northstar', 'northstar_settings_section');
    add_settings_field('northstar_message', 'Message', 'northstar_message_callback', 'northstar', 'northstar_settings_section');
    add_settings_field('northstar_text_color', 'Text Color', 'northstar_text_color_callback', 'northstar', 'northstar_settings_section');
    add_settings_field('northstar_bg_color', 'Background Color', 'northstar_bg_color_callback', 'northstar', 'northstar_settings_section');
    register_setting('northstar_settings_group', 'northstar_activate', 'intval');
    register_setting('northstar_settings_group', 'northstar_message', 'sanitize_text_field');
    register_setting('northstar_settings_group', 'northstar_text_color', 'sanitize_hex_color');
    register_setting('northstar_settings_group', 'northstar_bg_color', 'sanitize_hex_color');
}
add_action('admin_init', 'northstar_register_settings');

// Add the settings page to the WP settings menu
function northstar_add_settings_page() {
    add_options_page('NorthStar', 'NorthStar', 'manage_options', 'northstar', 'northstar_settings_page');
}
add_action('admin_menu', 'northstar_add_settings_page');

// Callback functions for the settings fields
function northstar_settings_section_callback() {
    echo '<p>Select your NorthStar settings here. What motivates you? What\'s your mantra?</p>';
}
function northstar_activate_callback() {
    $activate = get_option('northstar_activate', 1);
    echo '<input type="checkbox" name="northstar_activate" value="1" ' . checked(1, $activate, false) . '/>';
}
function northstar_message_callback() {
    $message = get_option('northstar_message', 'Let your NorthStar be your guide!');
    echo '<input type="text" name="northstar_message" value="' . esc_attr($message) . '" style="width: 50ch;"/>';
}
function northstar_text_color_callback() {
    $text_color = get_option('northstar_text_color', '#ffffff');
    echo '<input type="text" name="northstar_text_color" class="color-picker" value="' . esc_attr($text_color) . '"/>';
}
function northstar_bg_color_callback() {
    $bg_color = get_option('northstar_bg_color', '#10a37f');
    echo '<input type="text" name="northstar_bg_color" class="color-picker" value="' . esc_attr($bg_color) . '"/>';
}

// NorthStar admin bar callback
function northstar_admin_bar() {
    $activate = get_option('northstar_activate', 0);
    if ($activate) {
        $message = '⭐ ' . get_option('northstar_message', 'Let your north star guide you!');
        $text_color = get_option('northstar_text_color', '#ffffff');
        $bg_color = get_option('northstar_bg_color', '#10a37f');
        $style = 'color: ' . esc_attr($text_color) . '; background-color: ' . esc_attr($bg_color) . ';font-weight:bold';
        global $wp_admin_bar;
        $wp_admin_bar->add_menu(array(
            'id' => 'northstar',
            'parent' => 'top-secondary',
            'title' => '<a style="' . $style . '" href="' . admin_url('options-general.php?page=northstar') . '">' . esc_html($message) . '</a>',
            'meta' => array(
                'class' => 'northstar-menu-item'
            )
        ));
?>
        <style>
            #wpadminbar .ab-top-secondary .northstar-menu-item:hover {
                background-color: <?php echo esc_attr($bg_color); ?>;
            }
        </style>
    <?php
    }
}
add_action('admin_bar_menu', 'northstar_admin_bar');


// Enqueue the color picker script
function northstar_enqueue_color_picker() {
    wp_enqueue_style('wp-color-picker');
    wp_enqueue_script('wp-color-picker');
    wp_enqueue_script('northstar-color-picker', plugins_url('northstar-color-picker.js', __FILE__), array('wp-color-picker'), false, true);
}
add_action('admin_enqueue_scripts', 'northstar_enqueue_color_picker');

// Settings page output
function northstar_settings_page() {
    ?>
    <div class="wrap">
        <h2>NorthStar Settings</h2>
        <form action="options.php" method="post">
            <?php settings_fields('northstar_settings_group'); ?>
            <?php do_settings_sections('northstar'); ?>
            <?php submit_button(); ?>
        </form>
    </div>
<?php
}

function northstar_add_settings_link($links) {
    $settings_link = '<a href="/wp-admin/options-general.php?page=northstar">' . __('Settings') . '</a>';
    array_push($links, $settings_link);
    return $links;
}
$plugin = plugin_basename(__FILE__);
add_filter("plugin_action_links_$plugin", 'northstar_add_settings_link');
