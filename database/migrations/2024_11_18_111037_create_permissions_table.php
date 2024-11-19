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
        Schema::create('permissions', function (Blueprint $table) {
			$table->id();
			$table->string('name')->nullable();
			$table->string('heading')->nullable(); 
			$table->integer('position')->default(0); 
			$table->integer('heading_position')->default(1); 
			$table->integer('status')->default(1); 
			$table->timestamps();
		}); 
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
};
