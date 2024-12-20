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
        Schema::table('lightnet_countries', function (Blueprint $table) {
            $table->string('markdown_type')->default('flat');
            $table->decimal('markdown_charge', 25, 2)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lightnet_countries', function (Blueprint $table) {
            //
        });
    }
};
