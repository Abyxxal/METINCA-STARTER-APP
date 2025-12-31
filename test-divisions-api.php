<?php
// Test script untuk verify divisions API

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/bootstrap/app.php';

use App\Models\Division;
use App\Models\Department;

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== DIVISIONS API TEST ===\n\n";

// Test 1: Check if divisions table exists
echo "1. Checking divisions table...\n";
$divisions_count = Division::count();
echo "   Total divisions in DB: $divisions_count\n\n";

// Test 2: List all divisions
echo "2. All divisions:\n";
$all_divisions = Division::with('department')->get();
foreach ($all_divisions as $div) {
    echo "   - ID: {$div->id}, Dept ID: {$div->department_id}, Name: {$div->name}\n";
}
echo "\n";

// Test 3: List divisions by department
echo "3. Divisions by department:\n";
$departments = Department::all();
foreach ($departments as $dept) {
    $count = Division::where('department_id', $dept->id)->count();
    echo "   - Department: {$dept->name} (ID: {$dept->id}) has $count divisions\n";
    
    $divs = Division::where('department_id', $dept->id)->get();
    foreach ($divs as $div) {
        echo "      * {$div->name}\n";
    }
}

echo "\n=== END TEST ===\n";
