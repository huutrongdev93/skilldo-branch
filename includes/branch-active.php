<?php
Class Branch_Activator {
    public static function activate(): void
    {
        $db = include BRANCH_PATH.'/database/database.php';
        $db->up();
        self::addRole();
        \SkillDo\Cache::delete('table_columns_order');
    }

    public static function addRole(): void
    {
        $root = Role::get('root');
        $root->add('branch_list');
        $root->add('branch_add');
        $root->add('branch_edit');

        $admin = Role::get('administrator');
        $admin->add('branch_list');
        $admin->add('branch_add');
        $admin->add('branch_edit');
    }
}

Class Branch_Deactivation {

    public static function uninstall(): void
    {
        $db = include BRANCH_PATH.'/database/database.php';

        $db->down();
    }
}