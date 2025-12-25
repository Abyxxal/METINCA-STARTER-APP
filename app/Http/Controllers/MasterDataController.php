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
                'nama_karyawan' => 'required|string',
                'email' => 'required|email|unique:employees,email',
                'password' => 'required|string|min:6',
                'department_id' => 'required|exists:departments,id',
                'position_id' => 'required|exists:positions,id',
                'status' => 'required|in:active,inactive,resigned',
            ]);

            // Hash password sebelum disimpan
            $validated['password'] = bcrypt($validated['password']);

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
                'nik' => 'required|unique:employees,nik,' . $id . ',nik',
                'nama_karyawan' => 'required|string',
                'email' => 'required|email|unique:employees,email,' . $id . ',nik',
                'password' => 'nullable|string|min:6',
                'department_id' => 'required|exists:departments,id',
                'position_id' => 'required|exists:positions,id',
                'status' => 'required|in:active,inactive,resigned',
            ]);

            // Hash password jika diubah
            if (isset($validated['password']) && $validated['password']) {
                $validated['password'] = bcrypt($validated['password']);
            } else {
                unset($validated['password']);
            }

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
            $name = $employee->nama_karyawan;
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
            $employees = Employee::with(['department:id,name', 'position:id,name,department_id'])
                ->select('nik', 'nama_karyawan', 'email', 'department_id', 'position_id', 'status', 'created_at', 'updated_at')
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
     * Get all departments for DataTable with employee count
     * GET /api/departments
     */
    public function getDepartments()
    {
        try {
            $departments = Department::where('status', 'active')
                ->withCount('employees')
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

    /**
     * Store position (Create new position for a department)
     * POST /api/positions
     */
    public function storePosition(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string',
                'department_id' => 'required|exists:departments,id',
                'level' => 'nullable|integer|min:1',
                'description' => 'nullable|string',
                'status' => 'nullable|in:active,inactive'
            ]);

            // Set default values
            if (!isset($validated['level'])) {
                $validated['level'] = 1;
            }
            if (!isset($validated['status'])) {
                $validated['status'] = 'active';
            }

            $position = Position::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Jabatan berhasil ditambahkan',
                'data' => $position
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 400);
        }
    }

    public function destroyPosition($id)
    {
        try {
            $position = Position::findOrFail($id);
            $position->delete();

            return response()->json([
                'success' => true,
                'message' => 'Jabatan berhasil dihapus',
                'data' => $position
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 400);
        }
    }
}
