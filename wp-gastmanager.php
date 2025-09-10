<?php
/**
 * Plugin Name:       WP Gastmanager
 * Description:       Plugin zur Aufgabenverwaltung für Hotels und Gastgewerbe.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      8.0
 * Author:            marti99-lab
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       wp-gastmanager
 * Domain Path:       /languages
 */

defined('ABSPATH') || exit;

// Konstanten
define('WPGM_PATH', plugin_dir_path(__FILE__));
define('WPGM_URL',  plugin_dir_url(__FILE__));
define('WPGM_BASENAME', plugin_basename(__FILE__));
define('WPGM_VERSION', '1.0.0');

// Klassen einbinden
require_once WPGM_PATH . 'includes/class-cpt-aufgabe.php';
require_once WPGM_PATH . 'includes/class-roles.php';
require_once WPGM_PATH . 'includes/class-rest-api.php';
require_once WPGM_PATH . 'includes/class-csv-export.php';
require_once WPGM_PATH . 'includes/class-admin-filters.php';

// Übersetzungen laden
add_action('plugins_loaded', function () {
    load_plugin_textdomain('wp-gastmanager', false, dirname(plugin_basename(__FILE__)) . '/languages');
});

// Admin-Listenfilter
add_action('restrict_manage_posts', ['WPGM_Admin_Filters', 'render_filters']);
add_action('pre_get_posts',        ['WPGM_Admin_Filters', 'filter_query']);

// CPT & Shortcode
add_action('init', ['WPGM_CPT_Aufgabe', 'register_aufgabe_post_type']);
add_action('init', ['WPGM_CPT_Aufgabe', 'register_shortcode']);

// REST
add_action('rest_api_init', ['WPGM_REST_API', 'register_routes']);

// Metaboxen & Save
add_action('add_meta_boxes', ['WPGM_CPT_Aufgabe', 'add_metaboxen']);
add_action('save_post',      ['WPGM_CPT_Aufgabe', 'save_metaboxen']);

// CSV-Export
add_action('admin_menu', ['WPGM_CSV_Export', 'register_menu']);
add_action('admin_init', ['WPGM_CSV_Export', 'maybe_export_csv']);

// Aktivierung/Deaktivierung
register_activation_hook(__FILE__, function () {
    WPGM_Role_Manager::register_roles();
    WPGM_CPT_Aufgabe::register_aufgabe_post_type();
    flush_rewrite_rules();
});

register_deactivation_hook(__FILE__, function () {
    WPGM_Role_Manager::remove_roles();
    flush_rewrite_rules();
});




