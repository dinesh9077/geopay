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
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('unique_identifier')->nullable();
            $table->string('country_code')->nullable();
            $table->string('product_name')->nullable();
            $table->integer('operator_id')->nullable();
            $table->integer('product_id')->nullable();
            $table->string('mobile_number')->nullable(); 
            $table->string('unit_currency')->nullable();
            $table->decimal('unit_amount',25,4)->nullable();
            $table->decimal('rates',25,4)->nullable();
            $table->string('unit_convert_currency')->nullable();
            $table->decimal('unit_convert_amount',25,4)->nullable();
            $table->decimal('unit_convert_exchange',25,4)->nullable();
			$table->json('api_request')->nullable();
			$table->json('api_response')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) { 
        });
    }
};
