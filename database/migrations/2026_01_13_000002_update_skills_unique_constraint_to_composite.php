<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('skills', function (Blueprint $table) {
            // Drop old unique constraint on 'code' only
            $table->dropUnique(['code']);
            
            // Add new composite unique constraint on 'division_id' + 'code'
            $table->unique(['division_id', 'code'], 'skills_division_code_unique');
        });
    }

    public function down(): void
    {
        Schema::table('skills', function (Blueprint $table) {
            // Drop composite unique constraint
            $table->dropUnique('skills_division_code_unique');
            
            // Restore old unique constraint on 'code' only
            $table->unique(['code']);
        });
    }
};
