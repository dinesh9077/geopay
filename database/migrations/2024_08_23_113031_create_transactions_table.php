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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id(); // Auto-incrementing ID
			$table->unsignedBigInteger('user_id')->index(); // Index for user_id, assuming it references the users table
			$table->unsignedBigInteger('receiver_id')->nullable()->index(); // Index for receiver_id, nullable in case it's not applicable
			$table->string('platform_name')->index(); // Index for platform_name, typically used in queries
			$table->string('platform_provider')->index(); // Index for platform_provider
			$table->string('transaction_type')->index()->comment('credit, debit'); // Index and comment for type of transaction
			$table->unsignedBigInteger('country_id')->nullable()->index(); // Index for country_id, nullable for optional countries
			$table->decimal('txn_amount', 18, 4); // Decimal with 18 digits and 4 decimal places for monetary values
			$table->string('txn_status')->default('pending')->index()->comment('pending, process, success'); // Default value and index for transaction status
			$table->longText('comments')->nullable(); // Nullable text for comments
			$table->longText('notes')->nullable(); // Nullable text for any additional notes
			$table->timestamps(); // Auto-created created_at and updated_at columns
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
