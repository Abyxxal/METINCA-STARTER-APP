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
        Schema::create('employee_competencies', function (Blueprint $table) {
            $table->id();
            $table->string('nik'); // Foreign key to employees table
            $table->foreign('nik')->references('nik')->on('employees')->onDelete('cascade');
            $table->integer('level')->default(1)->comment('Competency level 1-4'); // 1=Perlu Training, 2=Mandiri, 3=Supervisor, 4=Expert
            $table->timestamp('updated_at');
            $table->timestamp('created_at');
            
            // Composite unique key to prevent duplicate competencies
            $table->unique(['nik']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_competencies');
    }
};
