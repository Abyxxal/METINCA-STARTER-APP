<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_competency_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_competency_id')
                ->constrained('employee_competencies')
                ->cascadeOnDelete();
            $table->tinyInteger('previous_level')->unsigned()->nullable();
            $table->tinyInteger('new_level')->unsigned();
            $table->enum('change_type', ['up', 'down', 'initial']);
            $table->string('change_source', 50);
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('exam_session_id')->nullable()->constrained('exam_sessions')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamp('created_at')->nullable();

            $table->index(['employee_competency_id', 'created_at'], 'ech_comp_id_created_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_competency_histories');
    }
};
