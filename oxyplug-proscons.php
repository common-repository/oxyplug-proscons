<?php
/**
 * Plugin Name: OxyPlug - Pros & Cons
 * Plugin URI: https://www.oxyplug.com/products/oxyplug-pros-and-cons/
 * Description: Provide products' advantages and disadvantages in the simplest way possible. Free pros and cons plugin with different templates and the ability to generate pros and cons structured data.
 * Version: 1.1.3
 * Requires at least: 5.5
 * Requires PHP: 7.4
 * Tested up to: 6.7
 * Author: OxyPlug
 * Author URI: https://www.oxyplug.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: oxyplug-proscons
 * Domain Path: /lang
 *
 * Copyright 2024 OxyPlug
 */

namespace OxyplugProsCons;

if (!defined('ABSPATH')) {
  exit;
}

/**
 * Class OxyplugProsCons
 */
class OxyplugProsCons
{
  /**
   * @return void
   */
  public static function init()
  {
    define('OXYPLUG_PROSCONS_PATH', dirname(__FILE__));
    define('OXYPLUG_PROSCONS_FILE', __FILE__);
    define('OXYPLUG_PROSCONS_VERSION', '1.1.3');

    $classes = array(
      'BaseController' => 10, 'SettingsController' => 11,
      'ProsConsController' => 12, 'PageController' => 13
    );
    foreach ($classes as $class => $priority) {
      require(OXYPLUG_PROSCONS_PATH . '/controllers/' . $class . '.php');
      call_user_func('OxyplugProsCons\\Controllers\\' . $class . '::run', $priority);
    }
  }
}

OxyplugProsCons::init();
