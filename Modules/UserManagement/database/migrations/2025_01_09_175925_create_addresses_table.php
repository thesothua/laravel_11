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
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Link to the user
            $table->string('address_line_1'); // First line of the address
            $table->string('address_line_2')->nullable(); // Second line (optional)
            $table->string('city'); // City
            $table->string('state'); // State or region
            $table->string('country'); // Country
            $table->string('postal_code'); // Postal or ZIP code
            $table->boolean('is_default')->default(false); // Mark if it's the default address
            $table->enum('type', ['home', 'work'])->default('home'); // Type of address
            $table->timestamps(); // created_at, updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
