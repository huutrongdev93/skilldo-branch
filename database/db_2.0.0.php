<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class () extends Migration {

    public function up(): void
    {
        if(schema()->hasColumn('branchs', 'default')) {
            schema()->table('branchs', function (Blueprint $table) {
                $table->renameColumn('default', 'isDefault');
            });
        }
    }

    public function down(): void
    {
    }
};