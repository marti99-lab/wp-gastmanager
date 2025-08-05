<?php
// includes/class-rest-api.php

defined('ABSPATH') || exit;

class WPGM_REST_API
{
    public static function register_routes()
    {
        register_rest_route('wp-gastmanager/v1', '/aufgaben', [
            'methods'             => 'GET',
            'callback'            => [__CLASS__, 'get_aufgaben'],
            'permission_callback' => function () {
                return is_user_logged_in();
            }
        ]);
    }

    public static function get_aufgaben($request)
    {
        $current_user = wp_get_current_user();
        $allowed_roles = ['administrator', 'editor', 'manager'];
        $show_all = array_intersect($current_user->roles, $allowed_roles);

        $args = [
            'post_type'      => 'aufgabe',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'orderby'        => 'meta_value',
            'meta_key'       => '_wpgm_faelligkeit',
            'order'          => 'ASC',
        ];

        if (empty($show_all)) {
            $args['meta_query'] = [
                [
                    'key'     => '_wpgm_verantwortlich',
                    'value'   => get_current_user_id(),
                    'compare' => '=',
                ]
            ];
        }

        $query = new WP_Query($args);
        $result = [];

        while ($query->have_posts()) {
            $query->the_post();

            $result[] = [
                'id'            => get_the_ID(),
                'title'         => get_the_title(),
                'zimmernummer'  => get_post_meta(get_the_ID(), '_wpgm_zimmernummer', true),
                'faelligkeit'   => get_post_meta(get_the_ID(), '_wpgm_faelligkeit', true),
                'verantwortlich'=> get_post_meta(get_the_ID(), '_wpgm_verantwortlich', true),
                'link'          => get_permalink(),
            ];
        }

        wp_reset_postdata();
        return rest_ensure_response($result);
    }
}
