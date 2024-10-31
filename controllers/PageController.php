<?php

namespace OxyplugProsCons\Controllers;

use WP_Rewrite;

class PageController extends BaseController
{
  private static $proscons_already_added = false;

  /**
   * @param $class_priority
   * @return void
   */
  public static function run($class_priority = 10)
  {
    // Add ProsCons To The Content
    add_filter('the_content', array(__CLASS__, 'add_proscons_to_the_content'), 100);

    // Add Structured Data To The Page
    add_filter('wp_footer', array(__CLASS__, 'add_structured_data_to_the_page'), 100);
  }

  /**
   * @param $content
   * @return mixed|string
   */
  public static function add_proscons_to_the_content($content)
  {
    if (!static::is_in_dashboard() && !static::is_rest() && !static::$proscons_already_added) {
      static::$proscons_already_added = true;
      $post_id = get_the_ID();

      // Pros / Cons
      $cons_meta = get_post_meta($post_id, '_oxyplug_proscons_con');
      $pros_meta = get_post_meta($post_id, '_oxyplug_proscons_pro');

      if (!empty($cons_meta) || !empty($pros_meta)) {
        // Title
        $title = get_post_meta($post_id, '_oxyplug_proscons_title', true);
        $title = $title ?: '';

        $cons = '';
        if (empty($cons_meta)) {
          $cons = '<li>' . esc_html__('No cons.') . '</li>';
        } else {
          foreach ($cons_meta as $con) {
            $cons .= '<li>' . esc_html($con) . '</li>';
          }
        }

        $pros = '';
        if (empty($pros_meta)) {
          $pros = '<li>' . esc_html__('No pros.') . '</li>';
        } else {
          foreach ($pros_meta as $pro) {
            $pros .= '<li>' . esc_html($pro) . '</li>';
          }
        }

        $content .= static::fill_in_template($title, $pros, $cons);
      }
    }

    return $content;
  }

  /**
   * @param $title
   * @param $pros
   * @param $cons
   * @return string|void
   */
  private static function fill_in_template($title, $pros, $cons)
  {
    $template_id = static::oxyplug_proscons_get_option('_oxyplug_proscons_template', '1');

    $max_width = static::oxyplug_proscons_get_option('_oxyplug_proscons_max_width', '700');
    $wrap_style = esc_attr('max-width: ' . $max_width . 'px');

    $title_color = static::oxyplug_proscons_get_option('_oxyplug_proscons_title_color', '#000000');
    $title_background_color = static::oxyplug_proscons_get_option('_oxyplug_proscons_title_background_color', '#ffffff');
    $title_style = esc_attr('color: ' . $title_color . '; background-color: ' . $title_background_color);

    $title_tag = static::oxyplug_proscons_get_option('_oxyplug_proscons_title_tag', 'p');
    $title_formatted = '<' . $title_tag . ' id="oxyplug-proscons-generated-list-title" style="' . $title_style . '">' . esc_html($title) . '</' . $title_tag . '>';
    $title_section = '';

    switch ($template_id) {
      case '1':
        if (static::oxyplug_proscons_get_option('_oxyplug_proscons_show_title') == 'yes') {
          $title_section = $title_formatted . '<div id="oxyplug-proscons-generated-list-separator"></div>';
        }

        return
          '<div style="clear:both"></div>
           <div id="oxyplug-proscons-generated-list-wrap" style="' . $wrap_style . '">
            ' . $title_section . '
            <div id="oxyplug-proscons-generated-list">
          
              <div id="oxyplug-proscons-pros-list-wrap">
                <p id="oxyplug-proscons-pros-list-title">' . esc_html__('Pros', 'oxyplug-proscons') . '</p>
                <ul id="oxyplug-proscons-pros-list">' . $pros . '</ul>
              </div>
          
              <div id="oxyplug-proscons-cons-list-wrap">
                <p id="oxyplug-proscons-cons-list-title">' . esc_html__('Cons', 'oxyplug-proscons') . '</p>
                <ul id="oxyplug-proscons-cons-list">' . $cons . '</ul>
              </div>
          
            </div>
          </div>';

      case '2':
      case '3':
        if (static::oxyplug_proscons_get_option('_oxyplug_proscons_show_title') == 'yes') {
          $title_section = $title_formatted;
        }

        return
          '<div style="clear:both"></div>
           <div id="oxyplug-proscons-generated-list-wrap" style="' . $wrap_style . '">
            ' . $title_section . '
            <div id="oxyplug-proscons-generated-list">
          
              <div id="oxyplug-proscons-pros-list-wrap">
                <p id="oxyplug-proscons-pros-list-title">' . esc_html__('Pros', 'oxyplug-proscons') . '</p>
                <ul id="oxyplug-proscons-pros-list">' . $pros . '</ul>
              </div>
          
              <div id="oxyplug-proscons-cons-list-wrap">
                <p id="oxyplug-proscons-cons-list-title">' . esc_html__('Cons', 'oxyplug-proscons') . '</p>
                <ul id="oxyplug-proscons-cons-list">' . $cons . '</ul>
              </div>
          
            </div>
          </div>';
      case '4':
        if (static::oxyplug_proscons_get_option('_oxyplug_proscons_show_title') == 'yes') {
          $title_section = $title_formatted;
        }

        return
          '<div style="clear:both"></div>
           <div id="oxyplug-proscons-generated-list-wrap" style="' . $wrap_style . '">
            ' . $title_section . '
            <div id="oxyplug-proscons-generated-list">
          
              <div id="oxyplug-proscons-pros-list-wrap">
                <img src="' . plugins_url('assets/images/oxyplug-pros-smiley.svg', OXYPLUG_PROSCONS_FILE) . '" alt="Pros Smiley">
                <p id="oxyplug-proscons-pros-list-title">' . esc_html__('Pros', 'oxyplug-proscons') . '</p>
                <ul id="oxyplug-proscons-pros-list">' . $pros . '</ul>
              </div>
          
              <div id="oxyplug-proscons-cons-list-wrap">
                <img src="' . plugins_url('assets/images/oxyplug-cons-smiley.svg', OXYPLUG_PROSCONS_FILE) . '" alt="Cons Smiley">
                <p id="oxyplug-proscons-cons-list-title">' . esc_html__('Cons', 'oxyplug-proscons') . '</p>
                <ul id="oxyplug-proscons-cons-list">' . $cons . '</ul>
              </div>
          
            </div>
          </div>';
    }
  }

  /**
   * @return void
   */
  public static function add_structured_data_to_the_page()
  {
    if (!static::is_in_dashboard() && !static::is_rest()) {
      $post_id = get_the_ID();

      $structured_data = get_post_meta($post_id, '_oxyplug_proscons_structured_data', true);
      if (!empty($structured_data)) {
        echo '<script type="application/ld+json">' . wp_json_encode($structured_data, JSON_UNESCAPED_UNICODE) . '</script>';
      }
    }
  }

  /**
   * @return bool
   */
  protected static function is_in_dashboard(): bool
  {
    $current_url = sanitize_text_field($_SERVER['REQUEST_SCHEME']) . '://' . sanitize_text_field($_SERVER['HTTP_HOST']) . sanitize_text_field($_SERVER['REQUEST_URI']);
    $current_url = esc_url($current_url);
    return mb_strpos($current_url, get_admin_url()) === 0;
  }

  /**
   * @return bool
   */
  protected static function is_rest()
  {
    if (
      (defined('REST_REQUEST') && REST_REQUEST) ||
      (isset($_GET['rest_route']) && strpos($_GET['rest_route'], '/') === 0)) {
      return true;
    }

    global $wp_rewrite;
    if ($wp_rewrite === null) $wp_rewrite = new WP_Rewrite();

    $rest_url = wp_parse_url(trailingslashit(rest_url()));
    $current_url = wp_parse_url(add_query_arg(array()));

    return strpos(isset($current_url['path']) ? $current_url['path'] : '/', $rest_url['path']) === 0;
  }
}