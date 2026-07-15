<?php

use BranchManagement\Modules\Admin\Branch\BranchForm;
use BranchManagement\Modules\Admin\Setting\BranchSystem;
use BranchManagement\Services\BranchRoleService;

add_filter('admin_system_tabs', [BranchSystem::class, 'register']);

/*
|--------------------------------------------------------------------------
| Hook Form
|--------------------------------------------------------------------------
| Form::fields - thêm điều chỉnh field trong form add và edit
| Form::buttons - thêm điều chỉnh buttons action trong form add và edit
*/
add_filter('manage_branch_input', [BranchForm::class, 'fields']);
add_filter('manage_branch_input', [BranchForm::class, 'buttons']);

/*
|--------------------------------------------------------------------------
| Hook phân quyền
|--------------------------------------------------------------------------
*/
add_filter('user_role_editor_group', [BranchRoleService::class, 'group']);
add_filter('user_role_editor_label', [BranchRoleService::class, 'label']);