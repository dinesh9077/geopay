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
		Schema::table('admins', function (Blueprint $table) {
			$table->date('dob')->nullable(); // Corrected nullable()
			$table->string('office_mobile')->nullable(); // Corrected nullable()
			$table->integer('role_id')->index(); // Index on the role_id
			$table->string('role')->nullable(); // Corrected nullable()
			$table->integer('status')->index()->default(1); // Default value for status
			$table->string('xps')->nullable(); // Default value for status
			$table->integer('assign_by')->default(1); // Default value for status
		});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            //
        });
    }
};
