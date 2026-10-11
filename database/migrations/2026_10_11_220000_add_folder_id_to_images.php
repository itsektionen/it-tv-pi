<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('images', 'folder_id')) {
            Schema::table('images', function (Blueprint $table) {
                $table->foreignId('folder_id')->nullable()->after('id')->constrained()->nullOnDelete();
            });

            return;
        }

        if (collect(Schema::getForeignKeys('images'))->contains(fn($key) => $key['columns'] === ['folder_id'])) {
            return;
        }

        Schema::table('images', function (Blueprint $table) {
            $table->foreign('folder_id')->references('id')->on('folders')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('images', function (Blueprint $table) {
            $table->dropConstrainedForeignId('folder_id');
        });
    }
};
