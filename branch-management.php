<?php
/**
Plugin name     : Branch management
Plugin class    : Branch_Management
Plugin uri      : http://sikido.vn
Description     : Ứng dụng quản lý chi nhánh
Author          : Nguyễn Hữu Trọng
Version         : 1.2.0
 */
const BRANCH_NAME = 'branch-management';

define('BRANCH_PATH', Path::plugin(BRANCH_NAME));

class Branch_Management {

    private string $name = 'Branch_Management';

    public function active(): void
    {
        Branch_Activator::activate();
    }

    public function uninstall(): void
    {
        Branch_Deactivation::uninstall();
    }
}

require_once 'includes/branch-active.php';

require_once 'includes/branch-model.php';

if(Admin::is()) {

    include_once 'includes/branch-roles.php';

    include_once 'includes/branch-ajax.php';

    include_once 'includes/branch-order.php';

    include_once 'admin/branch-admin.php';

    new BrandOrderAction();
}