<?php
require 'vendor/autoload.php';

use App\Models\Department;

$app = require 'bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Check departments
$depts = Department::all();
echo "Total Departments: " . count($depts) . "\n";
echo "Department API Response:\n";
echo json_encode(['success' => true, 'data' => $depts->toArray()], JSON_PRETTY_PRINT) . "\n";
