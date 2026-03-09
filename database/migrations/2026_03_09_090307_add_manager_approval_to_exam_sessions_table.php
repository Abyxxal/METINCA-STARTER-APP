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
        Schema::table('exam_sessions', function (Blueprint $table) {
            $table->enum('manager_decision', ['pending', 'approved', 'rejected'])->nullable()->after('admin_notes');
            $table->text('manager_notes')->nullable()->after('manager_decision');
            $table->unsignedBigInteger('decided_by')->nullable()->after('manager_notes');
            $table->timestamp('decided_at')->nullable()->after('decided_by');

            $table->foreign('decided_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exam_sessions', function (Blueprint $table) {
            $table->dropForeign(['decided_by']);
            $table->dropColumn(['manager_decision', 'manager_notes', 'decided_by', 'decided_at']);
        });
    }
};
