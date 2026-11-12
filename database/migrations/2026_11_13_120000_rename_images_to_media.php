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
        Schema::rename('images', 'media');

        Schema::table('media', function (Blueprint $table) {
            $table->string('type', 16)->default('image')->after('name');
            $table->float('duration')->nullable()->after('preview_path');
        });

        Schema::table('slideshow_items', function (Blueprint $table) {
            $table->integer('duration')->nullable()->change();
        });

        $this->renameSlideType('image', 'media');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('slideshow_items')->whereNull('duration')->update(['duration' => 5]);

        Schema::table('slideshow_items', function (Blueprint $table) {
            $table->integer('duration')->nullable(false)->change();
        });

        $this->renameSlideType('media', 'image');

        Schema::table('media', function (Blueprint $table) {
            $table->dropColumn(['type', 'duration']);
        });

        Schema::rename('media', 'images');
    }

    private function renameSlideType(string $from, string $to): void
    {
        DB::table('slideshow_items')->lazyById()->each(function ($item) use ($from, $to) {
            $data = json_decode($item->data ?? '[]', true) ?? [];

            if (array_key_exists($from, $data)) {
                $data[$to] = $data[$from];
                unset($data[$from]);
            }

            DB::table('slideshow_items')->where('id', $item->id)->update([
                'type' => $item->type === $from ? $to : $item->type,
                'data' => json_encode($data),
            ]);
        });
    }
};
