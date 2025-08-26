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
        Schema::create('merchant_transaction_limits', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();  
            $table->string('service')->index();  
            $table->decimal('daily_limit', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('merchant_transaction_limits');
    }
};
