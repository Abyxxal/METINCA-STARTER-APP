<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->string('question_set_id', 50)->nullable()->after('id')->comment('Grouping ID for question sets');
            $table->string('set_title')->nullable()->after('question_set_id')->comment('Title of the question set');
            
            $table->index('question_set_id');
        });

        // Group existing questions by skill_id and for_level, then assign same question_set_id
        $existingQuestions = DB::table('questions')
            ->select('skill_id', 'for_level')
            ->groupBy('skill_id', 'for_level')
            ->get();

        foreach ($existingQuestions as $group) {
            $questionSetId = 'QS-' . date('Ymd') . '-' . str_pad($group->skill_id, 3, '0', STR_PAD_LEFT) . '-L' . $group->for_level;
            
            // Get skill name for set title
            $skill = DB::table('skills')->where('id', $group->skill_id)->first();
            $setTitle = ($skill ? $skill->name : 'Unknown Skill') . ' - Level ' . $group->for_level;
            
            // Update all questions in this group
            DB::table('questions')
                ->where('skill_id', $group->skill_id)
                ->where('for_level', $group->for_level)
                ->update([
                    'question_set_id' => $questionSetId,
                    'set_title' => $setTitle,
                    'updated_at' => now()
                ]);
        }
    }

    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropIndex(['question_set_id']);
            $table->dropColumn(['question_set_id', 'set_title']);
        });
    }
};
