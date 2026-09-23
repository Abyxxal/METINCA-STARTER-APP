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
        Schema::create('exam_periods', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('division_id')->nullable()->constrained()->nullOnDelete()
                ->comment('NULL = periode berlaku untuk semua divisi');
            $table->timestamp('start_at')
                ->comment('Tanggal & jam ujian mulai dibuka (ditetapkan Manager).');
            $table->timestamp('end_at')
                ->comment('Tanggal & jam batas akhir ujian (ditetapkan Manager).');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_periods');
    }
};