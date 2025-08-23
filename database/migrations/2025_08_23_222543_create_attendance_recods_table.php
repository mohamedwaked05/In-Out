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
        Schema::create('attendance_records', function (Blueprint $table) {
            $table->id();
             $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['check_in', 'check_out']);
            $table->timestamp('recorded_at');
            $table->string('photo_path'); // Stores the path to the image
            $table->timestamps(); // Creates `created_at` and `updated_at`

            // Index for faster queries on user_id and recorded_at
            $table->index(['user_id', 'recorded_at']);
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_recods');
    }
};
