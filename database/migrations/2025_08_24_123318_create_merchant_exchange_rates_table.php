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
        Schema::create('merchant_exchange_rates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->enum('type', ['live', 'manual'])
                ->nullable()->index();
            $table->unsignedBigInteger('php')->index(); 
            $table->string('markdown_type')->default('flat');
            $table->decimal('markdown_charge', 15, 6)->default(0);
            $table->decimal('markdown_rate', 15, 6)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('merchant_exchange_rates');
    }
};
