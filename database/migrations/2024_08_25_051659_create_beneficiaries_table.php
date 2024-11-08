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
        Schema::create('beneficiaries', function (Blueprint $table) {
            $table->id();
            $table->string('user_id', 255);
            $table->unsignedTinyInteger('type')->default(1); // 1 or 2, default 1
            $table->string('country_id', 255);
            $table->text('bank_name');
            $table->text('account_number');
            $table->text('b_first_name');
            $table->text('b_middle_name')->nullable(); // default null
            $table->text('b_last_name');
            $table->text('b_address');
            $table->string('b_state', 255)->nullable(); // default null
            $table->text('b_mobile');
            $table->text('b_email');
            $table->unsignedTinyInteger('relations')->default(1); // 1, 2, or 3, default 1
            $table->text('other_remarks')->nullable(); // default null
            $table->text('remittance_purpose');
            $table->text('beneficiary_id');
            $table->text('receiver_id_expiry');
            $table->text('receiver_dob');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('beneficiaries');
    }
};
