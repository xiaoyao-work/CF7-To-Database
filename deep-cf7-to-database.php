<?php

/*
Plugin Name: CF7 to Database 
Plugin URI: http://plugins.deepdemo.us/
Description: Contact Form 7 To Database. Simple but flexible.
Author: DeepDev
Author URI: http://plugins.deepdemo.us/
Text Domain: cf7-to-database
Domain Path: /languages/
Version: 1.0
*/

/* Install and default settings */

if ( ! defined( 'WP_DEEP_CF7_PLUGIN_BASENAME' ) )
  define( 'WP_DEEP_CF7_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

if ( ! defined( 'WP_DEEP_CF7_PLUGIN_DIR' ) )
  define( 'WP_DEEP_CF7_PLUGIN_DIR', untrailingslashit( dirname( __FILE__ ) ) );

if ( ! defined( 'WP_DEEP_CF7_PLUGIN_URL' ) )
  define( 'WP_DEEP_CF7_PLUGIN_URL', untrailingslashit( plugins_url( '', __FILE__ ) ) );

require_once WP_DEEP_CF7_PLUGIN_DIR . '/includes/classes.php';
require_once WP_DEEP_CF7_PLUGIN_DIR . '/includes/functions.php';

add_action( 'activate_' . WP_DEEP_CF7_PLUGIN_BASENAME, 'wpdeep_cf7_install' );

function wpdeep_cf7_install() {
  if ( $opt = get_option( 'wpdeep_cf7' ) )
    return;
  wpdeep_cf7_register_post_types();
  $opt['version'] = "1.0";
  update_option( 'wpcf7', $opt );
}

// add_action('init', 'wpdeep_cf7_register_post_types');
add_action( 'plugins_loaded', 'wpdeep_cf7' );

function wpdeep_cf7() {
  // init();
  add_action( 'admin_menu', 'wpdeep_cf7_admin_menu', 9 );
}


function wpdeep_cf7_register_post_types() {
  register_post_type( 'deep_cf7entry', array(
    'labels' => array(
      'name' => __( 'CF7 Entries' ),
      'singular_name' => __( 'CF7 Entry' ),
      'add_new_item' => __( 'Add New Entry' ),
      'edit_item' => __( 'Edit Entry' ),
      'new_item' => __( 'New Entry' ),
      'view_item' => __( 'View Entry' ),
      'search_item' => __( 'Search Entry' ),
      'parent_item_colon' => __( 'Parent Entry' ),
      'not_found' => __( 'Not Found' ),
      ),
    'menu_position' => 5,
    'public' => true,
    'has_archive' => true,
    'hierarchical' => false, // Whether the post type is hierarchical. Allows Parent to be specified
    'rewrite' => array( 'slug' => 'cf7entry' ),
    'supports' => array( 'title' )
    ) );
}

function wpdeep_cf7_admin_menu() {
  $list = add_submenu_page( 'wpcf7',
    __( 'Entries', 'wpdeep_cf7' ),
    __( 'Entries', 'wpdeep_cf7' ),
    'wpcf7_read_contact_forms', 'wpdeep_cf7',
    'wpdeep_cf7_admin_entry_management_page' );
  add_action( 'load-' . $list, 'wpdeep_cf7_admin_entry_admin_page' );
}

function wpdeep_cf7_admin_entry_admin_page() {
  $current_screen = get_current_screen();
  if ( ! class_exists( 'WPCF7_Contact_Entry_List_Table' ) )
    require_once WP_DEEP_CF7_PLUGIN_DIR . '/includes/class-contact-entry-list-table.php';
  $action = $_GET['action'];
  if (isset($action) && $action == "show" ) {
    return ;
  }

  add_filter( 'manage_' . $current_screen->id . '_columns',
    array( 'WPCF7_Contact_Entry_List_Table', 'define_columns' ) );

  add_screen_option( 'per_page', array(
    'label' => __( 'Contact Forms Entry', 'wpdeep_cf7' ),
    'default' => 20,
    'option' => 'cfseven_contact_entries_per_page' ) );

}

function wpdeep_cf7_admin_entry_management_page() {
  $action = $_GET['action'];
  if (isset($action) && $action == "show" ) {
    $entry = get_post( intval( $_GET['entry_id'] ) );
    $form_id = intval( $_GET['form_id'] );
    $fields = get_post_meta( $form_id, "wp_deep_cf7_fields", true );
    $fields = string2array( $fields );
    ?>

    <div class="wrap">
      <?php screen_icon(); ?>
      <h2><?php
      echo esc_html( __( 'Contact Entry Show', 'wpdeep_cf7' ) );
      echo ' <a href="' . esc_url( add_query_arg( array( 'form_id' => $form_id ) , menu_page_url( 'wpdeep_cf7', false ) ) ) . '" class="add-new-h2">' . esc_html( __( 'Contact Entries', 'wpdeep_cf7' ) ) . '</a>';
      ?></h2>
      <table>
        <?php foreach ($fields as $key => $field) { ?>
        <tr>
          <th><?php echo $field; ?></th>
          <td><?php echo get_post_meta($entry->ID, "cf7_form_" . $field, true); ?></td>
        </tr>
        <?php } ?>
      </table>
    </div>
    <?php } else {
      $list_table = new WPCF7_Contact_Entry_List_Table();
      $list_table->prepare_items();
      ?>
      <div class="wrap">
        <?php screen_icon(); ?>

        <h2><?php
        echo esc_html( __( 'Contact Entries', 'wpdeep_cf7' ) );

        echo ' <a href="' . esc_url( menu_page_url( 'wpcf7-new', false ) ) . '" class="add-new-h2">' . esc_html( __( 'Add New Form', 'wpdeep_cf7' ) ) . '</a>';

        if ( ! empty( $_REQUEST['s'] ) ) {
          echo sprintf( '<span class="subtitle">'
            . __( 'Search results for &#8220;%s&#8221;', 'wpdeep_cf7' )
            . '</span>', esc_html( $_REQUEST['s'] ) );
        }
        ?></h2>
        <form method="get" action="">
          <input type="hidden" name="page" value="<?php echo esc_attr( $_REQUEST['page'] ); ?>" />
          <select name="form_id">
            <?php foreach ($list_table->forms as $key => $form) { ?>
            <option value='<?php echo $form->ID; ?>' <?php if($form->ID == $list_table->form_id) { echo " selected"; }?>><?php echo $form->post_title; ?> </option>
            <?php } ?>
          </select>
          <input type="submit" name="" id="doaction" class="button action" value="Apply">
          <?php // $list_table->search_box( __( 'Search Contact Entries', 'wpdeep_cf7' ), 'wpcf7-contact' ); ?>
          <?php $list_table->display(); ?>
        </form>

      </div>
      <?php
    }
  }

  add_action("wpcf7_before_send_mail", "wpdeep_cf7_entry_save");

  function wpdeep_cf7_entry_save(&$WPCF7_ContactForm) {
    $id = $WPCF7_ContactForm->id;
    $data = $WPCF7_ContactForm->posted_data;
    $scan_form = $WPCF7_ContactForm->scanned_form_tags;

    $entry_id = wp_insert_post( 
      array( 
        'post_type'   => 'deep_cf7entry', 
        'post_status' => "publish"
        )
      );
    if ($entry_id) {
      update_post_meta( $entry_id, 'cf7_form_id', $id );
      if (is_array($scan_form)) {
        foreach ($scan_form as $key => $value) {
          if (!empty($value['name'])) {
            $data[$value['name']] = empty($data[$value['name']]) ? "" : $data[$value['name']];
            update_post_meta( $entry_id, "cf7_form_" . $value['name'], $data[$value['name']] );
          }
        }
      }
    }
  }

  add_action("wpcf7_after_save", "wpdeep_cf7_after_save");

  function wpdeep_cf7_after_save(&$WPCF7_ContactForm) {
    $id = $WPCF7_ContactForm->id;
    $scan_form = $WPCF7_ContactForm->form_scan_shortcode();
    $fields = array();
    if (is_array($scan_form)) {
      foreach ($scan_form as $key => $value) {
        if (!empty($value['name'])) {
          $fields[] = $value['name'];
        }
      }
    }
    update_post_meta($id, 'wp_deep_cf7_fields', array2string($fields));
  }
