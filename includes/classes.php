<?php

class WP_Deep_CF7_Entry {

  const post_type = 'deep_cf7entry';

  private static $found_items = 0;
  private static $current = null;

  public $id;
  public $name;
  public $title;

  public static function count() {
    return self::$found_items;
  }

  public static function set_current( self $obj ) {
    self::$current = $obj;
  }

  public static function get_current() {
    return self::$current;
  }

  public static function reset_current() {
    self::$current = null;
  }

  public static function register_post_type() {
    $capability_type = self::post_type . '_cap';
    register_post_type( self::post_type, array(
      'labels' => array(
        'name' => __( 'Contact Form Entries', 'wpdeep_cf7' ),
        'singular_name' => __( 'Contact Form Entry', 'wpdeep_cf7' ) 
        ),
      'rewrite' => false,
      'query_var' => false,
      'capability_type' => $capability_type,
      /*'capabilities' => array(
        'delete_posts'           => "delete_deep_cf7entry_caps",
        'delete_post'    => "delete_{$capability_type}",
        'delete_posts'           => "delete_{$capability_type}s",
        'delete_private_posts'   => "delete_private_{$capability_type}s",
        'delete_published_posts' => "delete_published_{$capability_type}s",
        'delete_others_posts'    => "delete_others_{$capability_type}s",
        'edit_post'    => "edit_{$capability_type}",
        'read_post'    => "read_{$capability_type}",
        'edit_posts'     => "edit_{$capability_type}s",
        'edit_others_posts'  => "edit_others_{$capability_type}s",
        'publish_posts'    => "publish_{$capability_type}s",
        'read_private_posts'   => "read_private_{$capability_type}s",
        'edit_private_posts'     => "edit_private_{$capability_type}s",
        'edit_published_posts'   => "edit_published_{$capability_type}s",
        ),*/
      ) );
      // delete_deep_cf7entry_caps
}

public static function find( $args = '' ) {
  $defaults = array(
    'post_status' => 'any',
    'posts_per_page' => -1,
    'offset' => 0,
    'orderby' => 'ID',
    'order' => 'ASC' );

  $args = wp_parse_args( $args, $defaults );

  $args['post_type'] = self::post_type;

  $q = new WP_Query();
  $posts = $q->query( $args );

  self::$found_items = $q->found_posts;

  $objs = array();

  foreach ( (array) $posts as $post )
    $objs[] = new self( $post );

  return $objs;
}

public function __construct( $post = null ) {
  $post = get_post( $post );
  if ( $post && self::post_type == get_post_type( $post ) ) {
    $this->id = $post->ID;
    $this->name = $post->post_name;
    $this->title = $post->post_title;
  }
}
}
?>