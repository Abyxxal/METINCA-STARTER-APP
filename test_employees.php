<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = \Illuminate\Http\Request::capture()
);

// Test query
use App\Models\Employee;
$employees = Employee::with(['department', 'division', 'position'])->get();
echo "Total Employees: " . $employees->count() . "\n";
foreach ($employees as $emp) {
    echo "NIK: " . $emp->nik . ", Divisi: " . ($emp->division ? $emp->division->name : 'NULL') . "\n";
}
