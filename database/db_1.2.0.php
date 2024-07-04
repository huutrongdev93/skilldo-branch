<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class () extends Migration {

    public function up(): void
    {
        if(schema()->hasTable('branchs')) {
            schema()->table('branchs', function (Blueprint $table) {
                $table->dateTime('created')->default('CURRENT_TIMESTAMP')->change();
            });
        }
    }

    public function down(): void
    {
    }
};