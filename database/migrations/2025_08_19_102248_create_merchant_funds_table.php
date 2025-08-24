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
        Schema::create('merchant_funds', function (Blueprint $table) {
            $table->id(); 
            $table->unsignedBigInteger('user_id')->nullable()->index();  
            $table->decimal('amount', 15, 2);
            $table->enum('payment_mode', ['cash', 'bank', 'upi', 'cheque'])->default('cash');
            $table->string('transaction_id')->nullable();
            $table->string('receipt')->nullable();
            $table->date('date');
            $table->text('remarks')->nullable(); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('merchant_funds');
    }
};
