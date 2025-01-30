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
        Schema::create('onafric_channels', function (Blueprint $table) {
			$table->id();
			$table->unsignedBigInteger('country_id')->index();
			$table->string('channel')->nullable();
			$table->decimal('fees', 10, 2)->default(0);
			$table->enum('commission_type', ['flat', 'percentage'])->default('flat');
			$table->decimal('commission_charge', 10, 2)->default(0);
			$table->boolean('status')->default(1);
			$table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('onafric_channels');
    }
};
