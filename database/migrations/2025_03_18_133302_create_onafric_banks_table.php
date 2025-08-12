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
        Schema::create('onafric_banks', function (Blueprint $table) {
            $table->id();
            $table->string('payout_iso')->nullable()->index();
            $table->string('mfs_bank_code')->nullable();
            $table->string('bank_name')->nullable();
            $table->longText('response')->nullable();
            $table->integer('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('onafric_banks');
    }
};
