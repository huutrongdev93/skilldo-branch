<?php
Class Branch_Activator {
    public static function activate(): void
    {
        $db = include BRANCH_PATH.'/database/database.php';
        $db->up();
        self::addRole();
    }

    public static function addRole(): void
    {
        $root = Role::get('root');
        $root->add_cap('branch_list');
        $root->add_cap('branch_add');
        $root->add_cap('branch_edit');

        $admin = Role::get('administrator');
        $admin->add_cap('branch_list');
        $admin->add_cap('branch_add');
        $admin->add_cap('branch_edit');
    }
}

Class Branch_Deactivation {

    public static function uninstall(): void
    {
        $db = include BRANCH_PATH.'/database/database.php';

        $db->down();
    }
}