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
            'name' => __('Aufgaben', 'wp-gastmanager'),
            'singular_name' => __('Aufgabe', 'wp-gastmanager'),
            'add_new' => __('Neue Aufgabe', 'wp-gastmanager'),
            'add_new_item' => __('Neue Aufgabe hinzufügen', 'wp-gastmanager'),
            'edit_item' => __('Aufgabe bearbeiten', 'wp-gastmanager'),
            'new_item' => __('Neue Aufgabe', 'wp-gastmanager'),
            'view_item' => __('Aufgabe ansehen', 'wp-gastmanager'),
            'search_items' => __('Aufgaben durchsuchen', 'wp-gastmanager'),
            'not_found' => __('Keine Aufgaben gefunden', 'wp-gastmanager'),
            'menu_name' => __('Aufgaben', 'wp-gastmanager')
        ];

        $args = [
            'labels' => $labels,
            'public' => true,
            'has_archive' => true,
            'menu_position' => 21,
            'menu_icon' => 'dashicons-clipboard',
            'supports' => ['title', 'editor', 'author'],
            'rewrite' => ['slug' => 'aufgabe'],
            'show_in_rest' => true
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
        wp_nonce_field('wpgm_save_meta', 'wpgm_meta_nonce');
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
        $users = get_users(['role__in' => ['administrator','editor','author','manager','mitarbeiter','hausdame','technik']]);
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
        if (wp_is_post_autosave($post_id) || wp_is_post_revision($post_id)) return;
    
        if (!isset($_POST['wpgm_meta_nonce']) || !wp_verify_nonce($_POST['wpgm_meta_nonce'], 'wpgm_save_meta')) {
            return;
        }
    
        if (!current_user_can('edit_post', $post_id)) return;
    
        if (isset($_POST['wpgm_zimmernummer'])) {
            update_post_meta(
                $post_id,
                '_wpgm_zimmernummer',
                sanitize_text_field(wp_unslash($_POST['wpgm_zimmernummer']))
            );
        }
    
        if (isset($_POST['wpgm_faelligkeit'])) {
            $date = sanitize_text_field(wp_unslash($_POST['wpgm_faelligkeit']));
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
                update_post_meta($post_id, '_wpgm_faelligkeit', $date);
            }
        }
    
        if (isset($_POST['wpgm_verantwortlich'])) {
            update_post_meta(
                $post_id,
                '_wpgm_verantwortlich',
                (int) $_POST['wpgm_verantwortlich']
            );
        }
    
        if (isset($_POST['wpgm_fortschritt'])) {
            $wert = (int) $_POST['wpgm_fortschritt'];
            $wert = max(0, min(100, $wert));
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
            'order' => 'ASC',
            'show' => 'own',   // own | all (nur für berechtigte Rollen wirksam)
            'show_filters' => 'yes',   // yes | no
        ], $atts);

        $wants_all = $atts['show'] === 'all' && !empty($show_all);

        // Filter aus der URL ziehen
        $filter_verantw = isset($_GET['wpgm_verantwortlich']) ? absint($_GET['wpgm_verantwortlich']) : 0;
        $filter_zimmer = isset($_GET['wpgm_zimmernummer']) ? sanitize_text_field($_GET['wpgm_zimmernummer']) : '';
        $filter_due = isset($_GET['wpgm_faellig_bis']) ? sanitize_text_field($_GET['wpgm_faellig_bis']) : '';

        // Nur privilegierte Rollen dürfen frei nach "verantwortlich" filtern
        if (empty($show_all)) {
            // Nicht-privilegierte sehen immer nur ihre Aufgaben; ignorieren fremde Auswahl
            $filter_verantw = get_current_user_id();
        }

        $args = [
            'post_type' => 'aufgabe',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'meta_key' => $atts['orderby'] === 'meta_value' ? '_wpgm_faelligkeit' : '',
            'orderby' => $atts['orderby'],
            'order' => $atts['order'],
        ];

        $mq = [];

        // Verantwortlich-Filter
        if ($filter_verantw) {
            $mq[] = [
                'key'     => '_wpgm_verantwortlich',
                'value'   => $filter_verantw,
                'compare' => '=',
            ];
        } elseif (!$wants_all) {
            $mq[] = [
                'key'     => '_wpgm_verantwortlich',
                'value'   => get_current_user_id(),
                'compare' => '=',
            ];
        }
        
        // Zimmernummer (Teiltreffer sinnvoll)
        if ($filter_zimmer !== '') {
            $mq[] = [
                'key'     => '_wpgm_zimmernummer',
                'value'   => $filter_zimmer,
                'compare' => 'LIKE',
            ];
        }
        
        // Fällig bis (<= Datum)
        if ($filter_due !== '') {
            $mq[] = [
                'key'     => '_wpgm_faelligkeit',
                'value'   => $filter_due,
                'compare' => '<=',
                'type'    => 'DATE',
            ];
        }
        
        // Nur setzen, wenn mindestens eine Bedingung vorhanden ist
        if (!empty($mq)) {
            if (count($mq) > 1) {
                $mq['relation'] = 'AND';
            }
            $args['meta_query'] = $mq;
        }
        

        $query = new WP_Query($args);
        if (!$query->have_posts()) {
            return '<p>' . esc_html__('Keine Aufgaben vorhanden.', 'wp-gastmanager') . '</p>';
        }

        ob_start();

        if ($atts['show_filters'] === 'yes') {
            // Nur privilegierte Rollen: Dropdown für Verantwortliche
            $users = [];
            if (!empty($show_all)) {
                $users = get_users(['role__in' => ['administrator', 'editor', 'author', 'manager', 'mitarbeiter', 'hausdame', 'technik']]);
            }

            echo '<form method="get" class="wpgm-filter-form">';
            // Zimmer
            echo '<label>' . esc_html__('Zimmernummer', 'wp-gastmanager') . ': ';
            echo '<input type="text" name="wpgm_zimmernummer" value="' . esc_attr($filter_zimmer) . '"></label> ';
            // Fällig bis
            echo '<label>' . esc_html__('Fällig bis', 'wp-gastmanager') . ': ';
            echo '<input type="date" name="wpgm_faellig_bis" value="' . esc_attr($filter_due) . '"></label> ';

            // Verantwortlich nur für privilegierte
            if (!empty($show_all)) {
                echo '<label>' . esc_html__('Verantwortlich', 'wp-gastmanager') . ': ';
                echo '<select name="wpgm_verantwortlich">';
                echo '<option value="0">' . esc_html__('– alle –', 'wp-gastmanager') . '</option>';
                foreach ($users as $u) {
                    printf(
                        '<option value="%d"%s>%s</option>',
                        $u->ID,
                        selected($filter_verantw, $u->ID, false),
                        esc_html($u->display_name)
                    );
                }
                echo '</select></label> ';
            } else {
                // Nicht-privilegierte: verstecktes Feld auf eigenen User fixieren
                echo '<input type="hidden" name="wpgm_verantwortlich" value="' . esc_attr(get_current_user_id()) . '">';
            }

            // show-Param mitnehmen (falls Admin Seite mit show=all nutzt)
            if ($wants_all) {
                echo '<input type="hidden" name="show" value="all">';
            }

            echo '<button type="submit">' . esc_html__('Filter anwenden', 'wp-gastmanager') . '</button>';
            echo '</form><br>';
        }

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
