<?php
/**
 * Plugin Name:       WP Gastmanager
 * Description:       Plugin zur Aufgabenverwaltung für Hotels und Gastgewerbe.
 * Version:           1.0.0
 * Author:            marti99-lab
 * License:           GPL2
 * License URI:       https://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * Text Domain:       wp-gastmanager
 * Domain Path:       /languages
 */
// wp-gastmanager.php

defined('ABSPATH') || exit;

// Plugin-Pfad definieren
define('WPGM_PATH', plugin_dir_path(__FILE__));

// Klassen einbinden
require_once WPGM_PATH . 'includes/class-cpt-aufgabe.php';
require_once WPGM_PATH . 'includes/class-roles.php';
require_once WPGM_PATH . 'includes/class-rest-api.php';
require_once WPGM_PATH . 'includes/class-csv-export.php';
require_once WPGM_PATH . 'includes/class-admin-filters.php';

// Diese Hooks sorgen dafür, dass die Filter im Admin-Bereich angezeigt werden und die Abfrage entsprechend angepasst wird
add_action('restrict_manage_posts', ['WPGM_Admin_Filters', 'render_filters']);
add_action('pre_get_posts',        ['WPGM_Admin_Filters', 'filter_query']);

// Aktionen registrieren
add_action('init', ['WPGM_CPT_Aufgabe', 'register_aufgabe_post_type']);
// Shortcode registrieren
add_action('init', ['WPGM_CPT_Aufgabe', 'register_shortcode']);

// REST-API-Endpunkte des Plugins registrieren
add_action('rest_api_init', ['WPGM_REST_API', 'register_routes']);

// Hook für Metaboxen
add_action('add_meta_boxes', ['WPGM_CPT_Aufgabe', 'add_metaboxen']);
// Hook für Speichern
add_action('save_post', ['WPGM_CPT_Aufgabe', 'save_metaboxen']);

// Registriert Backend-Menü und verarbeitet CSV-Export, wenn ausgelöst
add_action('admin_menu', ['WPGM_CSV_Export', 'register_menu']);
add_action('admin_init', ['WPGM_CSV_Export', 'maybe_export_csv']);

// Benutzerrollen bei Plugin-Aktivierung registrieren
register_activation_hook(__FILE__, ['WPGM_Role_Manager', 'register_roles']);
// Rollen bei Deaktivierung entfernen
register_deactivation_hook(__FILE__, ['WPGM_Role_Manager', 'remove_roles']);



