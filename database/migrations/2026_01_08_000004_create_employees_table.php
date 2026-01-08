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
            $table->string('nik')->unique();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            
            // Foreign keys to the hierarchy
            $table->foreignId('department_id')
                ->constrained()
                ->onDelete('restrict');
            $table->foreignId('division_id')
                ->constrained()
                ->onDelete('restrict');
            $table->foreignId('position_id')
                ->constrained()
                ->onDelete('restrict');
            
            // Employee status
            $table->enum('status', ['Aktif', 'Non-Aktif', 'Cuti'])->default('Aktif');
            
            // Additional employee info
            $table->date('join_date')->nullable();
            $table->timestamps();
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
