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
        Schema::table('live_exchange_rates', function (Blueprint $table) {
            $table->decimal('api_markdown_rate', 15, 6)->default(0); 
            $table->string('api_markdown_type')->default('flat');
            $table->decimal('api_markdown_charge', 15, 6)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('live_exchange_rates', function (Blueprint $table) {
            //
        });
    }
};
