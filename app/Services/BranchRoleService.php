<?php
namespace BranchManagement\Services;

class BranchRoleService
{
    static function group( $group )
    {
        $group['branch'] = [
            'label' => trans('branch-management::role.title'),
            'capabilities' => array_keys(static::capabilities())
        ];
        return $group;
    }

    static function label( $label ): array
    {
        return array_merge( $label, static::capabilities() );
    }

    static function capabilities(): array
    {
        $label['branch_list']      = trans('branch-management::role.list');
        $label['branch_add']       = trans('branch-management::role.add');
        $label['branch_edit']      = trans('branch-management::role.edit');
        return $label;
    }
}

