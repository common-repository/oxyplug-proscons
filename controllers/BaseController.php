<?php

namespace OxyplugProsCons\Controllers;

use OxyplugProsCons\OxyplugProsCons;

class BaseController extends OxyplugProsCons
{
  /**
   * @param $class_priority
   * @return void
   */
  public static function run($class_priority = 10)
  {
    // Lang
    add_action('plugins_loaded', array(__CLASS__, 'load_plugin_textdomain'), $class_priority);

    // Admin JS & CSS
    add_action('admin_enqueue_scripts', array(__CLASS__, 'add_admin_assets'), $class_priority);

    // Site JS & CSS
    add_action('wp_enqueue_scripts', array(__CLASS__, 'add_site_assets'), $class_priority);
  }

  /**
   * @param $option_name
   * @param $default
   * @return false|mixed|void
   */
  protected static function oxyplug_proscons_get_option($option_name, $default = false)
  {
    if (is_multisite()) {
      // Maybe later!
      // $network_id = is_plugin_active_for_network(OXYPLUG_PROSCONS_PLUGIN_PATH) ? get_main_network_id() : get_current_blog_id();
      $network_id = get_current_blog_id();

      return get_network_option($network_id, $option_name, $default);
    }

    return get_option($option_name, $default);
  }

  /**
   * @param $option_name
   * @param $option_value
   * @return void
   */
  protected static function oxyplug_proscons_update_option($option_name, $option_value)
  {
    if (is_multisite()) {
      update_network_option(get_current_blog_id(), $option_name, $option_value);
    } else {
      update_option($option_name, $option_value);
    }
  }

  /**
   * @return void
   */
  public static function load_plugin_textdomain()
  {
    load_plugin_textdomain(
      'oxyplug-proscons',
      false,
      basename(dirname(OXYPLUG_PROSCONS_FILE)) . '/lang/'
    );
  }

  /**
   * @return void
   */
  protected static function add_trans()
  {
    wp_localize_script(
      'oxyplug-proscons-admin-script',
      'oxyplug_proscons_trans',
      static::get_trans()
    );
  }

  /**
   * @return void
   */
  public static function add_admin_assets()
  {
    // Admin JS Scripts
    wp_register_script(
      'oxyplug-proscons-admin-script',
      plugins_url('assets/js/admin-script.js', OXYPLUG_PROSCONS_FILE),
      array('jquery'),
      OXYPLUG_PROSCONS_VERSION
    );
    wp_enqueue_script('oxyplug-proscons-admin-script');

    // Admin CSS Styles
    wp_register_style(
      'oxyplug-proscons-admin-style',
      plugins_url('assets/css/admin-style.css', OXYPLUG_PROSCONS_FILE),
      array(),
      OXYPLUG_PROSCONS_VERSION
    );
    wp_enqueue_style('oxyplug-proscons-admin-style');

    // Add Trans
    static::add_trans();
  }

  /**
   * @return void
   */
  public static function add_site_assets()
  {
    // Site CSS Styles
    $template_id = static::oxyplug_proscons_get_option('_oxyplug_proscons_template', '1');
    wp_register_style(
      'oxyplug-proscons-site-style-' . $template_id,
      plugins_url('assets/css/site-style-' . $template_id . '.css', OXYPLUG_PROSCONS_FILE),
      array(),
      OXYPLUG_PROSCONS_VERSION
    );
    wp_enqueue_style('oxyplug-proscons-site-style-' . $template_id);
  }

  /**
   * @return array
   */
  private static function get_trans()
  {
    return array(
      'enter_positive_point' => esc_html__('Enter a positive point...', 'oxyplug-proscons'),
      'enter_negative_point' => esc_html__('Enter a negative point...', 'oxyplug-proscons'),
      'are_you_sure' => esc_html__('Are you sure?', 'oxyplug-proscons'),
      'cons_pros_of_product' => esc_html__('The pros & cons of this product', 'oxyplug-proscons'),
      'pros' => esc_html__('Pros', 'oxyplug-proscons'),
      'cons' => esc_html__('Cons', 'oxyplug-proscons'),
      'successfully_generated' => esc_html__('The pros and cons generated successfully.', 'oxyplug-proscons'),
      'fill_in_inputs' => esc_html__('Please fill in cons and/or pros.', 'oxyplug-proscons'),
      'cons_empty_title' => esc_html__('No cons.', 'oxyplug-proscons'),
      'pros_empty_title' => esc_html__('No pros.', 'oxyplug-proscons'),
    );
  }
}
