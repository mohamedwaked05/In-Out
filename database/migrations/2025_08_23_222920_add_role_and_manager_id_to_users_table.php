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
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['manager', 'employee'])->default('employee');
            $table->foreignId('manager_id')->nullable()->constrained('users')->cascadeOnDelete();
    });
}

// Always define the 'down' method to reverse the migration
    public function down(): void
{
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['manager_id']);
            $table->dropColumn(['role', 'manager_id']);
    });
}
};
