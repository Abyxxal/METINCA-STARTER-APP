<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Department;
use App\Models\Position;
use Illuminate\Http\Request;

class MasterDataController extends Controller
{
    // ============================================
    // EMPLOYEE CRUD
    // ============================================

    /**
     * Store employee
     * POST /api/employees
     */
    public function storeEmployee(Request $request)
    {
        try {
            $validated = $request->validate([
                'nik' => 'required|unique:employees,nik',
                'name' => 'required|string',
                'department_id' => 'required|exists:departments,id',
                'position_id' => 'required|exists:positions,id',
                'status' => 'required|in:active,inactive,resigned',
            ]);

            $employee = Employee::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Karyawan berhasil ditambahkan',
                'data' => $employee
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Update employee
     * PUT /api/employees/{id}
     */
    public function updateEmployee(Request $request, $id)
    {
        try {
            $employee = Employee::findOrFail($id);

            $validated = $request->validate([
                'nik' => 'required|unique:employees,nik,' . $id,
                'name' => 'required|string',
                'department_id' => 'required|exists:departments,id',
                'position_id' => 'required|exists:positions,id',
                'status' => 'required|in:active,inactive,resigned',
            ]);

            $employee->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Karyawan berhasil diperbarui',
                'data' => $employee
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Delete employee
     * DELETE /api/employees/{id}
     */
    public function destroyEmployee($id)
    {
        try {
            $employee = Employee::findOrFail($id);
            $name = $employee->name;
            $employee->delete();

            return response()->json([
                'success' => true,
                'message' => 'Karyawan ' . $name . ' berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 400);
        }
    }

    // ============================================
    // DEPARTMENT CRUD
    // ============================================

    /**
     * Store department
     * POST /api/departments
     */
    public function storeDepartment(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|unique:departments,name|string',
                'employee_count' => 'nullable|integer|min:0',
                'status' => 'nullable|in:active,inactive',
            ]);

            if (!isset($validated['status'])) {
                $validated['status'] = 'active';
            }

            $department = Department::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Departemen berhasil ditambahkan',
                'data' => $department
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Update department
     * PUT /api/departments/{id}
     */
    public function updateDepartment(Request $request, $id)
    {
        try {
            $department = Department::findOrFail($id);

            $validated = $request->validate([
                'name' => 'required|unique:departments,name,' . $id . '|string',
                'employee_count' => 'nullable|integer|min:0',
                'status' => 'nullable|in:active,inactive',
            ]);

            $department->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Departemen berhasil diperbarui',
                'data' => $department
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Delete department
     * DELETE /api/departments/{id}
     */
    public function destroyDepartment($id)
    {
        try {
            $department = Department::findOrFail($id);
            $name = $department->name;
            $department->delete();

            return response()->json([
                'success' => true,
                'message' => 'Departemen ' . $name . ' berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 400);
        }
    }

    // ============================================
    // DATA FETCHING (for DataTables)
    // ============================================

    /**
     * Get all employees for DataTable
     * GET /api/employees
     */
    public function getEmployees()
    {
        try {
            $employees = Employee::with(['department', 'position'])
                ->select('id', 'nik', 'name', 'email', 'department_id', 'position_id', 'status')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $employees
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Get single employee
     * GET /api/employees/{id}
     */
    public function getEmployee($id)
    {
        try {
            $employee = Employee::with(['department', 'position'])->find($id);

            if (!$employee) {
                return response()->json([
                    'success' => false,
                    'message' => 'Karyawan tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $employee
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Get all departments for dropdown
     * GET /api/departments/list
     */
    public function listDepartments()
    {
        try {
            $departments = Department::select('id', 'name')
                ->where('status', 'active')
                ->orderBy('name')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $departments
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Get positions by department
     * GET /api/positions?department_id={id}
     */
    public function getPositionsByDepartment(Request $request)
    {
        try {
            $departmentId = $request->get('department_id');
            
            if (!$departmentId) {
                return response()->json([
                    'success' => false,
                    'message' => 'department_id required'
                ], 400);
            }

            $positions = Position::where('department_id', $departmentId)
                ->select('id', 'name')
                ->orderBy('name')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $positions
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 400);
        }
    }
}
