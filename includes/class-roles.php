<?php
// includes/class-roles.php

defined('ABSPATH') || exit;

class WPGM_Role_Manager
{
    // Rollen bei Plugin-Aktivierung registrieren
    public static function register_roles()
    {
        add_role('mitarbeiter', __('Mitarbeiter', 'wp-gastmanager'), [
            'read' => true,
        ]);

        add_role('hausdame', __('Hausdame', 'wp-gastmanager'), [
            'read' => true,
        ]);

        add_role('technik', __('Technik', 'wp-gastmanager'), [
            'read' => true,
        ]);
    }

    // Rollen bei Deaktivierung entfernen (optional)
    public static function remove_roles()
    {
        remove_role('mitarbeiter');
        remove_role('hausdame');
        remove_role('technik');
    }
}
