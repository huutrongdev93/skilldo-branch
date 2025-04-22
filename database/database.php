<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Capsule\Manager as DB;

return new class () extends Migration {

    public function up(): void
    {
        if(!schema()->hasTable('branchs')) {
            schema()->create('branchs', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name', 255)->collation('utf8mb4_unicode_ci')->nullable();
                $table->string('phone', 200)->collation('utf8mb4_unicode_ci')->nullable();
                $table->string('email', 200)->collation('utf8mb4_unicode_ci')->nullable();
                $table->string('address', 255)->collation('utf8mb4_unicode_ci')->nullable();
                $table->integer('ward')->default(0);
                $table->integer('district')->default(0);
                $table->integer('city')->default(0);
                $table->string('status', 100)->collation('utf8mb4_unicode_ci')->default('working');
                $table->integer('isDefault')->default(0);
                $table->integer('order')->default(0);
                $table->text('area')->collation('utf8mb4_unicode_ci')->nullable();
                $table->dateTime('created')->default(DB::raw('CURRENT_TIMESTAMP'));
                $table->dateTime('updated')->nullable();
            });
        }

        if(schema()->hasTable('order') && !schema()->hasColumn('order', 'branch_id')) {
            schema()->table('order', function (Blueprint $table) {
                $table->integer('branch_id')->default(0)->after('user_updated');
            });
        }

        if(schema()->hasTable('user') && !schema()->hasColumn('user', 'branch_id')) {
            schema()->table('user', function (Blueprint $table) {
                $table->integer('branch_id')->default(0)->after('status');
            });
        }
    }

    public function down(): void
    {
        if(schema()->hasTable('branchs')) {
            schema()->drop('branchs');
        }
        if(schema()->hasColumn('order', 'branch_id')) {
            schema()->table('order', function (Blueprint $table) {
                $table->dropColumn('branch_id');
            });
        }
        if(schema()->hasColumn('user', 'branch_id')) {
            schema()->table('user', function (Blueprint $table) {
                $table->dropColumn('branch_id');
            });
        }
    }
};