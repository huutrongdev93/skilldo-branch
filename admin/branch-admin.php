<?php
class AdminBranch {
    static function register($tabs) {
        $tabs['branch'] = [
            'group'         => 'commerce',
            'label'         => trans('branch.system.title'),
            'description'   => trans('branch.system.description'),
            'callback'      => 'AdminBranch::render',
            'icon'          => '<i class="fa-duotone fa-warehouse-full"></i>',
            'form'          => false
        ];
        return $tabs;
    }
    
    static function render(): void
    {
        $branches = Branch::gets();

        $provinces = Cart_Location::cities();

        foreach ($branches as $key => $branch) {
            $form = form();
            $form->text('branch[name]', ['id' => 'branch_'.$branch->id.'_name',  'label' => trans('branch.field.name'), 'start' => 12], $branch->name);
            $form->email('branch[email]', ['id' => 'branch_'.$branch->id.'_email',  'label' => trans('general.email'),'start' => 6], $branch->email);
            $form->phone('branch[phone]', ['id' => 'branch_'.$branch->id.'_phone',  'label' => trans('general.phone'),'start' => 6], $branch->phone);
            $form->text('branch[address]', ['id' => 'branch_'.$branch->id.'_address',  'label' => trans('general.address'),'start' => 6], $branch->address);
            $form->select2('branch[city]', $provinces, ['id' => 'branch_'.$branch->id.'_city',  'label' => trans('branch.city'), 'start' => 6, 'class'=> 'stock-locations-input stock-locations-city', 'data-id' => $branch->city], $branch->city);
            $form->select2('branch[district]', [], ['id' => 'branch_'.$branch->id.'_district',  'label' => trans('branch.district'),'start' => 6, 'class'=> 'stock-locations-input stock-locations-district', 'data-id' => $branch->district], $branch->district);
            $form->select2('branch[ward]', [], ['id' => 'branch_'.$branch->id.'_ward',  'label' => trans('branch.ward'),'start' => 6, 'class'=> 'stock-locations-input stock-locations-ward', 'data-id' => $branch->ward], $branch->ward);
            $form = apply_filters('admin_branch_form', $form, $branch);
            $branch->form = $form;
            $branches[$key] = $branch;
        }

        $form = form();
        $form->text('branch[name]', ['label' => trans('branch.field.name'), 'start' => 12]);
        $form->email('branch[email]', ['label' => trans('general.email'),'start' => 6]);
        $form->phone('branch[phone]', ['label' => trans('general.phone'),'start' => 6]);
        $form->text('branch[address]', ['label' => trans('general.address'),'start' => 6]);
        $form->select2('branch[city]', $provinces, ['label' => trans('branch.city'), 'start' => 6, 'class'=> 'stock-locations-input stock-locations-city', 'data-id' => Arr::first($provinces)]);
        $form->select2('branch[district]', [], ['label' => trans('branch.district'),'start' => 6, 'class'=> 'stock-locations-input stock-locations-district']);
        $form->select2('branch[ward]', [], ['label' => trans('branch.ward'),'start' => 6, 'class'=> 'stock-locations-input stock-locations-ward']);
        $form = apply_filters('admin_branch_form', $form, []);

        Plugin::view(BRANCH_NAME, 'views/branch', [
            'branches' => $branches,
            'form' => $form,
            'provinces' => $provinces
        ]);
    }
}

add_filter('skd_system_tab', 'AdminBranch::register', 20);