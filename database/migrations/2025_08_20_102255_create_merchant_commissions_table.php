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
        Schema::create('merchant_commissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // Reference to merchants table
            $table->string('service'); // e.g. shipping, cod, insurance
            $table->enum('charge_type', ['flat', 'percentage']);
            $table->decimal('charge_value', 10, 2); // Amount or % value
            $table->boolean('status')->default(1); // Active/Inactive
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('merchant_commissions');
    }
};
