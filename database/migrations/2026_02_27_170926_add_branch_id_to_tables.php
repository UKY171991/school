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
        $tables = [
            'users',
            'students',
            'teachers',
            'grades',
            'sections',
            'subjects',
            'attendances',
            'exams',
            'marks',
            'fee_structures',
            'fee_payments',
            'books',
            'book_issues',
            'transport_routes',
            'vehicles',
            'drivers',
            'hostels',
            'hostel_rooms',
            'hostel_allocations',
            'homework',
            'syllabi',
            'teacher_timetables',
            'exam_timetables',
            'salaries',
            'staff_leaves',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                Schema::table($table, function (Blueprint $table) {
                    if (!Schema::hasColumn($table->getTable(), 'branch_id')) {
                        $table->foreignId('branch_id')->nullable()->after('school_id')->constrained()->onDelete('set null');
                    }
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = [
            'users',
            'students',
            'teachers',
            'grades',
            'sections',
            'subjects',
            'attendances',
            'exams',
            'marks',
            'fee_structures',
            'fee_payments',
            'books',
            'book_issues',
            'transport_routes',
            'vehicles',
            'drivers',
            'hostels',
            'hostel_rooms',
            'hostel_allocations',
            'homework',
            'syllabi',
            'teacher_timetables',
            'exam_timetables',
            'salaries',
            'staff_leaves',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                Schema::table($table, function (Blueprint $table) {
                    if (Schema::hasColumn($table->getTable(), 'branch_id')) {
                        $table->dropForeign(['branch_id']);
                        $table->dropColumn('branch_id');
                    }
                });
            }
        }
    }
};
