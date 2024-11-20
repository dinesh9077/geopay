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
        Schema::create('exchange_rates', function (Blueprint $table) {
			$table->id();  
			$table->integer('admin_id');  
			$table->unsignedTinyInteger('type')->index()->comment('1 - Add Service, 2 - Pay Service');  
			$table->string('currency', 3)->index(); // Limit currency to 3 characters (ISO 4217 standard) and add index
			$table->decimal('exchange_rate', 15, 8);
			$table->timestamps(); // Created and updated timestamps
		}); 
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exchange_rates');
    }
};
