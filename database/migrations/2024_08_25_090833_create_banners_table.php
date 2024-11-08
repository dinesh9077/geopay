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
        Schema::create('banners', function (Blueprint $table) {
            $table->id(); // auto-incrementing primary key
            $table->string('name', 255)->nullable(); // banner name field with varchar(255)
            $table->text('app_image'); // banner image field for app
            $table->text('web_image'); // banner image field for app
            $table->text('description')->nullable(); // banner text field with text
            $table->timestamps(); // created_at and updated_at timestamps
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('banners');
    }
};
