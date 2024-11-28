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
        Schema::create('admins', function (Blueprint $table) {
            $table->id();
			$table->string('name');
			$table->string('mobile')->nullable();
			$table->string('email')->unique();
			$table->timestamp('email_verified_at')->nullable();
			$table->string('password');
			$table->string('profile')->nullable();
			$table->date('dob')->nullable(); // Corrected nullable()
			$table->string('office_mobile')->nullable(); // Corrected nullable()
			$table->integer('role_id')->index(); // Index on the role_id
			$table->string('role')->nullable(); // Corrected nullable()
			$table->integer('status')->index()->default(1); // Default value for status
			$table->string('xps')->nullable(); // Default value for status
			$table->integer('assign_by')->default(1); // Default value for status
			$table->rememberToken();
			$table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admins');
    }
};
