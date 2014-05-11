<?php

if ( ! class_exists( 'WP_List_Table' ) )
  require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );

class WPCF7_Contact_Entry_List_Table extends WP_List_Table {
  public $fields;
  public $form_id;
  public $forms;

  public static function define_columns() {
    $q = new WP_Query();
    $forms = $q->query( "post_type=wpcf7_contact_form" );
    if ( isset($_GET['form_id']) && !empty($_GET['form_id']) ) {
      $form_id = intval($_GET['form_id']);
    } else {
      $form = current($forms);
      $form_id = $form->ID;
    }
    /*$entries = $q->query(
      array( 
        'post_type' => 'deep_cf7entry', 
        'meta_key' => 'cf7_form_id',
        'meta_value' => $form_id
        )
      );
    if (!empty($entries)) {
      $curr_entry = current($entries);
      $fields = get_post_meta($curr_entry->ID, "wp_deep_cf7_fields", true);
      $fields = string2array($fields);
    } else {
      $fields = array();
    }
    */

    $fields = get_post_meta($form_id, "wp_deep_cf7_fields", true);
    if ($fields) {
      $fields = string2array($fields);
    } else {
      $fields = array();
    }

    $columns = array( 'cb' => '<input type="checkbox" />', 'id' => __("ID", 'wpdeep_cf7') );
    $custom_columns =  array();
    if (!empty($fields)) {
      foreach ($fields as $key => $field) {
        if ($key > 3) {
          break;
        }
        echo $key;
        $custom_columns["col".$key] = __( $field, 'wpdeep_cf7' );
        // $custom_columns[$field] = __( $field, 'wpdeep_cf7' );
      }
    }
    $columns = array_merge($columns, $custom_columns, array('date' => __( 'Date', 'contact-form-7' ) ) );
    return $columns;
  }

  function __construct() {
    parent::__construct( array(
      'singular' => 'post',
      'plural' => 'posts',
      'ajax' => false ) );

    $q = new WP_Query();
    $forms = $q->query( "post_type=wpcf7_contact_form" );
    $this->forms = $forms;
    if ( isset($_GET['form_id']) && !empty($_GET['form_id']) ) {
      $form_id = intval($_GET['form_id']);
    } else {
      $form = current($forms);
      $form_id = $form->ID;
    }
    $this->form_id = $form_id;
    
    /*$entries = $q->query(
      array( 
        'post_type' => 'deep_cf7entry', 
        'meta_key' => 'cf7_form_id',
        'meta_value' => $form_id
        )
      );
    if (!empty($entries)) {
      $curr_entry = current($entries);
      $fields = get_post_meta($curr_entry->ID, "wp_deep_cf7_fields", true);
      $fields = string2array($fields);
    } else {
      $fields = array();
    }*/

    $fields = get_post_meta($form_id, "wp_deep_cf7_fields", true);
    if ($fields) {
      $fields = string2array($fields);
    } else {
      $fields = array();
    }
    $this->fields = $fields;
  }

  function prepare_items() {
    $current_screen = get_current_screen();
    $per_page = $this->get_items_per_page( 'cfseven_contact_entries_per_page' );

    $this->_column_headers = $this->get_column_info();

    $args = array(
      'posts_per_page' => $per_page,
      'meta_key' => 'cf7_form_id',
      'meta_value' => $this->form_id,
      'orderby' => 'ID',
      'order' => 'ASC',
      'offset' => ( $this->get_pagenum() - 1 ) * $per_page );

    if ( ! empty( $_REQUEST['s'] ) )
      $args['s'] = $_REQUEST['s'];

    if ( ! empty( $_REQUEST['orderby'] ) ) {
      if ( 'ID' == $_REQUEST['orderby'] )
        $args['orderby'] = 'ID';
      elseif ( 'date' == $_REQUEST['orderby'] )
        $args['orderby'] = 'date';
    }

    if ( ! empty( $_REQUEST['order'] ) ) {
      if ( 'asc' == strtolower( $_REQUEST['order'] ) )
        $args['order'] = 'ASC';
      elseif ( 'desc' == strtolower( $_REQUEST['order'] ) )
        $args['order'] = 'DESC';
    }

    $this->items = WP_Deep_CF7_Entry::find( $args );

    $total_items = WP_Deep_CF7_Entry::count();
    $total_pages = ceil( $total_items / $per_page );

    $this->set_pagination_args( array(
      'total_items' => $total_items,
      'total_pages' => $total_pages,
      'per_page' => $per_page ) );
  }

  function get_columns() {
    return get_column_headers( get_current_screen() );
  }

  function get_sortable_columns() {
    $columns = array(
      'title' => array( 'title', true ),
      'author' => array( 'author', false ),
      'date' => array( 'date', false ) );

    return $columns;
  }

  function get_bulk_actions() {
    $actions = array(
      'delete' => __( 'Delete', 'wpdeep_cf7' ) );

    return $actions;
  }

  function column_default( $item, $column_name ) {
    return '';
  }

  function column_cb( $item ) {
    return sprintf(
      '<input type="checkbox" name="%1$s[]" value="%2$s" />',
      $this->_args['singular'],
      $item->id );
  }

  function column_id( $item ) {

    $url = admin_url( 'admin.php?page=wpdeep_cf7&form_id='.absint( $this->form_id ).'&entry_id=' . absint( $item->id ) );
    $show_link = add_query_arg( array( 'action' => 'show' ), $url );
    $a = sprintf( '<a class="row-title" href="%1$s" title="%2$s">%3$s</a>',
      $show_link,
      esc_attr( sprintf( __( 'Show &#8220;%s&#8221;', 'wpdeep_cf7' ), $item->id ) ),
      esc_html( $item->id ) );
    return '<strong>' . $a . '</strong>';
  }

  function column_col0( $item ) {
    $value = get_post_meta($item->id, "cf7_form_".$this->fields[0], true);
    return $value;
  }

  function column_col1( $item ) {
    $value = get_post_meta($item->id, "cf7_form_".$this->fields[1], true);
    return $value;
  }

  function column_col2( $item ) {
    $value = get_post_meta($item->id, "cf7_form_".$this->fields[2], true);
    return $value;
  }

  function column_col3( $item ) {
    $value = get_post_meta($item->id, "cf7_form_".$this->fields[3], true);
    return $value;
  }

  function column_date( $item ) {
    $post = get_post( $item->id );
    if ( ! $post )
      return;
    $t_time = mysql2date( __( 'Y/m/d g:i:s A', 'wpdeep_cf7' ), $post->post_date, true );
    $m_time = $post->post_date;
    $time = mysql2date( 'G', $post->post_date ) - get_option( 'gmt_offset' ) * 3600;

    $time_diff = time() - $time;

    if ( $time_diff > 0 && $time_diff < 24*60*60 )
      $h_time = sprintf( __( '%s ago', 'wpdeep_cf7' ), human_time_diff( $time ) );
    else
      $h_time = mysql2date( __( 'Y/m/d', 'wpdeep_cf7' ), $m_time );

    return '<abbr title="' . $t_time . '">' . $h_time . '</abbr>';
  }
}

?>