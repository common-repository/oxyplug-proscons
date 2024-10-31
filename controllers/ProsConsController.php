<?php

namespace OxyplugProsCons\Controllers;

class ProsConsController extends BaseController
{
  protected static $oxyplug_site = 'https://www.oxyplug.com';
  protected static $documentation_url;

  /**
   * @param $class_priority
   * @return void
   */
  public static function run($class_priority = 10)
  {
    static::$documentation_url = static::$oxyplug_site . '/docs/oxy-proscons/';

    // Create Metabox
    add_action('add_meta_boxes', array(__CLASS__, 'create_metabox'));

    // Save ProsCons Data
    add_action('wp_ajax_oxyplug_proscons_save', array(__CLASS__, 'oxyplug_proscons_save'));

    // Add content when empty
    add_action('post_updated', array(__CLASS__, 'unempty'), 10, 2);
  }

  /**
   * @return void
   */
  public static function create_metabox()
  {
    add_meta_box(
      'oxyplug_proscons_metabox',
      esc_html__('Oxyplug Pros & Cons', 'oxyplug-proscons'),
      array(__CLASS__, 'render_metabox'),
      'product',
      'normal', // normal: main column | side: sidebar
      'high'
    );
  }

  /**
   * @param $post
   * @return void
   */
  public static function render_metabox($post)
  {
    $title = get_post_meta($post->ID, '_oxyplug_proscons_title', true);
    $title = $title ?: '';

    $product_name = get_post_meta($post->ID, '_oxyplug_proscons_product_name', true);
    $product_name = $product_name ?: $post->post_title;

    $user = get_user_by('id', get_current_user_id());
    $author_type = get_user_meta($user->ID, '_oxyplug_proscons_author_type', true);
    $author_name = get_user_meta($user->ID, '_oxyplug_proscons_author_name', true);
    if (empty($author_name)) {
      $author_name = $user->display_name;
    }

    $cons = get_post_meta($post->ID, '_oxyplug_proscons_con');
    $pros = get_post_meta($post->ID, '_oxyplug_proscons_pro');

    ?>
    <div id="oxyplug-proscons">
      <input type="hidden" name="post_id" value="<?php echo esc_attr($post->ID) ?>">
      <?php wp_nonce_field('oxyplug_proscons_save', 'oxyplug_proscons_save_nonce', false); ?>

      <p>
        <span class="oxyplug-proscons-asterisk">*</span>
        <?php esc_html_e('Required fields', 'oxyplug-proscons') ?>
      </p>

      <div class="oxyplug-proscons-each-row">

        <div>
          <h3><?php esc_html_e('Title', 'oxyplug-proscons') ?></h3>
          <input id="oxyplug-proscons-title"
                 type="text"
                 name="oxyplug_proscons[title]"
                 value="<?php echo esc_attr($title); ?>"
                 placeholder="<?php esc_attr_e('The pros & cons of this product', 'oxyplug-proscons'); ?>">
        </div>

        <div>
          <h3>
            <span class="oxyplug-proscons-asterisk">*</span>
            <?php esc_html_e('Product Name', 'oxyplug-proscons') ?>
          </h3>
          <input id="oxyplug-proscons-product-name"
                 type="text"
                 name="oxyplug_proscons[product_name]"
                 value="<?php echo esc_attr($product_name); ?>">
        </div>

      </div>

      <div class="oxyplug-proscons-each-row">

        <div>
          <h3>
            <span class="oxyplug-proscons-asterisk">*</span>
            <?php esc_html_e('Author Type', 'oxyplug-proscons') ?>
            <?php if ($author_type): ?>
              <a class="oxyplug-proscons-change-default"
                 href="<?php echo esc_attr(admin_url('admin.php?page=oxyplug-proscons-settings')); ?>">
                <?php esc_html_e('Change Default', 'oxyplug-proscons') ?>
              </a>
            <?php endif; ?>
          </h3>
          <?php if ($author_type): ?>
            <input type="text"
                   name="oxyplug_proscons[author][type]"
                   readonly="readonly"
                   autocomplete="off"
                   value="<?php echo esc_attr($author_type); ?>">
          <?php else: ?>
            <select name="oxyplug_proscons[author][type]" id="oxyplug-proscons-author-type" autocomplete="off">
              <option value=""><?php esc_html_e('-- Choose --', 'oxyplug-proscons') ?></option>
              <option value="Organization"><?php esc_html_e('Organization', 'oxyplug-proscons') ?></option>
              <option value="Person"><?php esc_html_e('Person', 'oxyplug-proscons') ?></option>
            </select>
          <?php endif; ?>
        </div>

        <div>
          <h3>
            <span class="oxyplug-proscons-asterisk">*</span>
            <?php esc_html_e('Author Name', 'oxyplug-proscons') ?>
            <a class="oxyplug-proscons-change-default"
               href="<?php echo esc_attr(admin_url('admin.php?page=oxyplug-proscons-settings')); ?>">
              <?php esc_html_e('Change Default', 'oxyplug-proscons') ?>
            </a>
          </h3>
          <input id="oxyplug-proscons-author-name"
                 type="text"
                 autocomplete="off"
                 readonly="readonly"
                 name="oxyplug_proscons[author][name]"
                 value="<?php echo esc_attr($author_name); ?>">
        </div>

      </div>

      <div class="oxyplug-proscons-each-row">
        <div id="oxyplug-proscons-pros">
          <h3>
            <span class="oxyplug-proscons-asterisk">*</span>
            <?php esc_html_e('Pros', 'oxyplug-proscons') ?>
          </h3>
          <?php if (empty($pros)): ?>
            <div>
              <input type="text"
                     name="oxyplug_proscons[pros][]"
                     placeholder="<?php esc_attr_e('Enter a positive point...', 'oxyplug-proscons'); ?>">
            </div>
          <?php else: ?>
            <div>
              <input type="text"
                     name="oxyplug_proscons[pros][]"
                     value="<?php echo esc_attr(array_shift($pros)) ?>"
                     placeholder="<?php esc_attr_e('Enter a positive point...', 'oxyplug-proscons'); ?>">
            </div>
            <?php foreach ($pros as $pro): ?>
              <div>
                <input type="text"
                       name="oxyplug_proscons[pros][]"
                       value="<?php echo esc_attr($pro) ?>"
                       placeholder="<?php esc_attr_e('Enter a positive point...', 'oxyplug-proscons'); ?>">
                <button class="button button-danger oxyplug-proscons-remove-pro" type="button">
                  <i class="dashicons dashicons-trash"></i>
                </button>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
          <button id="oxyplug-proscons-add-pro"
                  class="button"
                  type="button">
            <i class="dashicons dashicons-plus"></i>
            <?php esc_html_e('Add Pro', 'oxyplug-proscons') ?>
          </button>
        </div>
        <div id="oxyplug-proscons-cons">
          <h3>
            <span class="oxyplug-proscons-asterisk">*</span>
            <?php esc_html_e('Cons', 'oxyplug-proscons') ?>
          </h3>
          <?php if (empty($cons)): ?>
            <div>
              <input type="text"
                     name="oxyplug_proscons[cons][]"
                     placeholder="<?php esc_attr_e('Enter a negative point...', 'oxyplug-proscons'); ?>">
            </div>
          <?php else: ?>
            <div>
              <input type="text"
                     name="oxyplug_proscons[cons][]"
                     value="<?php echo esc_attr(array_shift($cons)) ?>"
                     placeholder="<?php esc_attr_e('Enter a negative point...', 'oxyplug-proscons'); ?>">
            </div>
            <?php foreach ($cons as $con): ?>
              <div>
                <input type="text"
                       name="oxyplug_proscons[cons][]"
                       value="<?php echo esc_attr($con) ?>"
                       placeholder="<?php esc_attr_e('Enter a negative point...', 'oxyplug-proscons'); ?>">
                <button class="button button-danger oxyplug-proscons-remove-con" type="button">
                  <i class="dashicons dashicons-trash"></i>
                </button>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
          <button id="oxyplug-proscons-add-con"
                  class="button"
                  type="button">
            <i class="dashicons dashicons-plus"></i>
            <?php esc_html_e('Add Con', 'oxyplug-proscons') ?>
          </button>
        </div>
      </div>

      <button id="oxyplug-proscons-save" type="button" class="button button-primary oxyplug-proscons-submit">
        <?php esc_html_e('Save', 'oxyplug-proscons'); ?>
      </button>

      <hr class="oxyplug-proscons-separator">

      <a class="link-to-oxyplug-proscons"
         href="<?php echo esc_attr(static::$documentation_url); ?>"
         target="_blank">
        <?php esc_html_e('Read More', 'oxyplug-proscons') ?>
      </a>
    </div>
    <?php
  }

  /**
   * @return void
   */
  public static function oxyplug_proscons_save()
  {
    if (isset($_POST['oxyplug_proscons_save_nonce']) && isset($_POST['action'])) {
      $nonce = sanitize_text_field(wp_unslash($_POST['oxyplug_proscons_save_nonce']));
      if (wp_verify_nonce($nonce, 'oxyplug_proscons_save')) {

        // Pros / Cons
        if (empty($_POST['oxyplug_proscons']['pros']) && empty($_POST['oxyplug_proscons']['cons'])) {
          wp_send_json_error(array('message' => esc_html__('At least one con/pro is required.', 'oxyplug-proscons')));
        }
        if (!is_array($_POST['oxyplug_proscons']['pros']) || !is_array($_POST['oxyplug_proscons']['cons'])) {
          wp_send_json_error(array('message' => esc_html__('The pros/cons are invalid.', 'oxyplug-proscons')));
        }

        // Product Name
        if (empty($_POST['oxyplug_proscons']['product_name'])) {
          wp_send_json_error(array('message' => esc_html__('The product name is required.', 'oxyplug-proscons')));
        }
        $product_name = trim(sanitize_text_field($_POST['oxyplug_proscons']['product_name']));

        // Author Type
        if (empty($_POST['oxyplug_proscons']['author']['type'])) {
          wp_send_json_error(array('message' => esc_html__('The author type is required.', 'oxyplug-proscons')));
        }
        if (!in_array($_POST['oxyplug_proscons']['author']['type'], array('Person', 'Organization'))) {
          wp_send_json_error(array('message' => esc_html__('The author type is invalid.', 'oxyplug-proscons')));
        }
        $author_type = trim(sanitize_text_field($_POST['oxyplug_proscons']['author']['type']));

        // Author Name
        if (empty($_POST['oxyplug_proscons']['author']['name'])) {
          wp_send_json_error(array('message' => esc_html__('The author name is required.', 'oxyplug-proscons')));
        }
        $author_name = trim(sanitize_text_field($_POST['oxyplug_proscons']['author']['name']));

        $post_id = (int)(sanitize_text_field($_POST['post_id']));
        if ($post_id > 0) {
          $user_id = get_current_user_id();
          if (empty(get_user_meta($user_id, '_oxyplug_proscons_author_type', true))) {
            update_user_meta($user_id, '_oxyplug_proscons_author_type', $author_type);
          }
          if (empty(get_user_meta($user_id, '_oxyplug_proscons_author_name', true))) {
            update_user_meta($user_id, '_oxyplug_proscons_author_name', $author_name);
          }

          // Pros
          delete_post_meta($post_id, '_oxyplug_proscons_pro');
          $positive_notes = array();
          $position = 0;
          foreach ($_POST['oxyplug_proscons']['pros'] as $pro) {
            $pro = trim(sanitize_text_field($pro));
            if ($pro) {
              $position++;
              add_post_meta($post_id, '_oxyplug_proscons_pro', $pro);
              $positive_notes[] = array(
                '@type' => 'ListItem',
                'position' => $position,
                'name' => $pro
              );
            }
          }

          // Cons
          delete_post_meta($post_id, '_oxyplug_proscons_con');
          $negative_notes = array();
          $position = 0;
          foreach ($_POST['oxyplug_proscons']['cons'] as $con) {
            $con = trim(sanitize_text_field($con));
            if ($con) {
              $position++;
              add_post_meta($post_id, '_oxyplug_proscons_con', $con);
              $negative_notes[] = array(
                '@type' => 'ListItem',
                'position' => $position,
                'name' => $con
              );
            }
          }

          $positive_notes_count = count($positive_notes);
          $negative_notes_count = count($negative_notes);
          if ($positive_notes_count || $negative_notes_count) {
            // Title
            if (empty($_POST['oxyplug_proscons']['title'])) {
              $title = 'The pros & cons of this product';
            } else {
              $title = trim(sanitize_text_field($_POST['oxyplug_proscons']['title']));
            }
            update_post_meta($post_id, '_oxyplug_proscons_title', $title);

            $structured_data = array(
              '@context' => 'http://schema.org',
              '@type' => 'Product',
              'name' => $product_name,
              'review' => array(
                '@type' => 'Review',
                'name' => sprintf(esc_html__('%s review', 'oxyplug-proscons'), $product_name),
                'author' => array(
                  '@type' => $author_type,
                  'name' => $author_name,
                ),
              )
            );

            if ($positive_notes_count) {
              $structured_data['positiveNotes'] = array(
                '@type' => 'ItemList',
                'itemListElement' => $positive_notes
              );
            }

            if ($negative_notes_count) {
              $structured_data['negativeNotes'] = array(
                '@type' => 'ItemList',
                'itemListElement' => $negative_notes
              );
            }

            update_post_meta($post_id, '_oxyplug_proscons_structured_data', $structured_data);

            $post = get_post($post_id);
            if (empty(trim($post->post_content))) {
              remove_action('post_updated', array(__CLASS__, 'unempty'), 10);
              wp_update_post(array('ID' => $post->ID, 'post_content' => ' '));
            }
          } else {
            delete_post_meta($post_id, '_oxyplug_proscons_structured_data');
            delete_post_meta($post_id, '_oxyplug_proscons_title');
          }

          wp_send_json_success(array('message' => esc_html__('Successfully saved.', 'oxyplug-proscons')));
        } else {
          wp_send_json_error(array('message' => esc_html__('The post_id is invalid.', 'oxyplug-proscons')));
        }

      } else {
        wp_send_json(array('message' => esc_html__('Wrong wpnonce. Refresh the page.', 'oxyplug-proscons')), 403);
      }

      wp_send_json_error(array('message' => esc_html__('Unknown Error!', 'oxyplug-proscons')), 422);
    }
  }

  public static function unempty($post_id, $post)
  {
    if ((defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) || !current_user_can('edit_post', $post_id)) {
      return;
    }

    if (empty(trim($post->post_content))) {
      remove_action('post_updated', array(__CLASS__, 'unempty'), 10);
      wp_update_post(array('ID' => $post->ID, 'post_content' => ' '));
    }
  }
}