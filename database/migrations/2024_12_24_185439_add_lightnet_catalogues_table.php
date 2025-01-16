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
        Schema::table('lightnet_catalogues', function (Blueprint $table) {
			$table->string('additionalField1')->nullable()->index();  // Indexed column
			$table->string('additionalField2')->nullable();  // Non-indexed nullable column
			$table->string('additionalField3')->nullable();  // Non-indexed nullable column
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lightnet_catalogues', function (Blueprint $table) {
            //
        });
    }
};
