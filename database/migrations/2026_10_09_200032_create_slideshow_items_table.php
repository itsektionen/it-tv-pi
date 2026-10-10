<?php

use App\Models\Slideshow;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('slideshow_items', function (Blueprint $table) {
            $table->id();
            $table->string("type", 16);
            $table->integer("duration");
            $table->json("data")->nullable();
            $table->foreignIdFor(Slideshow::class);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('slideshow_items');
    }
};
