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
        // Optimize users table for role-based queries
        Schema::table('users', function (Blueprint $table) {
            // Index for role-based queries (common in your middleware)
            if (!Schema::hasIndex('users', 'users_role_index')) {
                $table->index('role');
            }
            
            // Index for manager_id (foreign key and relationship queries)
            if (!Schema::hasIndex('users', 'users_manager_id_index')) {
                $table->index('manager_id');
            }
            
            // Composite index for manager queries (manager viewing their employees)
            if (!Schema::hasIndex('users', 'users_manager_id_role_index')) {
                $table->index(['manager_id', 'role']);
            }
        });

        // Optimize attendance_records table for common query patterns
        Schema::table('attendance_records', function (Blueprint $table) {
            // Single column index for user_id (common in user-specific queries)
            if (!Schema::hasIndex('attendance_records', 'attendance_records_user_id_index')) {
                $table->index('user_id');
            }
            
            // Index for type (check_in/check_out) queries
            if (!Schema::hasIndex('attendance_records', 'attendance_records_type_index')) {
                $table->index('type');
            }
            
            // Composite index for user's attendance history
            if (!Schema::hasIndex('attendance_records', 'attendance_records_user_id_type_index')) {
                $table->index(['user_id', 'type']);
            }
            
            // Composite index for date-range queries across all users
            if (!Schema::hasIndex('attendance_records', 'attendance_records_recorded_at_type_index')) {
                $table->index(['recorded_at', 'type']);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove users table indexes (only if they exist)
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndexIfExists('users_role_index');
            $table->dropIndexIfExists('users_manager_id_index');
            $table->dropIndexIfExists('users_manager_id_role_index');
        });

        // Remove attendance_records table indexes (only if they exist)
        Schema::table('attendance_records', function (Blueprint $table) {
            $table->dropIndexIfExists('attendance_records_user_id_index');
            $table->dropIndexIfExists('attendance_records_type_index');
            $table->dropIndexIfExists('attendance_records_user_id_type_index');
            $table->dropIndexIfExists('attendance_records_recorded_at_type_index');
        });
    }
};