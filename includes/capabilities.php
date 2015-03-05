<?php

add_filter( 'map_meta_cap', 'wpdeep_cf7_map_meta_cap', 10, 4 );

function wpdeep_cf7_map_meta_cap( $caps, $cap, $user_id, $args ) {
    $meta_caps = array(
        'wpdeep_cf7_edit_contact_entry' => WP_DEEP_CF7_ADMIN_READ_WRITE_CAPABILITY,
        'wpcf7_edit_contact_entrys' => WP_DEEP_CF7_ADMIN_READ_WRITE_CAPABILITY,
        'wpcf7_read_contact_entrys' => WP_DEEP_CF7_ADMIN_READ_CAPABILITY,
        'wpcf7_delete_contact_entry' => WP_DEEP_CF7_ADMIN_READ_WRITE_CAPABILITY );

    $meta_caps = apply_filters( 'wpdeep_cf7_map_meta_cap', $meta_caps );

    $caps = array_diff( $caps, array_keys( $meta_caps ) );

    if ( isset( $meta_caps[$cap] ) )
        $caps[] = $meta_caps[$cap];

    return $caps;
}

?>