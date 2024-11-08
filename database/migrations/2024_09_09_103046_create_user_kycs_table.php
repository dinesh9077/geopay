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
        Schema::create('user_kycs', function (Blueprint $table) {
			$table->id();
			$table->unsignedBigInteger('user_id')->index();
			$table->string('email')->index();
			$table->text('video')->nullable();
			$table->json('document')->nullable();
			$table->string('verification_status')->nullable();
			$table->string('identification_id')->nullable();
			$table->string('verification_id')->nullable();
			$table->json('meta_response')->nullable(); // Store the raw response if needed
			$table->timestamps(); 
			$table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
		});

    }
	
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_kycs');
    }
};
