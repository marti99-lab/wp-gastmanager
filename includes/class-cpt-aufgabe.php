<?php
// includes/class-cpt-aufgabe.php

defined('ABSPATH') || exit;

class WPGM_CPT_Aufgabe {
    public static function register_aufgabe_post_type() {
        $labels = [
            'name'               => __('Aufgaben', 'wp-gastmanager'),
            'singular_name'      => __('Aufgabe', 'wp-gastmanager'),
            'add_new'            => __('Neue Aufgabe', 'wp-gastmanager'),
            'add_new_item'       => __('Neue Aufgabe hinzufügen', 'wp-gastmanager'),
            'edit_item'          => __('Aufgabe bearbeiten', 'wp-gastmanager'),
            'new_item'           => __('Neue Aufgabe', 'wp-gastmanager'),
            'view_item'          => __('Aufgabe ansehen', 'wp-gastmanager'),
            'search_items'       => __('Aufgaben durchsuchen', 'wp-gastmanager'),
            'not_found'          => __('Keine Aufgaben gefunden', 'wp-gastmanager'),
            'menu_name'          => __('Aufgaben', 'wp-gastmanager')
        ];

        $args = [
            'labels'             => $labels,
            'public'             => true,
            'has_archive'        => true,
            'menu_position'      => 21,
            'menu_icon'          => 'dashicons-clipboard',
            'supports'           => ['title', 'editor', 'author'],
            'rewrite'            => ['slug' => 'aufgabe'],
            'show_in_rest'       => true
        ];

        register_post_type('aufgabe', $args);
    }
}
