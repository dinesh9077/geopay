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
        Schema::table('merchant_corridors', function (Blueprint $table) {
            $table->enum('fee_type', ['flat', 'percentage'])->default('flat');
            $table->decimal('fee_value', 10, 2)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('merchant_corridors', function (Blueprint $table) {
            //
        });
    }
};
