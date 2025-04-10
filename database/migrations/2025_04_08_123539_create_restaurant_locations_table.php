<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('restaurant_locations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('image');
            $table->text('address');
            $table->json('opening_hours'); // Store as JSON
            $table->string('phone');
            $table->string('email')->nullable();
            $table->boolean('delivery_available')->default(false);
            $table->string('delivery_areas')->nullable();
            $table->boolean('is_open')->default(true);
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('restaurant_locations');
    }
};
