<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('skills', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('division_id')->nullable();
            $table->string('code')->comment('Kode Skill (CMM, PT, MPL, RT, UT, US, DIM)');
            $table->string('name')->comment('Nama Skill');
            $table->string('category')->default('Technical')->comment('Skill category: Technical, Soft Skill, etc.');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('division_id')->references('id')->on('divisions')->onDelete('set null');
            $table->index('division_id');
            $table->unique(['code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('skills');
    }
};
