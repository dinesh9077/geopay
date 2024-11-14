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
			$table->unsignedBigInteger('user_id')->index(); 
			$table->string('business_licence')->nullable(); 
			$table->string('tin')->nullable(); 
			$table->string('vat')->nullable(); 
			$table->text('company_address')->nullable();
			$table->string('postcode')->nullable();
			$table->string('bank_name')->nullable(); 
			$table->string('account_number')->nullable();
			$table->string('bank_code')->nullable();
			$table->integer('step_number')->default(0);
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
