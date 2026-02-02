<?php
// Test file untuk cek data division dan position
require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "<h2>DATA CHECK - DIVISIONS & POSITIONS</h2>";
echo "<style>body{font-family:monospace;padding:20px;} table{border-collapse:collapse;} td,th{border:1px solid #ccc;padding:8px;} .ok{color:green;} .error{color:red;}</style>";

echo "<h3>Departments:</h3>";
$departments = \App\Models\Department::all();
echo "<table><tr><th>ID</th><th>Name</th></tr>";
foreach($departments as $dept) {
    echo "<tr><td>{$dept->id}</td><td>{$dept->name}</td></tr>";
}
echo "</table>";
echo "<p class='ok'>✓ Total: " . $departments->count() . " departments</p>";

echo "<h3>Divisions:</h3>";
$divisions = \App\Models\Division::all();
echo "<table><tr><th>ID</th><th>Name</th><th>Department ID</th></tr>";
foreach($divisions as $div) {
    echo "<tr><td>{$div->id}</td><td>{$div->name}</td><td>{$div->department_id}</td></tr>";
}
echo "</table>";
echo "<p class='ok'>✓ Total: " . $divisions->count() . " divisions</p>";

echo "<h3>Positions:</h3>";
$positions = \App\Models\Position::all();
echo "<table><tr><th>ID</th><th>Name</th><th>Division ID</th></tr>";
foreach($positions as $pos) {
    echo "<tr><td>{$pos->id}</td><td>{$pos->name}</td><td>{$pos->division_id}</td></tr>";
}
echo "</table>";
echo "<p class='ok'>✓ Total: " . $positions->count() . " positions</p>";

echo "<hr><h3>HTML TEST: Division Dropdown Simulation</h3>";
echo "<select id='testDivision' style='width:300px;padding:8px;margin:10px 0;'>";
echo "<option value=''>-- Pilih Divisi --</option>";
foreach($divisions as $div) {
    echo "<option value='{$div->id}' data-department='{$div->department_id}' style='display:none;'>{$div->name}</option>";
}
echo "</select><br>";
echo "<button onclick='showForDept(1)' style='padding:8px 15px;margin:5px;'>Show for Dept 1</button>";
echo "<button onclick='showForDept(3)' style='padding:8px 15px;margin:5px;'>Show for Dept 3</button>";
echo "<button onclick='showAll()' style='padding:8px 15px;margin:5px;'>Show All</button>";

echo "<script>
function showForDept(deptId) {
    console.log('Showing divisions for department:', deptId);
    let count = 0;
    document.querySelectorAll('#testDivision option').forEach((opt, i) => {
        if (i === 0) return; // skip first
        const optDept = opt.getAttribute('data-department');
        if (optDept == deptId) {
            opt.style.display = '';
            count++;
            console.log('  SHOW:', opt.value, opt.textContent, 'dept=', optDept);
        } else {
            opt.style.display = 'none';
            console.log('  HIDE:', opt.value, opt.textContent, 'dept=', optDept);
        }
    });
    console.log('Total shown:', count);
    alert('Showed ' + count + ' divisions for dept ' + deptId + '. Check dropdown and console.');
}

function showAll() {
    document.querySelectorAll('#testDivision option').forEach((opt, i) => {
        if (i > 0) opt.style.display = '';
    });
    alert('All options shown');
}

console.log('TEST PAGE LOADED');
console.log('Total option elements:', document.querySelectorAll('#testDivision option').length);
document.querySelectorAll('#testDivision option').forEach((opt, i) => {
    if (i > 0) {
        console.log('Option ' + i + ':', {
            value: opt.value,
            text: opt.textContent,
            dataDepartment: opt.getAttribute('data-department'),
            display: opt.style.display
        });
    }
});
</script>";

echo "<hr><p class='ok'><strong>✓ Test page loaded successfully!</strong></p>";
echo "<p>1. Check data di tabel atas</p>";
echo "<p>2. Click button untuk test dropdown filter</p>";
echo "<p>3. Open Console (F12) untuk lihat log detail</p>";
?>