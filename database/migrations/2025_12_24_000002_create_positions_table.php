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
        Schema::create('positions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Nama Jabatan/Posisi');
            $table->foreignId('department_id')->constrained('departments')->onDelete('cascade')->comment('ID Departemen');
            $table->integer('level')->default(1)->comment('Level Kompetensi (1-4)');
            $table->text('description')->nullable()->comment('Deskripsi Jabatan');
            $table->enum('status', ['active', 'inactive'])->default('active')->comment('Status Jabatan');
            $table->timestamps();

            // Unique constraint: satu posisi per department (optional, bisa duplikat)
            // $table->unique(['name', 'department_id']);

            // Indexes untuk performa
            $table->index('department_id');
            $table->index('level');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('positions');
    }
};
