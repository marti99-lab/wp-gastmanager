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

// Benutzerrollen bei Plugin-Aktivierung registrieren
register_activation_hook(__FILE__, ['WPGM_Role_Manager', 'register_roles']);
// Rollen bei Deaktivierung entfernen
register_deactivation_hook(__FILE__, ['WPGM_Role_Manager', 'remove_roles']);



