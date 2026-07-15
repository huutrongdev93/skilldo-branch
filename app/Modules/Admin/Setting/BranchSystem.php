<?php
namespace BranchManagement\Modules\Admin\Setting;

class BranchSystem
{
    static function register($tabs)
    {
        $tabs['branch'] = [
            'group'         => 'commerce',
            'label'         => trans('branch-management::system.title'),
            'description'   => trans('branch-management::system.description'),
            'href'          => route('admin.branch'),
            'icon'          => '<i class="fa-duotone fa-warehouse-full"></i>',
            'form'          => false
        ];

        return $tabs;
    }
}