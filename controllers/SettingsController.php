<?php

namespace OxyplugProsCons\Controllers;

class SettingsController extends BaseController
{
  /**
   * @param $class_priority
   * @return void
   */
  public static function run($class_priority = 10)
  {
    // Menu
    add_action('admin_menu', array(__CLASS__, 'add_menu'));

    // Settings
    add_action('wp_ajax_oxyplug_proscons_save_settings', array(__CLASS__, 'oxyplug_proscons_save_settings'));
    add_filter('plugin_action_links', array(__CLASS__, 'add_settings'), 10, 3);
  }

  /**
   * @return void
   */
  public static function add_menu()
  {
    add_menu_page(
      'Oxy Pros & Cons',
      'Oxy Pros & Cons',
      'manage_options',
      'oxyplug-proscons-settings',
      array(__CLASS__, 'oxyplug_proscons_settings'),
      'data:image/svg+xml;base64,' . base64_encode(file_get_contents(plugins_url('assets/images/oxyplug-proscons-icon.svg', OXYPLUG_PROSCONS_FILE))),
      100
    );
  }

  /**
   * @return string[]
   */
  private static function title_tags()
  {
    return array('p', 'h2', 'h3', 'h4', 'div', 'span');
  }

  /**
   * @return void
   */
  public static function oxyplug_proscons_settings()
  {
    $user = get_user_by('id', get_current_user_id());
    $author_type = get_user_meta($user->ID, '_oxyplug_proscons_author_type', true);
    $author_name = get_user_meta($user->ID, '_oxyplug_proscons_author_name', true);
    if (empty($author_name)) {
      $author_name = $user->display_name;
    }
    $show_title = static::oxyplug_proscons_get_option('_oxyplug_proscons_show_title', 'yes');
    $title_tag = static::oxyplug_proscons_get_option('_oxyplug_proscons_title_tag', 'p');
    $max_width = static::oxyplug_proscons_get_option('_oxyplug_proscons_max_width', '700');
    $template_id = static::oxyplug_proscons_get_option('_oxyplug_proscons_template', '1');
    $title_color = static::oxyplug_proscons_get_option('_oxyplug_proscons_title_color', '#000000');
    $title_background_color = static::oxyplug_proscons_get_option('_oxyplug_proscons_title_background_color', '#ffffff');

    ?>
    <form id="oxyplug-proscons-settings" action="" method="post" autocomplete="off">
      <?php wp_nonce_field('oxyplug_proscons_save_settings', 'oxyplug_proscons_settings_nonce', false) ?>
      <input type="hidden" name="action" value="oxyplug_proscons_save_settings">
      <h1 class="oxyplug-proscons-head-title"><?php esc_html_e('Oxyplug Pros & Cons | Settings', 'oxyplug-proscons') ?></h1>

      <div class="oxyplug-proscons-each-section">
        <div>
          <strong class="oxyplug-proscons-h2"><?php esc_html_e('Author Type', 'oxyplug-proscons') ?></strong>
          <div class="oxyplug-proscons-d-768-inline-block">
            <select name="oxyplug_proscons[author][type]" id="oxyplug-proscons-author-type">
              <option value="">
                <?php esc_html_e('-- Choose --', 'oxyplug-proscons') ?>
              </option>
              <option value="Organization" <?php selected('Organization', $author_type) ?>>
                <?php esc_html_e('Organization', 'oxyplug-proscons') ?>
              </option>
              <option value="Person" <?php selected('Person', $author_type) ?>>
                <?php esc_html_e('Person', 'oxyplug-proscons') ?>
              </option>
            </select>
          </div>
        </div>

        <div>
          <strong class="oxyplug-proscons-h2"><?php esc_html_e('Author Name', 'oxyplug-proscons') ?></strong>
          <div class="oxyplug-proscons-d-768-inline-block">
            <input type="text" name="oxyplug_proscons[author][name]" value="<?php echo esc_attr($author_name); ?>">
          </div>
        </div>

        <div>
          <strong class="oxyplug-proscons-h2"><?php esc_html_e('Show Title', 'oxyplug-proscons') ?></strong>
          <input type="checkbox"
                 name="oxyplug_proscons[show_title]" <?php echo esc_html($show_title == 'yes' ? ' checked="checked" ' : '') ?>>
        </div>

        <div>
          <strong class="oxyplug-proscons-h2"><?php esc_html_e('Title Tag', 'oxyplug-proscons') ?></strong>
          <select name="oxyplug_proscons[title_tag]">
            <?php foreach (static::title_tags() as $tag): ?>
              <option value="<?php echo esc_attr($tag); ?>" <?php selected($tag, $title_tag) ?>>
                <?php echo esc_html(strtoupper($tag)); ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div>
          <strong class="oxyplug-proscons-h2"><?php esc_html_e('Title Color', 'oxyplug-proscons') ?></strong>
          <input type="color" name="oxyplug_proscons[title_color]" value="<?php echo esc_attr($title_color); ?>">
        </div>

        <div>
          <strong class="oxyplug-proscons-h2"><?php esc_html_e('Title Background Color', 'oxyplug-proscons') ?></strong>
          <input type="color" name="oxyplug_proscons[title_background_color]"
                 value="<?php echo esc_attr($title_background_color); ?>">
        </div>

        <div>
          <strong class="oxyplug-proscons-h2"><?php esc_html_e('Max Width', 'oxyplug-proscons') ?></strong>
          <input name="oxyplug_proscons[max_width]"
                 type="number"
                 min="1"
                 max="1500"
                 value="<?php echo esc_attr($max_width); ?>">
        </div>

        <div>
          <strong
              class="oxyplug-proscons-h2 oxyplug-proscons-template-head"><?php esc_html_e('Template', 'oxyplug-proscons') ?></strong>
          <br>
          <?php for ($t = 1; $t <= 4; $t++): ?>
            <div class="oxyplug-proscons-d-768-inline-block oxyplug-proscons-template-wrap">
              <img
                  src="<?php echo esc_attr(plugins_url('assets/images/site-style-' . $t . '.png', OXYPLUG_PROSCONS_FILE)) ?>"
                  alt="site style <?php echo esc_attr($t); ?>">
              <div class="oxyplug-proscons-template-radio">
                <input type="radio"
                  <?php if ($template_id == $t) echo esc_html('checked="checked"') ?>
                       name="oxyplug_proscons[template]"
                       value="<?php echo esc_attr($t); ?>">
              </div>
            </div>
          <?php endfor; ?>
        </div>

        <button id="oxyplug-proscons-settings-save" class="button button-primary oxyplug-proscons-submit" type="button">
          <?php esc_html_e('Save', 'oxyplug-proscons'); ?>
        </button>

      </div>

    </form>
  <?php }

  /**
   * @return void
   */
  public static function oxyplug_proscons_save_settings()
  {
    if (isset($_POST['oxyplug_proscons_settings_nonce']) && isset($_POST['action'])) {
      $nonce = sanitize_text_field(wp_unslash($_POST['oxyplug_proscons_settings_nonce']));
      if (wp_verify_nonce($nonce, 'oxyplug_proscons_save_settings')) {

        // Author Type
        if (empty($_POST['oxyplug_proscons']['author']['type'])) {
          wp_send_json_error(array('message' => esc_html__('The author type is required.', 'oxyplug-proscons')));
        }
        if (!in_array($_POST['oxyplug_proscons']['author']['type'], array('Person', 'Organization'))) {
          wp_send_json_error(array('message' => esc_html__('The author type is invalid.', 'oxyplug-proscons')));
        }
        $author_type = sanitize_text_field($_POST['oxyplug_proscons']['author']['type']);

        // Author Name
        if (empty($_POST['oxyplug_proscons']['author']['name'])) {
          wp_send_json_error(array('message' => esc_html__('The author name is required.', 'oxyplug-proscons')));
        }
        $author_name = sanitize_text_field($_POST['oxyplug_proscons']['author']['name']);

        // Title Tag
        if (
          empty($_POST['oxyplug_proscons']['title_tag']) ||
          !in_array($_POST['oxyplug_proscons']['title_tag'], static::title_tags())
        ) {
          wp_send_json_error(array('message' => esc_html__('The title tag is invalid.', 'oxyplug-proscons')));
        }
        $title_tag = sanitize_text_field($_POST['oxyplug_proscons']['title_tag']);

        // Title Color
        if (empty($_POST['oxyplug_proscons']['title_color'])) {
          wp_send_json_error(array('message' => esc_html__('The title color is invalid.', 'oxyplug-proscons')));
        } else {
          $title_color = sanitize_text_field($_POST['oxyplug_proscons']['title_color']);
          $valid = is_string($title_color) && preg_match('/^#([a-fA-F\d]{6})$/', $title_color);
          if (!$valid) {
            wp_send_json_error(array('message' => esc_html__('The title color is invalid.', 'oxyplug-proscons')));
          }
        }
        $title_color = sanitize_hex_color($title_color);

        // Title Background Color
        if (empty($_POST['oxyplug_proscons']['title_background_color'])) {
          wp_send_json_error(array('message' => esc_html__('The title background color is invalid.', 'oxyplug-proscons')));
        } else {
          $title_background_color = sanitize_hex_color($_POST['oxyplug_proscons']['title_background_color']);
          $valid = preg_match('/^#([a-fA-F\d]{6})$/', $title_background_color);
          if (!$valid) {
            wp_send_json_error(array('message' => esc_html__('The title background color is invalid.', 'oxyplug-proscons')));
          }
        }

        // Show Title
        $show_title = isset($_POST['oxyplug_proscons']['show_title']) ? 'yes' : 'no';

        // Max Width
        if (empty($_POST['oxyplug_proscons']['max_width'])) {
          wp_send_json_error(array('message' => esc_html__('The max width is invalid.', 'oxyplug-proscons')));
        }
        $max_width = (int)($_POST['oxyplug_proscons']['max_width']);
        if ($max_width < 1 || $max_width > 1500) {
          wp_send_json_error(array('message' => esc_html__('The max width is invalid.', 'oxyplug-proscons')));
        }
        $max_width = sanitize_text_field($_POST['oxyplug_proscons']['max_width']);

        // Template
        if (empty($_POST['oxyplug_proscons']['template'])) {
          wp_send_json_error(array('message' => esc_html__('The template is required.', 'oxyplug-proscons')));
        }
        if (!in_array($_POST['oxyplug_proscons']['template'], array(1, 2, 3, 4))) {
          wp_send_json_error(array('message' => esc_html__('The template is invalid.', 'oxyplug-proscons')));
        }
        $template = sanitize_text_field($_POST['oxyplug_proscons']['template']);

        $user_id = get_current_user_id();
        update_user_meta($user_id, '_oxyplug_proscons_author_type', $author_type);
        update_user_meta($user_id, '_oxyplug_proscons_author_name', $author_name);
        static::oxyplug_proscons_update_option('_oxyplug_proscons_title_tag', $title_tag);
        static::oxyplug_proscons_update_option('_oxyplug_proscons_title_color', $title_color);
        static::oxyplug_proscons_update_option('_oxyplug_proscons_title_background_color', $title_background_color);
        static::oxyplug_proscons_update_option('_oxyplug_proscons_show_title', $show_title);
        static::oxyplug_proscons_update_option('_oxyplug_proscons_max_width', $max_width);
        static::oxyplug_proscons_update_option('_oxyplug_proscons_template', $template);

        wp_send_json_success(array('message' => esc_html__('Successfully saved.', 'oxyplug-proscons')));
      }
    }
  }

  /**
   * @param $actions
   * @param $plugin_file
   * @param $plugin_data
   * @return mixed
   */
  public static function add_settings($actions, $plugin_file, $plugin_data)
  {
    if (isset($plugin_data['slug']) && $plugin_data['slug'] == 'oxyplug-proscons') {
      $href = admin_url('admin.php?page=oxyplug-proscons-settings');

      $actions['Settings'] = '<a href="' . $href . '">' . __('Settings', 'oxyplug-proscons') . '</a>';
    }

    return $actions;
  }
}