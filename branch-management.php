<?php
const BRANCH_NAME = 'branch-management';

const BRANCH_VERSION = '2.0.0';

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
include_once 'includes/Status.php';

require_once 'includes/branch-active.php';

require_once 'includes/branch-model.php';

if(Admin::is()) {

    include_once 'update/update.php';

    include_once 'includes/branch-roles.php';

    include_once 'includes/branch-ajax.php';

    include_once 'admin/admin.php';
}