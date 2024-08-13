<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class () extends Migration {

    public function up(): void
    {
        if(schema()->hasTable('branchs')) {
            schema()->table('branchs', function (Blueprint $table) {
                $table->integer('ward')->default(0)->change();
                $table->integer('district')->default(0)->change();
                $table->integer('city')->default(0)->change();
            });
        }
    }

    public function down(): void
    {
        if(schema()->hasTable('branchs')) {
            schema()->table('branchs', function (Blueprint $table) {
                $table->string('ward', 100)->nullable()->change();
                $table->string('district', 100)->nullable()->change();
                $table->string('city', 100)->nullable()->change();
            });
        }
    }
};