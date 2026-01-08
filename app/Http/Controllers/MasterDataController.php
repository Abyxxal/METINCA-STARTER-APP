<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Department;
use App\Models\Position;
use App\Models\Division;
use App\Models\Skill;
use App\Models\EmployeeCompetency;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * MasterDataController
 * 
 * Mengelola semua operasi CRUD untuk master data:
 * - Karyawan (Employee)
 * - Departemen (Department)
 * - Divisi (Division)
 * - Jabatan (Position)
 * - Kompetensi & Skill
 */
class MasterDataController extends Controller
{
    // ============================================
    // EMPLOYEE - CREATE & UPDATE
    // ============================================

    /**
     * Menyimpan data karyawan baru ke database
     * 
     * Validasi: NIK, Email harus unik
     * Password di-hash sebelum disimpan
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeEmployee(Request $request)
    {
        try {
            // Validasi input
            $validated = $request->validate([
                'nik' => 'required|unique:employees,nik',
                'nama_karyawan' => 'required|string',
                'email' => 'required|email|unique:employees,email',
                'password' => 'required|string|min:6',
                'department_id' => 'required|exists:departments,id',
                'division_id' => 'required|exists:divisions,id',
                'position_id' => 'required|exists:positions,id',
                'status' => 'required|in:active,inactive,resigned',
            ]);

            // Hash password sebelum disimpan
            $validated['password'] = bcrypt($validated['password']);

            // Buat record karyawan baru
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
     * Memperbarui data karyawan yang sudah ada
     * 
     * Validasi: NIK & Email harus unik (exclude record saat ini)
     * Password opsional, jika tidak diisi password lama tetap dipertahankan
     * 
     * @param Request $request
     * @param string $id NIK Karyawan
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateEmployee(Request $request, $id)
    {
        try {
            $employee = Employee::findOrFail($id);

            // Build validation rules dynamically
            $rules = [
                'nama_karyawan' => 'required|string',
                'password' => 'nullable|string|min:6',
                'department_id' => 'required|exists:departments,id',
                'division_id' => 'required|exists:divisions,id',
                'position_id' => 'required|exists:positions,id',
                'status' => 'required|in:active,inactive,resigned',
            ];

            // Only validate NIK uniqueness if it changed
            $newNik = $request->input('nik');
            if ($newNik !== $employee->nik) {
                // NIK changed, validate uniqueness
                $rules['nik'] = ['required', Rule::unique('employees', 'nik')];
            } else {
                // NIK didn't change, just require it
                $rules['nik'] = 'required';
            }

            // Always validate email uniqueness but exclude current record
            $rules['email'] = ['required', 'email', Rule::unique('employees', 'email')->where(function ($query) use ($id) {
                return $query->where('nik', '!=', $id);
            })];

            $validated = $request->validate($rules);

            // Hash password hanya jika ada perubahan
            if (isset($validated['password']) && $validated['password']) {
                $validated['password'] = bcrypt($validated['password']);
            } else {
                unset($validated['password']);
            }

            // Update record karyawan
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
     * Menghapus data karyawan dari database
     * 
     * @param string $id NIK Karyawan
     * @return \Illuminate\Http\JsonResponse
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
    // DEPARTMENT - CREATE & UPDATE
    // ============================================

    /**
     * Menyimpan departemen baru ke database
     * 
     * Validasi: Nama departemen harus unik
     * Status default = 'active'
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeDepartment(Request $request)
    {
        try {
            // Validasi input
            $validated = $request->validate([
                'name' => 'required|unique:departments,name|string',
                'employee_count' => 'nullable|integer|min:0',
                'status' => 'nullable|in:active,inactive',
            ]);

            // Set status default
            if (!isset($validated['status'])) {
                $validated['status'] = 'active';
            }

            // Buat record departemen baru
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
     * Memperbarui data departemen
     * 
     * @param Request $request
     * @param int $id ID Departemen
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateDepartment(Request $request, $id)
    {
        try {
            $department = Department::findOrFail($id);

            // Validasi input
            $validated = $request->validate([
                'name' => 'required|unique:departments,name,' . $id . '|string',
                'employee_count' => 'nullable|integer|min:0',
                'status' => 'nullable|in:active,inactive',
            ]);

            // Update record departemen
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
     * Menghapus departemen dari database
     * 
     * @param int $id ID Departemen
     * @return \Illuminate\Http\JsonResponse
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
    // FETCHING DATA - untuk dropdown & tabel
    // ============================================

    /**
     * Mengambil daftar semua karyawan dengan relasi department, division, position
     * Digunakan untuk menampilkan tabel data karyawan
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getEmployees()
    {
        try {
            // Query dengan eager loading relasi
            $employees = Employee::with([
                'department:id,name', 
                'division:id,name', 
                'position:id,name,department_id'
            ])
            ->select('nik', 'nama_karyawan', 'email', 'department_id', 'division_id', 'position_id', 'status', 'created_at', 'updated_at')
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
     * Mengambil detail karyawan single berdasarkan ID/NIK
     * 
     * @param string $id NIK Karyawan
     * @return \Illuminate\Http\JsonResponse
     */
    public function getEmployee($id)
    {
        try {
            $employee = Employee::with(['department', 'division', 'position'])->find($id);

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
     * Mengambil daftar departemen yang aktif untuk dropdown
     * Digunakan saat add/edit karyawan
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function listDepartments()
    {
        try {
            // Query hanya departemen aktif, urutkan abjad
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
     * Mengambil daftar departemen dengan jumlah karyawan
     * Digunakan untuk menampilkan tabel departemen
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDepartments()
    {
        try {
            // Query dengan count relasi employees
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
     * Mengambil daftar jabatan berdasarkan departemen
     * Query parameter: department_id (required)
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
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

            // Query jabatan yang sesuai departemen
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
     * Menyimpan jabatan baru untuk departemen
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storePosition(Request $request)
    {
        try {
            // Validasi input
            $validated = $request->validate([
                'name' => 'required|string',
                'department_id' => 'required|exists:departments,id',
                'level' => 'nullable|integer|min:1',
                'description' => 'nullable|string',
                'status' => 'nullable|in:active,inactive'
            ]);

            // Set nilai default
            if (!isset($validated['level'])) {
                $validated['level'] = 1;
            }
            if (!isset($validated['status'])) {
                $validated['status'] = 'active';
            }

            // Buat record jabatan baru
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

    /**
     * Menghapus jabatan dari database
     * 
     * @param int $id ID Jabatan
     * @return \Illuminate\Http\JsonResponse
     */
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

    // ============================================
    // COMPETENCY MANAGEMENT
    // ============================================

    /**
     * Mengambil data kompetensi karyawan dengan filter departemen/NIK
     * Query parameter: department_id, nik (opsional)
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCompetencies(Request $request)
    {
        try {
            $departmentId = $request->query('department_id');
            $nik = $request->query('nik');

            // Build query dengan join untuk mendapatkan data lengkap
            $query = Employee::query()
                ->join('departments', 'employees.department_id', '=', 'departments.id')
                ->join('positions', 'employees.position_id', '=', 'positions.id')
                ->leftJoin('employee_competencies', 'employees.nik', '=', 'employee_competencies.nik')
                ->select([
                    'employees.nik',
                    'employees.nama_karyawan',
                    'employees.department_id',
                    'employees.position_id',
                    'employees.status',
                    'departments.name as nama_departemen',
                    'positions.name as nama_jabatan',
                    'employee_competencies.level'
                ]);

            // Apply filter jika diberikan
            if ($departmentId) {
                $query->where('employees.department_id', $departmentId);
            }

            if ($nik) {
                $query->where('employees.nik', $nik);
            }

            $employees = $query->get();

            // Format response
            $data = $employees->map(function ($employee) {
                return [
                    'id' => $employee->nik,
                    'nik' => $employee->nik,
                    'nama' => $employee->nama_karyawan,
                    'jabatan' => $employee->nama_jabatan ?? 'N/A',
                    'departemen' => $employee->nama_departemen ?? 'N/A',
                    'level' => $employee->level ?? 1,
                    'status' => $employee->status ?? 'active'
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $data
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Menyimpan atau memperbarui level kompetensi karyawan
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeCompetency(Request $request)
    {
        try {
            // Validasi input
            $validated = $request->validate([
                'nik' => 'required|exists:employees,nik',
                'level' => 'required|integer|in:1,2,3,4'
            ]);

            // Update atau create record kompetensi
            $competency = EmployeeCompetency::updateOrCreate(
                ['nik' => $validated['nik']],
                ['level' => $validated['level']]
            );

            return response()->json([
                'success' => true,
                'message' => 'Kompetensi berhasil diperbarui',
                'data' => $competency
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 400);
        }
    }

    // ============================================
    // DIVISION & SKILL MANAGEMENT
    // ============================================

    /**
     * Menyimpan divisi baru untuk departemen
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeDivision(Request $request)
    {
        try {
            // Validasi input
            $validated = $request->validate([
                'department_id' => 'required|integer|exists:departments,id',
                'name' => 'required|string|max:255',
                'description' => 'nullable|string|max:500',
                'status' => 'nullable|in:active,inactive',
            ]);

            // Set status default jika tidak diberikan
            if (!isset($validated['status']) || empty($validated['status'])) {
                $validated['status'] = 'active';
            }

            // Buat record divisi baru
            $division = Division::create($validated);

            return response()->json([
                'success' => true, 
                'data' => $division
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Menghapus divisi dari database
     * 
     * @param int $id ID Divisi
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroyDivision($id)
    {
        try {
            $division = Division::findOrFail($id);
            $division->delete();

            return response()->json([
                'success' => true, 
                'message' => 'Divisi berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Mengambil daftar divisi berdasarkan departemen
     * Query parameter: department_id (opsional - jika tidak ada, return semua divisi aktif)
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDivisions(Request $request)
    {
        try {
            $departmentId = $request->query('department_id');
            
            // Build query
            $query = Division::where('status', 'active');
            
            // Apply filter department jika diberikan
            if ($departmentId) {
                $query->where('department_id', $departmentId);
            }

            // Get data
            $divisions = $query->get();

            return response()->json([
                'success' => true, 
                'data' => $divisions
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Mengambil daftar skill untuk divisi tertentu
     * 
     * @param int $divisionId ID Divisi
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSkillsByDivision($divisionId)
    {
        try {
            // Query skill aktif untuk divisi
            $skills = Skill::where('division_id', $divisionId)
                ->where('status', 'active')
                ->get();
            
            return response()->json([
                'success' => true, 
                'data' => $skills
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Menyimpan skill baru untuk divisi
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeSkill(Request $request)
    {
        try {
            // Validasi input
            $validated = $request->validate([
                'division_id' => 'required|exists:divisions,id',
                'code' => 'required|string',
                'name' => 'required|string',
                'description' => 'nullable|string',
            ]);

            // Buat record skill baru
            $skill = Skill::create($validated);

            return response()->json([
                'success' => true, 
                'data' => $skill
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Menghapus skill dari database
     * 
     * @param int $id ID Skill
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroySkill($id)
    {
        try {
            // Delete skill
            Skill::findOrFail($id)->delete();

            return response()->json([
                'success' => true, 
                'message' => 'Skill berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Mengambil data kompetensi karyawan berdasarkan skill untuk membuat matrix
     * Query parameter: department_id, division_id (opsional)
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSkillBasedCompetencies(Request $request)
    {
        try {
            $departmentId = $request->query('department_id');
            $divisionId = $request->query('division_id');
            
            // Query karyawan aktif dengan relasi
            $query = Employee::with([
                'department', 
                'position', 
                'competencies.skill.division'
            ])->where('status', 'active');
            
            // Filter berdasarkan departemen jika diberikan
            if ($departmentId) {
                $query->where('department_id', $departmentId);
            }

            $employees = $query->get();
            
            // Query skill berdasarkan divisi/departemen
            $skillsQuery = Skill::where('status', 'active')->with('division');
            
            if ($divisionId) {
                // Jika divisi dipilih, ambil skill dari divisi itu saja
                $skillsQuery->where('division_id', $divisionId);
            } elseif ($departmentId) {
                // Jika departemen dipilih, ambil skill dari divisi dalam departemen itu
                $skillsQuery->whereHas('division', function($q) use ($departmentId) {
                    $q->where('department_id', $departmentId);
                });
            }
            
            $skills = $skillsQuery->get();

            // Build matrix data
            $matrix = [];
            foreach ($employees as $emp) {
                $empData = [
                    'nik' => $emp->nik,
                    'nama' => $emp->nama_karyawan,
                    'departemen' => $emp->department->name,
                    'jabatan' => $emp->position->name,
                    'status' => $emp->status,
                    'skills' => []
                ];

                // Untuk setiap skill, ambil level kompetensi karyawan (jika ada)
                foreach ($skills as $skill) {
                    $competency = $emp->competencies()
                        ->where('skill_id', $skill->id)
                        ->first();
                    
                    $empData['skills'][$skill->id] = [
                        'skill_id' => $skill->id,
                        'skill_code' => $skill->code,
                        'skill_name' => $skill->name,
                        'level' => $competency ? $competency->level : 0
                    ];
                }

                $matrix[] = $empData;
            }

            return response()->json([
                'success' => true,
                'data' => $matrix,
                'skills' => $skills
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Menyimpan atau memperbarui kompetensi skill karyawan
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeSkillCompetency(Request $request)
    {
        try {
            // Validasi input
            $validated = $request->validate([
                'nik' => 'required|exists:employees,nik',
                'skill_id' => 'required|exists:skills,id',
                'level' => 'required|integer|min:0|max:4',
            ]);

            // Update atau create record kompetensi skill
            $competency = EmployeeCompetency::updateOrCreate(
                ['nik' => $validated['nik'], 'skill_id' => $validated['skill_id']],
                ['level' => $validated['level']]
            );

            return response()->json([
                'success' => true, 
                'data' => $competency
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Reset password karyawan ke password default
     * 
     * @param string $id (NIK atau Employee ID)
     * @return \Illuminate\Http\JsonResponse
     */
    public function resetEmployeePassword($id)
    {
        try {
            // Find employee by NIK or ID
            $employee = Employee::where('nik', $id)->orWhere('id', $id)->first();

            if (!$employee) {
                return response()->json([
                    'success' => false,
                    'message' => 'Karyawan tidak ditemukan'
                ], 404);
            }

            // Get associated user
            $user = $employee->user;

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User terkait tidak ditemukan'
                ], 404);
            }

            // Set default password (use NIK as default password)
            $defaultPassword = $employee->nik;
            $user->update([
                'password' => bcrypt($defaultPassword)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Password karyawan berhasil direset ke NIK: ' . $employee->nik
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
