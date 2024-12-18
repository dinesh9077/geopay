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
        Schema::create('lightnet_countries', function (Blueprint $table) {
            $table->id();
            $table->string('data'); 
            $table->string('value'); 
            $table->string('label')->nullable(); 
            $table->string('service_name')->index(); 
            $table->integer('status')->default(1)->index(); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lightnet_countries');
    }
};
