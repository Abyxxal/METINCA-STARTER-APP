<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Skill;
use App\Models\Division;
use App\Models\DivisionSkill;

/**
 * CBTSkillSeeder
 * 
 * Seeds skills for CBT system and maps them to divisions.
 * Creates 4 core manufacturing skills:
 * - CMM Operation (for Quality Control)
 * - Hand Tools / Caliper (for Quality Control)
 * - Inventory Management (for Gudang)
 * - Negotiation (for Sales)
 */
class CBTSkillSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Seeding CBT Skills...');

        // Get divisions
        $qualityControl = Division::where('name', 'Quality Control')->first();
        $gudang = Division::where('name', 'Gudang')->first();
        $sales = Division::where('name', 'Sales')->first();

        // ============================================
        // CREATE SKILLS
        // ============================================

        // CMM Operation Skill
        $cmmSkill = Skill::updateOrCreate(
            ['code' => 'SKILL-CMM'],
            [
                'division_id' => $qualityControl?->id,
                'name' => 'CMM Operation',
                'category' => 'Technical',
                'description' => 'Coordinate Measuring Machine operation, calibration, and maintenance. Includes alignment procedures, probe calibration, and temperature control.',
                'is_active' => true,
            ]
        );

        // Hand Tools / Caliper Skill
        $caliperSkill = Skill::updateOrCreate(
            ['code' => 'SKILL-CALIPER'],
            [
                'division_id' => $qualityControl?->id,
                'name' => 'Hand Tools / Caliper',
                'category' => 'Technical',
                'description' => 'Precision measurement using hand tools, calipers, micrometers, and gauges. Includes reading measurements and quality inspection.',
                'is_active' => true,
            ]
        );

        // Inventory Management Skill
        $inventorySkill = Skill::updateOrCreate(
            ['code' => 'SKILL-INV'],
            [
                'division_id' => $gudang?->id,
                'name' => 'Inventory Management',
                'category' => 'Technical',
                'description' => 'Warehouse and inventory management skills. Includes stock counting, FIFO/LIFO, storage management, and inventory software.',
                'is_active' => true,
            ]
        );

        // Negotiation Skill
        $negoSkill = Skill::updateOrCreate(
            ['code' => 'SKILL-NEGO'],
            [
                'division_id' => $sales?->id,
                'name' => 'Negotiation',
                'category' => 'Soft Skill',
                'description' => 'Client negotiation and communication skills. Includes pricing negotiation, contract handling, and customer relations.',
                'is_active' => true,
            ]
        );

        // ============================================
        // MAP SKILLS TO DIVISIONS
        // ============================================

        // Quality Control Division Skills
        if ($qualityControl) {
            DivisionSkill::updateOrCreate(
                ['division_id' => $qualityControl->id, 'skill_id' => $cmmSkill->id],
                [
                    'required_level' => 2,
                    'is_mandatory' => true,
                    'description' => 'CMM operation is mandatory for QC personnel',
                ]
            );

            DivisionSkill::updateOrCreate(
                ['division_id' => $qualityControl->id, 'skill_id' => $caliperSkill->id],
                [
                    'required_level' => 2,
                    'is_mandatory' => true,
                    'description' => 'Caliper proficiency required for quality inspection',
                ]
            );
        }

        // Gudang Division Skills
        if ($gudang) {
            DivisionSkill::updateOrCreate(
                ['division_id' => $gudang->id, 'skill_id' => $inventorySkill->id],
                [
                    'required_level' => 2,
                    'is_mandatory' => true,
                    'description' => 'Inventory management mandatory for warehouse staff',
                ]
            );
        }

        // Sales Division Skills
        if ($sales) {
            DivisionSkill::updateOrCreate(
                ['division_id' => $sales->id, 'skill_id' => $negoSkill->id],
                [
                    'required_level' => 2,
                    'is_mandatory' => true,
                    'description' => 'Negotiation skills required for sales team',
                ]
            );
        }

        $this->command->info('✅ CBT Skills seeded successfully!');
        $this->command->info('   - CMM Operation');
        $this->command->info('   - Hand Tools / Caliper');
        $this->command->info('   - Inventory Management');
        $this->command->info('   - Negotiation');
    }
}
