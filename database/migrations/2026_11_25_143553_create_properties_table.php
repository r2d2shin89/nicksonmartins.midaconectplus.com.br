<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('purpose', 20);
            $table->string('type', 100);
            $table->decimal('price', 12, 2);
            $table->decimal('condo_fee', 12, 2)->default(0);
            $table->decimal('iptu', 12, 2)->default(0);
            $table->decimal('area', 12, 2);
            $table->decimal('lot_area', 12, 2)->nullable();
            $table->unsignedInteger('bedrooms')->default(0);
            $table->unsignedInteger('bathrooms')->default(0);
            $table->unsignedInteger('parking')->default(0);
            $table->unsignedInteger('suite')->default(0);
            $table->string('address');
            $table->string('neighborhood')->nullable();
            $table->string('city');
            $table->string('state', 2);
            $table->string('zip_code', 20)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->text('description');
            $table->json('features');
            $table->json('photos');
            $table->boolean('featured')->default(false);
            $table->string('status')->default('disponivel');
            $table->timestamps();

            $table->index('created_at');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
