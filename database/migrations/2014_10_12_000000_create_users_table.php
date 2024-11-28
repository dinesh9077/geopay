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
		Schema::create('users', function (Blueprint $table) {
			$table->id();
			$table->unsignedBigInteger('user_role_id')->default(1)->index();
			$table->string('first_name');
			$table->string('last_name');
			$table->string('email')->unique();
			$table->string('password');
			$table->unsignedBigInteger('country_id')->index(); // Changed to unsignedBigInteger for foreign key reference
			$table->string('mobile_number');
			$table->string('formatted_number');
			$table->string('referalcode')->nullable();
			$table->text('fcm_token')->nullable();
			$table->boolean('is_company')->default(0)->index();
			$table->string('verification_token')->nullable();
			$table->boolean('is_email_verify')->default(0)->comment('0,1');
			$table->boolean('is_mobile_verify')->default(0)->comment('0,1');
			$table->boolean('is_kyc_verify')->default(0)->comment('0,1');
			$table->boolean('status')->default(1)->index();
			$table->string('role')->index();
			$table->decimal('balance', 25, 10)->default(0);
			$table->rememberToken();
			$table->string('profile_image')->nullable();
			$table->integer('terms')->default(1);
			$table->string('xps')->nullable();
			$table->string('company_name')->nullable();
			$table->integer('user_limit_id')->default(1); 
			$table->softDeletes();
			$table->timestamps();
		}); 
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
