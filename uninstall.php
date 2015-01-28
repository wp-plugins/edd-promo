<?php
if ( !defined('WP_UNINSTALL_PLUGIN') ) {
    exit();
}
delete_option('edd_promo_options');
global $wpdb;
$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}eddpromorecords");
$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}eddpromonewsletter");
?>
