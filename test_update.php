<?php
require 'vendor/autoload.php';

use App\Models\Employee;

$app = require 'bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Check employee data
$employee = Employee::first();
if ($employee) {
    echo "✅ Employee found:\n";
    echo "  NIK: " . $employee->nik . "\n";
    echo "  Name: " . $employee->nama_karyawan . "\n";
    echo "  Email: " . $employee->email . "\n";
    echo "  Dept ID: " . $employee->department_id . "\n";
    echo "  Division ID: " . $employee->division_id . "\n";
    echo "  Position ID: " . $employee->position_id . "\n";
} else {
    echo "❌ No employee found\n";
}
