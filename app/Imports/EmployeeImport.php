<?php

namespace App\Imports;

use App\Models\Employee;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class EmployeeImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
     * @param array $row
     *
     * @return Employee
     */
    public function model(array $row)
    {
        return new Employee([
            'nik' => $row['nik'] ?? null,
            'name' => $row['name'] ?? null,
            'email' => $row['email'] ?? null,
            'department_id' => $row['department_id'] ?? null,
            'position_id' => $row['position_id'] ?? null,
            'phone' => $row['phone'] ?? null,
            'address' => $row['address'] ?? null,
            'photo_path' => $row['photo_path'] ?? null,
            'hire_date' => $row['hire_date'] ?? null,
            'status' => $row['status'] ?? 'active',
        ]);
    }

    /**
     * Validation rules untuk setiap row
     */
    public function rules(): array
    {
        return [
            'nik' => ['required', 'string', 'unique:employees,nik'],
            'name' => ['required', 'string'],
            'email' => ['nullable', 'email', 'unique:employees,email'],
            'department_id' => ['required', 'exists:departments,id'],
            'position_id' => ['required', 'exists:positions,id'],
            'phone' => ['nullable', 'string'],
            'address' => ['nullable', 'string'],
            'photo_path' => ['nullable', 'string'],
            'hire_date' => ['nullable', 'date'],
            'status' => ['in:active,inactive,resigned'],
        ];
    }

    /**
     * Custom error messages
     */
    public function customValidationMessages()
    {
        return [
            'nik.required' => 'NIK harus diisi',
            'nik.unique' => 'NIK sudah ada di database',
            'name.required' => 'Nama harus diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah terdaftar',
            'department_id.required' => 'Department harus dipilih',
            'department_id.exists' => 'Department tidak ditemukan',
            'position_id.required' => 'Position harus dipilih',
            'position_id.exists' => 'Position tidak ditemukan',
            'status.in' => 'Status harus: active, inactive, atau resigned',
        ];
    }
}
