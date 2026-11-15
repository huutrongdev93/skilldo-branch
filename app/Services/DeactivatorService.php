<?php
namespace BranchManagement\Services;

use Illuminate\Database\Schema\Blueprint;

Class DeactivatorService
{
    public static function uninstall(): void
    {
        if(schema()->hasTable('branchs'))
        {
            schema()->dropIfExists('branchs');
        }

        if(schema()->hasColumn('order', 'branch_id'))
        {
            schema()->table('order', function (Blueprint $table)
            {
                $table->dropColumn('branch_id');
            });
        }

        if(schema()->hasTable('users') && schema()->hasColumn('users', 'branch_id'))
        {
            schema()->table('users', function (Blueprint $table)
            {
                $table->dropColumn('branch_id');
            });
        }
    }
}