<?php
// includes/class-cpt-aufgabe.php

defined('ABSPATH') || exit;

// WP-Gastmanager Custom Post Type Aufgabe (To-do)
class WPGM_CPT_Aufgabe
{
    // Registriert den Custom Post Type „aufgabe“ für Aufgabenverwaltung
    public static function register_aufgabe_post_type()
    {
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

    // Fügt die Metaboxen für Zimmernummer, Fälligkeit und Verantwortliche hinzu
    public static function add_metaboxen()
    {
        add_meta_box(
            'wpgm_zimmernummer',
            __('Zimmernummer', 'wp-gastmanager'),
            [__CLASS__, 'render_zimmernummer_metabox'],
            'aufgabe',
            'normal',
            'default'
        );

        add_meta_box(
            'wpgm_faelligkeit',
            __('Fälligkeitsdatum', 'wp-gastmanager'),
            [__CLASS__, 'render_faelligkeit_metabox'],
            'aufgabe',
            'side'
        );

        add_meta_box(
            'wpgm_verantwortlich',
            __('Verantwortliche Person', 'wp-gastmanager'),
            [__CLASS__, 'render_verantwortlich_metabox'],
            'aufgabe',
            'side'
        );

        add_meta_box(
            'wpgm_fortschritt',
            __('Fortschritt (%)', 'wp-gastmanager'),
            [__CLASS__, 'render_fortschritt_metabox'],
            'aufgabe',
            'side'
        );
    }

    // Metaboxen anzeigen in HTML
    public static function render_zimmernummer_metabox($post)
    {
        $value = get_post_meta($post->ID, '_wpgm_zimmernummer', true);
        echo '<label for="wpgm_zimmernummer">' . esc_html__('Zimmernummer:', 'wp-gastmanager') . '</label><br>';
        echo '<input type="text" id="wpgm_zimmernummer" name="wpgm_zimmernummer" value="' . esc_attr($value) . '" />';
    }

    public static function render_faelligkeit_metabox($post)
    {
        $value = get_post_meta($post->ID, '_wpgm_faelligkeit', true);
        echo '<label for="wpgm_faelligkeit">' . esc_html__('Fällig bis:', 'wp-gastmanager') . '</label><br>';
        echo '<input type="date" id="wpgm_faelligkeit" name="wpgm_faelligkeit" value="' . esc_attr($value) . '" />';
    }

    public static function render_verantwortlich_metabox($post)
    {
        $value = get_post_meta($post->ID, '_wpgm_verantwortlich', true);
        $users = get_users(['role__in' => ['author', 'editor', 'administrator']]);

        echo '<label for="wpgm_verantwortlich">' . esc_html__('Verantwortlich:', 'wp-gastmanager') . '</label><br>';
        echo '<select id="wpgm_verantwortlich" name="wpgm_verantwortlich">';
        echo '<option value="">' . esc_html__('– auswählen –', 'wp-gastmanager') . '</option>';
        foreach ($users as $user) {
            $selected = ($user->ID == $value) ? 'selected' : '';
            echo '<option value="' . esc_attr($user->ID) . '" ' . $selected . '>' . esc_html($user->display_name) . '</option>';
        }
        echo '</select>';
    }

    public static function render_fortschritt_metabox($post)
    {
        $value = get_post_meta($post->ID, '_wpgm_fortschritt', true);
        echo '<label for="wpgm_fortschritt">' . esc_html__('Fortschritt in %', 'wp-gastmanager') . '</label><br>';
        echo '<input type="number" id="wpgm_fortschritt" name="wpgm_fortschritt" value="' . esc_attr($value) . '" min="0" max="100" step="1" /> %';
    }

    // Metadaten speichern
    public static function save_metaboxen($post_id)
    {
        if (get_post_type($post_id) !== 'aufgabe') return;
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

        if (isset($_POST['wpgm_zimmernummer'])) {
            update_post_meta($post_id, '_wpgm_zimmernummer', sanitize_text_field($_POST['wpgm_zimmernummer']));
        }

        if (isset($_POST['wpgm_faelligkeit'])) {
            update_post_meta($post_id, '_wpgm_faelligkeit', sanitize_text_field($_POST['wpgm_faelligkeit']));
        }

        if (isset($_POST['wpgm_verantwortlich'])) {
            update_post_meta($post_id, '_wpgm_verantwortlich', intval($_POST['wpgm_verantwortlich']));
        }

        if (isset($_POST['wpgm_fortschritt'])) {
            $wert = min(100, max(0, intval($_POST['wpgm_fortschritt'])));
            update_post_meta($post_id, '_wpgm_fortschritt', $wert);
        }
    }

    // Shortcode registrieren
    public static function register_shortcode()
    {
        add_shortcode('wpgm_aufgabenliste', [__CLASS__, 'render_aufgabenliste']);
    }

    // Callback für den Shortcode
    public static function render_aufgabenliste($atts)
    {
        $current_user = wp_get_current_user();
        $allowed_roles = ['administrator', 'editor', 'manager'];
        $show_all = array_intersect($current_user->roles, $allowed_roles);

        $atts = shortcode_atts([
            'orderby' => 'meta_value',
            'order'   => 'ASC',
            'show'    => 'own'
        ], $atts);

        $wants_all = $atts['show'] === 'all' && !empty($show_all);

        $args = [
            'post_type'      => 'aufgabe',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'meta_key'       => $atts['orderby'] === 'meta_value' ? '_wpgm_faelligkeit' : '',
            'orderby'        => $atts['orderby'],
            'order'          => $atts['order'],
        ];

        if (!$wants_all) {
            $args['meta_query'] = [
                [
                    'key'     => '_wpgm_verantwortlich',
                    'value'   => get_current_user_id(),
                    'compare' => '=',
                ]
            ];
        }

        $query = new WP_Query($args);
        if (!$query->have_posts()) {
            return '<p>' . esc_html__('Keine Aufgaben vorhanden.', 'wp-gastmanager') . '</p>';
        }

        ob_start();
        echo '<ul class="wpgm-aufgabenliste">';
        while ($query->have_posts()) {
            $query->the_post();
            $faellig = get_post_meta(get_the_ID(), '_wpgm_faelligkeit', true);
            $zimmer = get_post_meta(get_the_ID(), '_wpgm_zimmernummer', true);
            $verantwortlich = get_post_meta(get_the_ID(), '_wpgm_verantwortlich', true);
            $user = $verantwortlich ? get_userdata($verantwortlich) : null;
            $fortschritt = get_post_meta(get_the_ID(), '_wpgm_fortschritt', true);

            echo '<li>';
            echo '<strong>' . esc_html(get_the_title()) . '</strong><br>';
            echo esc_html__('Zimmer', 'wp-gastmanager') . ': ' . esc_html($zimmer) . '<br>';
            echo esc_html__('Fällig bis', 'wp-gastmanager') . ': ' . esc_html($faellig) . '<br>';
            echo esc_html__('Verantwortlich', 'wp-gastmanager') . ': ' . ($user ? esc_html($user->display_name) : '–') . '<br>';
            echo esc_html__('Fortschritt', 'wp-gastmanager') . ': ' . esc_html($fortschritt) . '%<br>';
            echo '</li>';
        }
        echo '</ul>';
        wp_reset_postdata();
        return ob_get_clean();
    }
}
