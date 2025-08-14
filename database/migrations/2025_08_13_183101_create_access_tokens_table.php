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
        Schema::create('access_tokens', function (Blueprint $t) {
            $t->id()->startingValue(1000);
            $t->foreignId('user_id')->index();
            $t->string('name')->nullable();
            $t->string('token_hash', 64)->index(); // sha256(secret)
            $t->timestamp('expires_at')->nullable()->index();
            $t->timestamp('last_used_at')->nullable();
            $t->timestamp('revoked_at')->nullable();
            $t->string('ip', 45)->nullable();
            $t->string('ua', 255)->nullable();
            $t->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('access_tokens');
    }
};
