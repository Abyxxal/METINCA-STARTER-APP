<?php
// Test file untuk cek data division dan position
require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "<h2>DATA CHECK</h2>";

echo "<h3>Departments:</h3>";
$departments = \App\Models\Department::all();
foreach($departments as $dept) {
    echo "ID: {$dept->id} - Name: {$dept->name}<br>";
}

echo "<h3>Divisions:</h3>";
$divisions = \App\Models\Division::all();
foreach($divisions as $div) {
    echo "ID: {$div->id} - Name: {$div->name} - Department ID: {$div->department_id}<br>";
}

echo "<h3>Positions:</h3>";
$positions = \App\Models\Position::all();
foreach($positions as $pos) {
    echo "ID: {$pos->id} - Name: {$pos->name} - Division ID: {$pos->division_id}<br>";
}

echo "<hr>";
echo "<h3>HTML Dropdown Test:</h3>";
echo "<select id='testDivision'>";
echo "<option value=''>-- Pilih Divisi --</option>";
foreach($divisions as $div) {
    echo "<option value='{$div->id}' data-department='{$div->department_id}' style='display:none;'>{$div->name}</option>";
}
echo "</select>";

echo "<script>
console.log('TEST: Checking division options...');
document.querySelectorAll('#testDivision option').forEach((opt, i) => {
    if (i > 0) {
        console.log('Option ' + i + ':', {
            value: opt.value,
            dataDepartment: opt.getAttribute('data-department'),
            text: opt.textContent
        });
    }
});
</script>";
