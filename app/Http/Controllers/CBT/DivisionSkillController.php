<?php

namespace App\Http\Controllers\CBT;

use App\Http\Controllers\Controller;
use App\Models\Division;
use App\Models\Skill;
use App\Models\DivisionSkill;
use Illuminate\Http\Request;

/**
 * DivisionSkillController
 * 
 * Controller for managing skill assignments per division
 */
class DivisionSkillController extends Controller
{
    /**
     * Display mapping of skills to divisions
     */
    public function index(Request $request)
    {
        $divisions = Division::with('department')->orderBy('name')->get();
        $selectedDivisionId = $request->division_id ?? ($divisions->first()->id ?? null);
        
        $selectedDivision = null;
        $divisionSkills = collect();
        
        if ($selectedDivisionId) {
            $selectedDivision = Division::with('department')->find($selectedDivisionId);
            
            // Get skills already assigned to this division
            $divisionSkills = DivisionSkill::where('division_id', $selectedDivisionId)
                ->with('skill')
                ->get();
        }
        
        return view('cbt.admin.division-skills.index', compact(
            'divisions', 
            'selectedDivisionId', 
            'selectedDivision',
            'divisionSkills'
        ));
    }

    /**
     * Store a new skill for a division
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'division_id' => 'required|exists:divisions,id',
            'skill_name' => 'required|string|min:3|max:255',
        ]);

        // Get the division
        $division = Division::find($validated['division_id']);

        // Create new skill
        $skill = Skill::create([
            'code' => strtoupper(str_replace(' ', '_', substr($validated['skill_name'], 0, 20))),
            'name' => $validated['skill_name'],
            'division_id' => $validated['division_id'],
            'is_active' => true,
        ]);

        // Create the division-skill mapping with default level 1
        DivisionSkill::create([
            'division_id' => $validated['division_id'],
            'skill_id' => $skill->id,
            'required_level' => 1,
            'is_mandatory' => false,
        ]);

        return back()->with('success', "Skill '{$validated['skill_name']}' berhasil ditambahkan ke divisi {$division->name}!");
    }

    /**
     * Update a skill mapping
     */
    public function update(Request $request, DivisionSkill $divisionSkill)
    {
        $validated = $request->validate([
            'skill_name' => 'required|string|min:3|max:255',
            'is_mandatory' => 'boolean',
        ]);

        // Update skill name
        if ($divisionSkill->skill) {
            $divisionSkill->skill->update([
                'name' => $validated['skill_name'],
                'code' => strtoupper(str_replace(' ', '_', substr($validated['skill_name'], 0, 20))),
            ]);
        }

        // Update division skill mapping
        $divisionSkill->update([
            'is_mandatory' => $validated['is_mandatory'] ?? false,
        ]);

        return back()->with('success', 'Skill berhasil diperbarui!');
    }

    /**
     * Remove a skill mapping from a division
     */
    public function destroy(DivisionSkill $divisionSkill)
    {
        $divisionSkill->delete();
        return back()->with('success', 'Skill berhasil dihapus dari divisi!');
    }
}
