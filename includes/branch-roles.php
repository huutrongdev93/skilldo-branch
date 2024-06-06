<?php
class BranchRole {
    static function group( $group ) {
        $group['branch'] = [
            'label' => trans('branch.title'),
            'capabilities' => array_keys(BranchRole::capabilities())
        ];
        return $group;
    }
    static function label( $label ): array
    {
        return array_merge( $label, BranchRole::capabilities() );
    }
    static function capabilities(): array
    {
        $label['branch_list']      = trans('branch.role.list');
        $label['branch_add']       = trans('branch.role.add');
        $label['branch_edit']      = trans('branch.role.edit');
        return $label;
    }
}

add_filter('user_role_editor_group', 'BranchRole::group');
add_filter('user_role_editor_label', 'BranchRole::label');

