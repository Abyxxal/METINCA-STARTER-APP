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
        Schema::create('employees', function (Blueprint $table) {
            $table->string('nik')->primary()->comment('Nomor Induk Karyawan');
            $table->string('nama_karyawan')->comment('Nama Lengkap Karyawan');
            $table->string('email')->unique()->comment('Email Karyawan');
            $table->string('password')->comment('Password Karyawan');
            $table->foreignId('department_id')->constrained('departments')->onDelete('restrict')->comment('ID Departemen');
            $table->foreignId('position_id')->constrained('positions')->onDelete('restrict')->comment('ID Jabatan');
            $table->enum('status', ['active', 'inactive', 'resigned'])->default('active')->comment('Status Karyawan');
            $table->timestamps();

            // Indexes untuk performa
            $table->index('department_id');
            $table->index('position_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
