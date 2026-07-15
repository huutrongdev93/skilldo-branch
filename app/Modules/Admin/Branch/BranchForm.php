<?php
namespace BranchManagement\Modules\Admin\Branch;

use BranchManagement\Models\Branch;
use SkillDo\Cms\FormAdmin\FormAdmin;
use SkillDo\Cms\Location\Location;
use SkillDo\Cms\Location\Location2;
use SkillDo\Cms\Support\Admin;
use SkillDo\Cms\Support\Cms;
use SkillDo\Cms\Support\Url;
use Illuminate\Support\Arr;
use SkillDo\Validate\Rule;

class BranchForm
{
    static function fields(FormAdmin $form): FormAdmin
    {
        $form->setModel(Branch::class);

        $provinces = Location2::provincesOptions();

        $provinces = Arr::prepend($provinces, trans('sicommerce::checkout.field.city.select'), '');

        $form->leftTop()
            ->addGroup('info','Thông Tin')
            ->text('name', [
                'label' => trans('branch-management::system.field.name'),
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

        $wardOptions = [];

        $object = Cms::getData('object');

        if(!empty($object->city))
        {
            $wardOptions = Location2::wardsOptions($object->city);
        }

        $form->right()
            ->addGroup('address', 'Địa chỉ')
            ->text('address', ['label' => trans('general.address')])
            ->select2('city', $provinces, ['label' => trans('branch-management::system.field.city'),  'data-input-address' => 'city', 'data-id' => (!empty($object->city)) ? $object->city : Arr::first($provinces)])
            ->select2('ward', $wardOptions, ['label' => trans('branch-management::system.field.ward'), 'data-input-address' => 'ward']);

        return $form;
    }

    static function buttons(FormAdmin $form): FormAdmin
    {
        $buttons = [];

        if(Admin::isPage('branch_add'))
        {
            $buttons[] = Admin::button('save');
            $buttons[] = Admin::button('back', [
                'href' => Url::route('admin.branch'),
                'class' => ['btn-back-to-redirect']
            ]);
        }

        if(Admin::isPage('branch_edit'))
        {
            $buttons[] = Admin::button('save');
            $buttons[] = Admin::button('add', ['href' => route('admin.branch.add'), 'text' => '', 'tooltip' => 'Thêm mới']);
            $buttons[] = Admin::button('back', [
                'href' => Url::route('admin.branch'),
                'text' => '',
                'tooltip' => 'Quay lại',
                'class' => ['btn-back-to-redirect']
            ]);
        }

        $buttons = apply_filters('branch_form_buttons', $buttons);

        $form->setButtons($buttons);

        return $form;
    }
}