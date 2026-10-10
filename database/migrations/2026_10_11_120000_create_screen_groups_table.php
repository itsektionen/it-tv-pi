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
        Schema::create('screen_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('screen_screen_group', function (Blueprint $table) {
            $table->foreignId('screen_group_id')->constrained()->cascadeOnDelete();
            $table->string('screen_id');
            $table->foreign('screen_id')->references('id')->on('screens')->cascadeOnDelete();
            $table->primary(['screen_group_id', 'screen_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('screen_screen_group');
        Schema::dropIfExists('screen_groups');
    }
};
