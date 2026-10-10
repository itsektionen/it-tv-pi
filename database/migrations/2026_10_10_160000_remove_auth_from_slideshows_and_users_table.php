<?php

use App\Models\User;
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
        Schema::table('slideshows', function (Blueprint $table) {
            $table->dropConstrainedForeignIdFor(User::class);
        });

        DB::table('users')->where('name', 'removeme')->delete();

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('permission');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('permission', ['viewer', 'manager', 'admin'])->default('viewer');
        });

        $userId = DB::table('users')->insertGetId([
            'name' => 'removeme',
            'password' => Hash::make('removeme'),
            'permission' => 'admin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Schema::table('slideshows', function (Blueprint $table) use ($userId) {
            $table->foreignIdFor(User::class)->default($userId)->constrained();
        });
    }
};
