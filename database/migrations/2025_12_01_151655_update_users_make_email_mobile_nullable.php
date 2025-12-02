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
        Schema::table('users', function (Blueprint $table) {
            //$table->string('email')->nullable()->change();
            $table->string('mobile_number')->nullable()->change();
            $table->string('formatted_number')->nullable()->change();
            $table->string('country_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //$table->string('email')->nullable(false)->change();
            $table->string('mobile_number')->nullable(false)->change();
            $table->string('formatted_number')->nullable(false)->change();
            $table->string('country_id')->nullable(false)->change();
        });
    }
};
