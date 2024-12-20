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
        Schema::create('live_exchange_rates', function (Blueprint $table) {
			$table->id();
			$table->string('channel')->index();
			$table->string('currency')->index();
			$table->decimal('markdown_rate', 15, 6)->default(0); 
			$table->decimal('aggregator_rate', 15, 6)->default(0); 
			$table->string('markdown_type')->default('flat'); 
			$table->decimal('markdown_charge', 15, 6)->default(0); 
			$table->integer('status')->default(1); 
			$table->timestamps();
		});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('live_exchange_rates');
    }
};
