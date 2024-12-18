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
        Schema::create('lightnet_catalogues', function (Blueprint $table) {
            $table->id();
            $table->string('category_name')->index();
            $table->string('service_name')->index(); 
            $table->string('catalogue_type')->index(); 
            $table->string('catalogue_description')->nullable(); 
            $table->json('data'); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lightnet_catalogues');
    }
};
