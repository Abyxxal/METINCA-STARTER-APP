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
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique()->comment('Nama Departemen');
            $table->text('description')->nullable()->comment('Deskripsi Departemen');
            $table->integer('employee_count')->default(0)->comment('Jumlah Karyawan');
            $table->enum('status', ['active', 'inactive'])->default('active')->comment('Status Departemen');
            $table->timestamps();

            // Index untuk performa query
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('departments');
    }
};
