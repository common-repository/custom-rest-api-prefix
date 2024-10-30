<?php
/**
 * Plugin Name:     Custom REST API Prefix
 * Plugin URI:      https://github.com/alvindcaesar/custom-rest-api-prefix
 * Description:     A simple plugin to customize the default WordPress REST API prefix (wp-json)
 * Author:          Alvind Caesar
 * Author URI:      https://alvindcaesar.com
 * Text Domain:     custom-rest-api-prefix
 * Domain Path:     /languages
 * Version:         1.0.1
 *
 * @package         Custom_Rest_Api_Prefix
 */

if( ! defined('ABSPATH')) die;

/* ------------------------------------------------------------------------ *
 * Setting Registration
 * ------------------------------------------------------------------------ */
 
/**
 * Initializes the Custom REST API Prefix Setting page by registering the Sections,
 * Fields, and Settings.
 *
 * This function is registered with the 'admin_init' hook.
 */
function cra_prefix_initialize_options() {
 
    add_settings_section(
        'cra_prefix_settings_section',       // ID used to identify this section and with which to register options
        'Custom REST API Prefix Setting',    // Title to be displayed on the administration page
        'cra_prefix_section_callback',       // Callback used to render the description of the section
        'permalink'                          // Page on which to add this section of options
    );

    add_settings_field( 
      'cra_prefix_setting',               // ID used to identify the field throughout the theme
      'Custom REST API Prefix',           // The label to the left of the option interface element
      'cra_prefix_settings_callback',     // The name of the function responsible for rendering the option interface
      'permalink',                        // The page on which this option will be displayed
      'cra_prefix_settings_section',      // The name of the section to which this field belongs
      array(                              // The array of arguments to pass to the callback. In this case, just a description.
          '<i>If you set you custom prefix to "api", your REST API URL endpoint will be <code>' . site_url(). '/api</code></i>'
      )
  );

  register_setting(
    'permalink',
    'cra_prefix_setting'
  );

  if( isset($_POST['permalink_structure']) && isset( $_POST['cra_prefix_setting'] ) ){
    $cra_prefix = wp_unslash( sanitize_text_field($_POST['cra_prefix_setting']) );
    update_option( 'cra_prefix_setting',  $cra_prefix );
  } 
} // end cra_prefix_initialize_options

add_action('admin_init', 'cra_prefix_initialize_options');


/* ------------------------------------------------------------------------ *
 * Section Callbacks
 * ------------------------------------------------------------------------ */
 
/**
 * This function provides a simple description for the Permalink Settings page.
 *
 * It is called from the 'cra_prefix_initialize_options' function by being passed as a parameter
 * in the add_settings_section function.
 */

function cra_prefix_section_callback() {
    echo '<p>Enter your custom REST API prefix to replace the default <code>wp-json</code> prefix.</p>';
} // end cra_prefix_section_callback

/* ------------------------------------------------------------------------ *
 * Field Callbacks
 * ------------------------------------------------------------------------ */
 
/**
 * This function renders the interface elements for entering the custom REST API prefix name.
 * 
 * It accepts an array of arguments and expects the first element in the array to be the description
 * to be displayed under the text field.
 */

function cra_prefix_settings_callback($args) {
  $html  = '<code>'.site_url().'/</code> ';
  $html .= '<input type="text" autocomplete="off" class="regular-text" id="cra_prefix_setting" name="cra_prefix_setting" placeholder="Eg. api" value="'.get_option('cra_prefix_setting').'"/>';
  $html .= '<p class="description"><label for="cra_prefix_setting"> '  . $args[0] . '</p>';

  $allowed_tags = array(
    'code'  => array(),
    'input' => array(
      'autocomplete' => array(),
      'class'        => array(),
      'id'           => array(),
      'name'         => array(),
      'placeholder'  => array(),
      'value'        => array()
    ),
    'p'     => array(
      'class' => array()
    ),
    'label'  => array()
  );
   
  echo wp_kses($html, $allowed_tags);
} // end cra_prefix_settings_callback


add_filter('rest_url_prefix', 'cra_prefix_custom_rest');

function cra_prefix_custom_rest() {
  $custom_prefix = !empty(get_option('cra_prefix_setting')) ? get_option('cra_prefix_setting') : 'wp-json';
  return $custom_prefix;
}