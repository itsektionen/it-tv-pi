<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('permission', ['viewer', 'manager', 'admin'])->default('viewer');
        });

        if (DB::table('users')->where('permission', 'admin')->doesntExist()) {
            DB::table('users')->insert([
                'name' => 'removeme',
                'password' => Hash::make('removeme'),
                'permission' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('users')->where('name', 'removeme')->delete();

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('permission');
        });
    }
};
