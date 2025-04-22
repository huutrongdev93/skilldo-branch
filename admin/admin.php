<?php

use SkillDo\Validate\Rule;

include 'table.php';

include 'button.php';

class AdminBranch {
    static function register($tabs) {
        $tabs['branch'] = [
            'group'         => 'commerce',
            'label'         => trans('branch.system.title'),
            'description'   => trans('branch.system.description'),
            'href'          => Url::route('admin.branch'),
            'icon'          => '<i class="fa-duotone fa-warehouse-full"></i>',
            'form'          => false
        ];
        return $tabs;
    }


    static function form(FormAdmin $form): FormAdmin
    {
        $object = Cms::getData('object');

        $provinces = \Skilldo\Location::provincesOptions();

        $provinces = Arr::prepend($provinces, trans('checkout.field.city.select'), '');

        $form->leftTop
            ->addGroup('info','Thông Tin')
            ->text('name', [
                'label' => trans('branch.field.name'),
                'validations' => Rule::make('name')->notEmpty()
            ])
            ->email('email', [
                'label' => trans('general.email'),
                'start' => 6,
                'validations' => Rule::make('email')->email()
            ])
            ->phone('phone', [
                'label' => trans('general.phone'),
                'start' => 6,
                'validations' => Rule::make('phone')->phone()
            ]);

        $districtOptions = [];

        $wardOptions = [];

        if(!empty($object->city))
        {
            $districtOptions = \Skilldo\Location::districtsOptions($object->city);

            if(!empty($object->district))
            {
                $wardOptions = \Skilldo\Location::wardsOptions($object->district);
            }
        }

        $form->right
            ->addGroup('address', 'Địa chỉ')
            ->text('address', ['label' => trans('general.address')])
            ->select2('city', $provinces, ['label' => trans('branch.city'),  'data-input-address' => 'city', 'data-id' => (!empty($object->city)) ? $object->city : Arr::first($provinces)])
            ->select2('district', $districtOptions, ['label' => trans('branch.district'), 'data-input-address' => 'district'])
            ->select2('ward', $wardOptions, ['label' => trans('branch.ward'), 'data-input-address' => 'ward']);

        return $form;
    }

    static function save($id, $insertData): SKD_Error|int
    {
        return Branch::insert($insertData);
    }
}

add_filter('skd_system_tab', 'AdminBranch::register', 20);
add_filter('manage_branch_input', 'AdminBranch::form');
add_filter('form_submit_branch', 'AdminBranch::save', 10, 2);