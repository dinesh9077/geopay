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
		Schema::create('company_details', function (Blueprint $table) {
			$table->id();
			$table->unsignedBigInteger('user_id')->index(); // Indexing user_id for performance
			$table->string('company_name');
			$table->string('business_licence'); // Changed to string for shorter length
			$table->string('tin'); // Changed to string for shorter length
			$table->string('vat'); // Changed to string for shorter length
			$table->text('company_address');
			$table->string('postcode'); // Changed to string for shorter length
			$table->string('bank_name'); // Changed to string for shorter length
			$table->string('account_number'); // Changed to string for shorter length
			$table->string('bank_code'); // Changed to string for shorter length
			$table->timestamps();

			// Add foreign key constraint
			$table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
		});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_details');
    }
};
