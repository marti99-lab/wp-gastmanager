<?php
/**
 * Plugin Name:       WP Gastmanager
 * Description:       Aufgabenverwaltung speziell für Hotels und das Gastgewerbe.
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

// Grundlegende Konstanten für Pfade, URLs und Plugin-Infos
define('WPGM_PATH', plugin_dir_path(__FILE__));
define('WPGM_URL',  plugin_dir_url(__FILE__));
define('WPGM_BASENAME', plugin_basename(__FILE__));
define('WPGM_VERSION', '1.0.0');

// Zentrale Klassen laden
require_once WPGM_PATH . 'includes/class-cpt-aufgabe.php';
require_once WPGM_PATH . 'includes/class-roles.php';
require_once WPGM_PATH . 'includes/class-rest-api.php';
require_once WPGM_PATH . 'includes/class-csv-export.php';
require_once WPGM_PATH . 'includes/class-admin-filters.php';

// Sprachdateien einbinden
add_action('plugins_loaded', function () {
    load_plugin_textdomain('wp-gastmanager', false, dirname(plugin_basename(__FILE__)) . '/languages');
});

// Filter-Optionen im Admin-Bereich
add_action('restrict_manage_posts', ['WPGM_Admin_Filters', 'render_filters']);
add_action('pre_get_posts',        ['WPGM_Admin_Filters', 'filter_query']);

// Registrierung von Custom Post Type und Shortcode
add_action('init', ['WPGM_CPT_Aufgabe', 'register_aufgabe_post_type']);
add_action('init', ['WPGM_CPT_Aufgabe', 'register_shortcode']);

// REST-API-Endpunkte aktivieren
add_action('rest_api_init', ['WPGM_REST_API', 'register_routes']);

// Zusätzliche Eingabefelder (Metaboxen) und Speichern
add_action('add_meta_boxes', ['WPGM_CPT_Aufgabe', 'add_metaboxen']);
add_action('save_post',      ['WPGM_CPT_Aufgabe', 'save_metaboxen']);

// Menüpunkt für CSV-Export einbinden
add_action('admin_menu', ['WPGM_CSV_Export', 'register_menu']);
add_action('admin_init', ['WPGM_CSV_Export', 'maybe_export_csv']);

// Bei Aktivierung Rollen anlegen, CPT registrieren und Permalinks aktualisieren
register_activation_hook(__FILE__, function () {
    WPGM_Role_Manager::register_roles();
    WPGM_CPT_Aufgabe::register_aufgabe_post_type();
    flush_rewrite_rules();
});

// Bei Deaktivierung Rollen entfernen und Permalinks neu schreiben
register_deactivation_hook(__FILE__, function () {
    WPGM_Role_Manager::remove_roles();
    flush_rewrite_rules();
});


// -----------------------------------------------------------------------------
// Zusätzliche Spalten in der Admin-Liste für Aufgaben
// -----------------------------------------------------------------------------
add_filter('manage_aufgabe_posts_columns', function($cols){
    $cols['wpgm_zimmer']  = __('Zimmer', 'wp-gastmanager');
    $cols['wpgm_faellig'] = __('Fällig bis', 'wp-gastmanager');
    $cols['wpgm_fort']    = __('% erledigt', 'wp-gastmanager');
    return $cols;
});

add_action('manage_aufgabe_posts_custom_column', function($col, $post_id){
    if ($col === 'wpgm_zimmer')  echo esc_html(get_post_meta($post_id, '_wpgm_zimmernummer', true));
    if ($col === 'wpgm_faellig') echo esc_html(get_post_meta($post_id, '_wpgm_faelligkeit', true));
    if ($col === 'wpgm_fort')    echo esc_html(get_post_meta($post_id, '_wpgm_fortschritt', true)) . '%';
}, 10, 2);

// Spalte "Fällig bis" sortierbar machen
add_filter('manage_edit-aufgabe_sortable_columns', function($cols){
    $cols['wpgm_faellig'] = 'wpgm_faellig';
    return $cols;
});

add_action('pre_get_posts', function($q){
    if (!is_admin() || !$q->is_main_query()) return;
    if ($q->get('post_type') !== 'aufgabe') return;
    if ($q->get('orderby') === 'wpgm_faellig') {
        $q->set('meta_key', '_wpgm_faelligkeit');
        $q->set('orderby', 'meta_value');
        $q->set('meta_type', 'DATE');
    }
});



