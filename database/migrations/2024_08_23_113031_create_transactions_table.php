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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('user_id', 255);
            $table->string('receiver_id', 255);
            $table->string('wallet_id', 255);
            $table->string('invoice_id', 255);
            $table->text('transaction_id');
            $table->string('platform_name', 255);
            $table->string('platform_provider', 255);
            $table->string('country_id', 255);
            $table->text('transaction_type');
            $table->string('image', 255)->nullable();
            $table->decimal('previous_amount', 28, 8);
            $table->decimal('current_amount', 28, 8);
            $table->decimal('total_amount', 28, 8);
            $table->decimal('requested_amount', 28, 8);
            $table->decimal('commission_amount', 28, 8);
            $table->string('transaction_status', 255);
            $table->text('remarks');
            $table->text('comments')->nullable();
            $table->text('remarks');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
