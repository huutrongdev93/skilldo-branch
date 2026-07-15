<?php

use SkillDo\Support\Path;

class BranchManagement
{
    public function active(): void
    {
        include_once Path::plugin('branch-management/app/Services/ActivatorService.php');

        \BranchManagement\Services\ActivatorService::activate();
    }

    public function uninstall(): void
    {
        include_once Path::plugin('branch-management/app/Services/DeactivatorService.php');

        \BranchManagement\Services\DeactivatorService::uninstall();
    }
}