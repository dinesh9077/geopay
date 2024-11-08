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
        Schema::create('otps', function (Blueprint $table) {
			$table->id(); // auto-incrementing primary key
			$table->string('email_mobile', 100)->index(); // Email field with string (100) and index for faster searches
			$table->string('otp', 6); // Shortened OTP field, assuming a 6-character OTP (adjust as needed)
			$table->timestamp('expires_at')->nullable(); // Timestamp for OTP expiration
			$table->timestamps(); // created_at and updated_at timestamps
		}); 
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('otps');
    }
};
