<?php
// includes/class-csv-export.php

defined('ABSPATH') || exit;

class WPGM_CSV_Export
{
    public static function register_menu()
    {
        add_submenu_page(
            'edit.php?post_type=aufgabe',
            __('CSV-Export', 'wp-gastmanager'),
            __('CSV-Export', 'wp-gastmanager'),
            'manage_options',
            'wpgm-csv-export',
            [__CLASS__, 'render_export_page']
        );
    }

    public static function render_export_page()
    {
        if (!current_user_can('manage_options')) {
            wp_die(__('Zugriff verweigert', 'wp-gastmanager'));
        }

        echo '<div class="wrap">';
        echo '<h1>' . esc_html__('CSV-Export der Aufgaben', 'wp-gastmanager') . '</h1>';
        echo '<form method="post">';
        submit_button(__('Export starten', 'wp-gastmanager'), 'primary', 'wpgm_export_csv');
        echo '</form>';
        echo '</div>';
    }

    public static function maybe_export_csv()
    {
        if (!is_admin() || !current_user_can('manage_options')) {
            return;
        }

        if (!isset($_POST['wpgm_export_csv'])) {
            return;
        }

        $args = [
            'post_type'      => 'aufgabe',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'orderby'        => 'meta_value',
            'meta_key'       => '_wpgm_faelligkeit',
            'order'          => 'ASC',
        ];

        $query = new WP_Query($args);

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=aufgaben-export.csv');

        $output = fopen('php://output', 'w');
        fputcsv($output, ['ID', 'Titel', 'Zimmer', 'Fällig bis', 'Verantwortlich']);

        while ($query->have_posts()) {
            $query->the_post();
            $id     = get_the_ID();
            $title  = get_the_title();
            $zimmer = get_post_meta($id, '_wpgm_zimmernummer', true);
            $faellig = get_post_meta($id, '_wpgm_faelligkeit', true);
            $verantwortlich_id = get_post_meta($id, '_wpgm_verantwortlich', true);
            $verantwortlich = $verantwortlich_id ? get_userdata($verantwortlich_id)->display_name : '';

            fputcsv($output, [$id, $title, $zimmer, $faellig, $verantwortlich]);
        }

        wp_reset_postdata();
        exit;
    }
}
