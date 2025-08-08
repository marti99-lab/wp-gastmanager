<?php
// includes/class-admin-filters.php
defined('ABSPATH') || exit;

class WPGM_Admin_Filters {
    // Filter-UI oberhalb der Liste
    public static function render_filters() {
        $screen = get_current_screen();
        if (!$screen || $screen->post_type !== 'aufgabe') return;

        $val_user  = isset($_GET['wpgm_verantwortlich']) ? absint($_GET['wpgm_verantwortlich']) : '';
        $val_room  = isset($_GET['wpgm_zimmernummer'])   ? sanitize_text_field($_GET['wpgm_zimmernummer']) : '';
        $val_date  = isset($_GET['wpgm_faellig_bis'])    ? sanitize_text_field($_GET['wpgm_faellig_bis']) : '';

        // Verantwortliche (Dropdown)
        $users = get_users(['role__in' => ['administrator','editor','author','mitarbeiter','hausdame','technik']]);
        echo '<select name="wpgm_verantwortlich" id="wpgm_verantwortlich">';
        echo '<option value="">' . esc_html__('– Verantwortlich –', 'wp-gastmanager') . '</option>';
        foreach ($users as $u) {
            printf('<option value="%d"%s>%s</option>',
                $u->ID,
                selected($val_user, $u->ID, false),
                esc_html($u->display_name)
            );
        }
        echo '</select>';

        // Zimmernummer
        printf(
            ' <input type="text" name="wpgm_zimmernummer" placeholder="%s" value="%s" />',
            esc_attr__('Zimmernummer', 'wp-gastmanager'),
            esc_attr($val_room)
        );

        // Fällig bis (Datum)
        printf(
            ' <input type="date" name="wpgm_faellig_bis" value="%s" />',
            esc_attr($val_date)
        );
    }

    // Query anpassen nach gesetzten Filtern
    public static function filter_query($query) {
        if (!is_admin() || !$query->is_main_query()) return;
        if ($query->get('post_type') !== 'aufgabe') return;

        $meta_query = ['relation' => 'AND'];

        if (!empty($_GET['wpgm_verantwortlich'])) {
            $meta_query[] = [
                'key'     => '_wpgm_verantwortlich',
                'value'   => absint($_GET['wpgm_verantwortlich']),
                'compare' => '=',
            ];
        }

        if (!empty($_GET['wpgm_zimmernummer'])) {
            $meta_query[] = [
                'key'     => '_wpgm_zimmernummer',
                'value'   => sanitize_text_field($_GET['wpgm_zimmernummer']),
                'compare' => 'LIKE', // oder '=' für exakte Treffer
            ];
        }

        if (!empty($_GET['wpgm_faellig_bis'])) {
            $meta_query[] = [
                'key'     => '_wpgm_faelligkeit',
                'value'   => sanitize_text_field($_GET['wpgm_faellig_bis']),
                'compare' => '<=',
                'type'    => 'DATE',
            ];
            // sinnvolle Standardsortierung nach Fälligkeit
            $query->set('meta_key', '_wpgm_faelligkeit');
            $query->set('orderby', 'meta_value');
            $query->set('order', 'ASC');
        }

        // Nur setzen, wenn mindestens ein Filter aktiv ist
        if (count($meta_query) > 1) {
            $query->set('meta_query', $meta_query);
        }
    }
}
