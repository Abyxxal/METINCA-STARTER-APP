<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

/**
 * EmployeeService
 * 
 * Service layer untuk business logic terkait Employee
 */
class EmployeeService
{
    /**
     * Create employee dengan user account
     */
    public function createEmployee(array $data)
    {
        DB::beginTransaction();
        try {
            // Create User first
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role' => 'user',
            ]);

            // Handle photo upload
            $photoPath = null;
            if (isset($data['photo'])) {
                $photoPath = $data['photo']->store('photos', 'public');
                $user->update(['photo' => $photoPath]);
            }

            // Create Employee
            $employee = Employee::create([
                'nik' => $data['nik'],
                'name' => $data['name'],
                'email' => $data['email'],
                'department_id' => $data['department_id'],
                'division_id' => $data['division_id'],
                'position_id' => $data['position_id'],
                'join_date' => $data['join_date'] ?? null,
                'status' => $data['status'] ?? 'Aktif',
            ]);

            // Link user to employee
            $user->update(['employee_id' => $employee->nik]);

            DB::commit();

            // Clear cache
            Cache::forget('employees_all');
            Cache::forget('departments_with_counts');

            return $employee->fresh(['department', 'division', 'position']);
        } catch (\Exception $e) {
            DB::rollBack();
            
            // Delete uploaded photo if exists
            if (isset($photoPath)) {
                Storage::disk('public')->delete($photoPath);
            }
            
            throw $e;
        }
    }

    /**
     * Update employee data
     */
    public function updateEmployee(Employee $employee, array $data)
    {
        DB::beginTransaction();
        try {
            // Handle NIK change
            $oldNik = $employee->nik;
            $newNik = $data['nik'];

            // Update employee
            $employee->fill([
                'name' => $data['name'],
                'department_id' => $data['department_id'],
                'division_id' => $data['division_id'],
                'position_id' => $data['position_id'],
                'join_date' => $data['join_date'] ?? $employee->join_date,
                'status' => $data['status'],
            ]);

            // Handle photo if provided
            if (isset($data['photo'])) {
                // Delete old photo
                if ($employee->user && $employee->user->photo) {
                    Storage::disk('public')->delete($employee->user->photo);
                }
                
                // Upload new photo
                $photoPath = $data['photo']->store('photos', 'public');
                if ($employee->user) {
                    $employee->user->update(['photo' => $photoPath]);
                }
            }

            // If NIK changed, update related records
            if ($oldNik !== $newNik) {
                // Update employee NIK (primary key)
                $employee->nik = $newNik;
                
                // Update user's employee_id
                if ($employee->user) {
                    $employee->user->update(['employee_id' => $newNik]);
                }
            }

            $employee->save();

            // Update related user if email changed
            if (isset($data['email']) && $employee->user) {
                $employee->user->update([
                    'name' => $data['name'],
                    'email' => $data['email'],
                ]);
                $employee->update(['email' => $data['email']]);
            }

            // Update password if provided
            if (isset($data['password']) && $employee->user) {
                $employee->user->update([
                    'password' => Hash::make($data['password'])
                ]);
            }

            DB::commit();

            // Clear cache
            Cache::forget('employees_all');
            Cache::forget('departments_with_counts');

            return $employee->fresh(['department', 'division', 'position', 'user']);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Delete employee (soft or hard delete based on has_data)
     */
    public function deleteEmployee(Employee $employee)
    {
        DB::beginTransaction();
        try {
            // Check if employee has exam sessions or competencies
            $hasExamSessions = $employee->examSessions()->count() > 0;
            $hasCompetencies = $employee->competencies()->count() > 0;

            if ($hasExamSessions || $hasCompetencies) {
                // Soft delete: set status to Non-Aktif
                $employee->update(['status' => 'Non-Aktif']);
                
                // Also deactivate user account
                if ($employee->user) {
                    $employee->user->update(['role' => 'inactive']);
                }
                
                $message = 'Employee has data, status changed to Non-Aktif';
            } else {
                // Hard delete: remove completely
                
                // Delete user account and photo
                if ($employee->user) {
                    if ($employee->user->photo) {
                        Storage::disk('public')->delete($employee->user->photo);
                    }
                    $employee->user->delete();
                }
                
                // Delete employee
                $employee->delete();
                
                $message = 'Employee deleted permanently';
            }

            DB::commit();

            // Clear cache
            Cache::forget('employees_all');
            Cache::forget('departments_with_counts');

            return ['success' => true, 'message' => $message];
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Import employees from array data
     */
    public function importEmployees(array $employeesData)
    {
        $imported = 0;
        $failed = [];

        foreach ($employeesData as $index => $data) {
            try {
                $this->createEmployee($data);
                $imported++;
            } catch (\Exception $e) {
                $failed[] = [
                    'row' => $index + 1,
                    'data' => $data,
                    'error' => $e->getMessage()
                ];
            }
        }

        return [
            'imported' => $imported,
            'failed' => $failed,
            'total' => count($employeesData)
        ];
    }

    /**
     * Export employees to array format
     */
    public function exportEmployees(array $filters = [])
    {
        $query = Employee::with(['department', 'division', 'position']);

        // Apply filters
        if (isset($filters['department_id'])) {
            $query->where('department_id', $filters['department_id']);
        }
        if (isset($filters['division_id'])) {
            $query->where('division_id', $filters['division_id']);
        }
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->get()->map(function($employee) {
            return [
                'nik' => $employee->nik,
                'name' => $employee->name,
                'email' => $employee->email,
                'department' => $employee->department->name ?? '',
                'division' => $employee->division->name ?? '',
                'position' => $employee->position->name ?? '',
                'join_date' => $employee->join_date,
                'status' => $employee->status,
            ];
        })->toArray();
    }
}
