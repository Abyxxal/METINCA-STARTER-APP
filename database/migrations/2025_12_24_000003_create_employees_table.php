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
            $table->id();
            $table->string('nik')->unique()->comment('Nomor Induk Karyawan');
            $table->string('name')->comment('Nama Lengkap Karyawan');
            $table->string('email')->unique()->nullable()->comment('Email Karyawan');
            $table->foreignId('department_id')->constrained('departments')->onDelete('restrict')->comment('ID Departemen');
            $table->foreignId('position_id')->constrained('positions')->onDelete('restrict')->comment('ID Jabatan');
            $table->string('phone')->nullable()->comment('Nomor Telepon');
            $table->text('address')->nullable()->comment('Alamat');
            $table->string('photo_path')->nullable()->comment('Path ke Foto Karyawan');
            $table->date('hire_date')->nullable()->comment('Tanggal Masuk Kerja');
            $table->enum('status', ['active', 'inactive', 'resigned'])->default('active')->comment('Status Karyawan');
            $table->timestamps();

            // Indexes untuk performa
            $table->index('nik');
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
