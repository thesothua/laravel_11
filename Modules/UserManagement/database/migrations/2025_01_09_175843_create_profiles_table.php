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
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Link to the user
            $table->string('first_name')->nullable(); // Phone number
            $table->string('last_name')->nullable(); // Phone number
            $table->string('phone_number')->nullable(); // Phone number
            $table->date('date_of_birth')->nullable(); // Date of birth
            $table->string('profile_image')->nullable(); // Profile image URL or path

            // Social Media Links
            $table->string('facebook_url')->nullable(); // Facebook profile
            $table->string('twitter_url')->nullable(); // Twitter profile
            $table->string('linkedin_url')->nullable(); // LinkedIn profile
            $table->string('instagram_url')->nullable(); // Instagram profile

            // Professional Information
            $table->string('job_title')->nullable(); // User's job title
            $table->string('company')->nullable(); // User's company
            $table->text('bio')->nullable(); // Short bio or description

            // Preferences
            $table->boolean('newsletter_subscription')->default(false); // Whether subscribed to the newsletter
            $table->string('preferred_language')->default('en'); // User's preferred language

            // Other Information
            $table->string('gender')->nullable(); // Gender (e.g., Male, Female, Other)

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
