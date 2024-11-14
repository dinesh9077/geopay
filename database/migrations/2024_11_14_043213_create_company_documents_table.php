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
        Schema::create('company_documents', function (Blueprint $table) {
			$table->id();
			$table->foreignId('company_details_id')->constrained('company_details')->onDelete('cascade');
			$table->string('text')->nullable();
			$table->string('document')->nullable();
			$table->integer('status')->default(0)->comment('0 - pending, 1 - approved, 2 - rejected');
			$table->longText('reason')->nullable();
			$table->timestamps();
		});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_documents');
    }
};
