<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('screens', function (Blueprint $table) {
            $table->foreignId('slideshow_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamp('slideshow_started_at')->nullable();
        });

        DB::table('screens')->update([
            'slideshow_id' => DB::table('slideshows')->where('active', true)->value('id'),
            'slideshow_started_at' => now(),
        ]);

        Schema::table('slideshows', function (Blueprint $table) {
            $table->dropColumn('active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('slideshows', function (Blueprint $table) {
            $table->boolean('active')->default(false);
        });

        Schema::table('screens', function (Blueprint $table) {
            $table->dropConstrainedForeignId('slideshow_id');
            $table->dropColumn('slideshow_started_at');
        });
    }
};
