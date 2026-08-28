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
        Schema::create('hall_amenities', function (Blueprint $table) {
            $table->id();
            $table->string('amenity_name');
            $table->decimal('extra_cost',10,2)->default(0.00);
            $table->foreignId('halls_id')->constrained('halls')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hall_amenities');
    }
};
