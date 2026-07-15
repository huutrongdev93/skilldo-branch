<?php
namespace BranchManagement\Services;

use Illuminate\Database\Schema\Blueprint;

Class DeactivatorService
{
    public static function uninstall(): void
    {
        if(schema()->hasTable('branchs'))
        {
            schema()->drop('branchs');
        }

        if(schema()->hasColumn('order', 'branch_id'))
        {
            schema()->table('order', function (Blueprint $table)
            {
                $table->dropColumn('branch_id');
            });
        }

        if(schema()->hasColumn('user', 'branch_id'))
        {
            schema()->table('user', function (Blueprint $table)
            {
                $table->dropColumn('branch_id');
            });
        }
    }
}