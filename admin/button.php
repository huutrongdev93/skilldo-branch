<?php
class BranchAdminButton
{
    static function formButton($module): void
    {
        $buttons = [];

        $view = Url::segment(3);

        if($view == 'add') {
            $buttons[] = Admin::button('save');
            $buttons[] = Admin::button('back', [
                'href' => Url::route('admin.branch'),
                'class' => ['btn-back-to-redirect']
            ]);
        }

        if($view == 'edit')
        {
            $buttons[] = Admin::button('save');
            $buttons[] = Admin::button('add', ['href' => Url::route('admin.branch.add'), 'text' => '', 'tooltip' => 'Thêm mới']);
            $buttons[] = Admin::button('back', [
                'href' => Url::route('admin.branch'),
                'text' => '',
                'tooltip' => 'Quay lại',
                'class' => ['btn-back-to-redirect']
            ]);
        }

        $buttons = apply_filters('branch_form_buttons', $buttons);

        Admin::view('include/form/form-action', ['buttons' => $buttons, 'module' => $module]);
    }

}
add_action('form_branch_action_button', 'BranchAdminButton::formButton');
