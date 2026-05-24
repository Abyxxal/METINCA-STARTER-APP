<?php

namespace App\Http\Controllers\Admin;

use App\Events\DashboardStatsUpdated;
use App\Events\EmployeeDataUpdated;
use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Department;
use App\Models\Position;
use App\Models\Division;
use App\Models\Skill;
use App\Models\User;
use App\Models\EmployeeCompetency;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

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
                'name' => 'required|string',
                'email' => 'required|email|unique:users,email|unique:employees,email',
                'password' => 'required|string|min:6',
                'department_id' => 'required|exists:departments,id',
                'division_id' => 'required|exists:divisions,id',
                'position_id' => 'required|exists:positions,id',
                'status' => 'required|in:Aktif,Non-Aktif',
                'join_date' => 'nullable|date',
                'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            // Reset auto-increment jika tabel kosong
            $this->ensureAutoIncrementReset('employees');
            $this->ensureAutoIncrementReset('users');

            // Handle foto upload
            $photoPath = null;
            if ($request->hasFile('photo')) {
                $file = $request->file('photo');
                $photoPath = $file->store('employees', 'public');
            }

            // Buat record karyawan
            $employeeData = [
                'nik' => $validated['nik'],
                'name' => $validated['name'],
                'email' => $validated['email'],
                'department_id' => $validated['department_id'],
                'division_id' => $validated['division_id'],
                'position_id' => $validated['position_id'],
                'join_date' => $validated['join_date'] ?? null,
                'status' => $validated['status'],
                'photo' => $photoPath,
            ];
            
            $employee = Employee::create($employeeData);

            // Create User account untuk login
            try {
                $user = User::create([
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'nik' => $validated['nik'],
                    'password' => bcrypt($validated['password']),
                    'role' => 'user',
                    'employee_nik' => $employee->nik,  // ✅ FIXED: employee_id → employee_nik
                ]);
                
                \Log::info('✅ User account created for employee: ' . $employee->nik . ' (' . $employee->name . ')');
            } catch (\Exception $userError) {
                \Log::error('❌ Failed to create user account: ' . $userError->getMessage());
                // Delete employee jika user creation gagal (rollback)
                $employee->delete();
                throw new \Exception('Gagal membuat user account: ' . $userError->getMessage());
            }

            EmployeeDataUpdated::dispatch('created', $employee->nik, $employee->name);
            DashboardStatsUpdated::dispatch();

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
                'name' => 'required|string',
                'department_id' => 'required|exists:departments,id',
                'division_id' => 'required|exists:divisions,id',
                'position_id' => 'required|exists:positions,id',
                'status' => 'required|in:Aktif,Non-Aktif',
                'join_date' => 'nullable|date',
                'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
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

            // Handle foto update
            if ($request->hasFile('photo')) {
                // Delete old photo jika ada
                if ($employee->photo) {
                    \Storage::disk('public')->delete($employee->photo);
                }
                // Store foto baru
                $photoPath = $request->file('photo')->store('employees', 'public');
                $validated['photo'] = $photoPath;
            }

            // Update record karyawan
            $employee->update($validated);

            EmployeeDataUpdated::dispatch('updated', $employee->nik, $employee->name);
            DashboardStatsUpdated::dispatch();

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
     * Cascade: Hapus employee → hapus user account yang terkait juga
     * 
     * @param string $id NIK Karyawan
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroyEmployee($id)
    {
        try {
            $employee = Employee::findOrFail($id);
            $name = $employee->name;
            
            // Hapus user account yang terkait jika ada
            User::where('nik', $id)->delete();
            
            // Hapus employee
            $employee->delete();

            // Renumber IDs di employees dan users table
            $this->renumberTableIds('users');
            $this->renumberTableIds('employees');

            EmployeeDataUpdated::dispatch('deleted', $id, $name);
            DashboardStatsUpdated::dispatch();

            return response()->json([
                'success' => true,
                'message' => 'Karyawan ' . $name . ' dan user account-nya berhasil dihapus'
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
                'divisions' => 'nullable|array',
                'divisions.*.name' => 'required|string',
                'divisions.*.positions' => 'nullable|array',
                'divisions.*.positions.*.name' => 'required|string',
            ]);

            // Reset auto-increment jika tabel kosong
            $this->ensureAutoIncrementReset('departments');

            // Buat record departemen baru
            $department = Department::create([
                'name' => $validated['name']
            ]);

            // Simpan divisions dan positions jika ada
            if (isset($validated['divisions']) && is_array($validated['divisions'])) {
                foreach ($validated['divisions'] as $divisionData) {
                    $division = $department->divisions()->create([
                        'name' => $divisionData['name'],
                        'department_id' => $department->id
                    ]);

                    // Simpan positions untuk division ini
                    if (isset($divisionData['positions']) && is_array($divisionData['positions'])) {
                        foreach ($divisionData['positions'] as $positionData) {
                            $division->positions()->create([
                                'name' => $positionData['name'],
                                'division_id' => $division->id
                            ]);
                        }
                    }
                }
            }

            // Load relationships untuk response
            $department->load(['divisions.positions']);

            // Clear cache setelah create
            Cache::forget('departments_list');
            Cache::forget('departments_with_counts');

            DashboardStatsUpdated::dispatch();

            return response()->json([
                'success' => true,
                'message' => 'Departemen dengan divisi dan jabatan berhasil ditambahkan',
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
     * Memperbarui data departemen dengan divisions dan positions
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
                'divisions' => 'nullable|array',
                'divisions.*.id' => 'nullable|integer|exists:divisions,id',
                'divisions.*.name' => 'required|string',
                'divisions.*.positions' => 'nullable|array',
                'divisions.*.positions.*.id' => 'nullable|integer|exists:positions,id',
                'divisions.*.positions.*.name' => 'required|string',
            ]);

            // Update nama departemen
            $department->update(['name' => $validated['name']]);

            // Handle divisions dan positions jika ada
            if (isset($validated['divisions']) && is_array($validated['divisions'])) {
                // Track ID divisions yang dikirim untuk mengetahui mana yang dihapus
                $sentDivisionIds = [];

                foreach ($validated['divisions'] as $divisionData) {
                    if (isset($divisionData['id'])) {
                        // Update division yang sudah ada
                        $division = Division::find($divisionData['id']);
                        if ($division && $division->department_id == $department->id) {
                            $division->update(['name' => $divisionData['name']]);
                            $sentDivisionIds[] = $division->id;
                        }
                    } else {
                        // Buat division baru
                        $division = $department->divisions()->create([
                            'name' => $divisionData['name']
                        ]);
                        $sentDivisionIds[] = $division->id;
                    }

                    // Handle positions untuk division ini
                    if (isset($divisionData['positions']) && is_array($divisionData['positions'])) {
                        $sentPositionIds = [];

                        foreach ($divisionData['positions'] as $positionData) {
                            if (isset($positionData['id'])) {
                                // Update position yang sudah ada
                                $position = Position::find($positionData['id']);
                                if ($position && $position->division_id == $division->id) {
                                    $position->update(['name' => $positionData['name']]);
                                    $sentPositionIds[] = $position->id;
                                }
                            } else {
                                // Buat position baru
                                $position = $division->positions()->create([
                                    'name' => $positionData['name']
                                ]);
                                $sentPositionIds[] = $position->id;
                            }
                        }

                        // Hapus positions yang tidak ada di request (dihapus user)
                        $division->positions()->whereNotIn('id', $sentPositionIds)->delete();
                    }
                }

                // Hapus divisions yang tidak ada di request (dihapus user)
                $department->divisions()->whereNotIn('id', $sentDivisionIds)->delete();
            }

            // Load relationships untuk response
            $department->load(['divisions.positions']);

            // Clear cache
            Cache::forget('departments_list');
            Cache::forget('departments_with_counts');

            DashboardStatsUpdated::dispatch();

            return response()->json([
                'success' => true,
                'message' => 'Departemen dengan divisi dan jabatan berhasil diperbarui',
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

            // Reindex ID setelah delete
            $this->reindexDepartmentIds();

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

    /**
     * Reindex Department IDs untuk sequential 1, 2, 3, ...
     * Dipanggil setelah delete departemen
     */
    private function reindexDepartmentIds()
    {
        try {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
            
            // Ambil semua departments, sorted by created_at
            $departments = DB::table('departments')
                ->orderBy('created_at')
                ->select('id', 'name', 'description', 'created_at', 'updated_at')
                ->get();

            if ($departments->count() > 0) {
                // Mapping old ID ke new ID
                $idMapping = [];
                $newId = 1;
                foreach ($departments as $dept) {
                    $idMapping[$dept->id] = $newId;
                    $newId++;
                }
                
                // Update divisions dengan ID mapping (before updating departments)
                foreach ($idMapping as $oldId => $newIdVal) {
                    DB::statement('UPDATE divisions SET department_id = ? WHERE department_id = ?', [$newIdVal, $oldId]);
                }
                
                // Truncate dan rebuild departments dengan ID baru
                DB::table('departments')->truncate();
                DB::statement('ALTER TABLE departments AUTO_INCREMENT = 1');
                
                // Insert kembali dengan ID sequential
                $newId = 1;
                foreach ($departments as $dept) {
                    DB::table('departments')->insert([
                        'id' => $newId,
                        'name' => $dept->name,
                        'description' => $dept->description,
                        'created_at' => $dept->created_at,
                        'updated_at' => $dept->updated_at,
                    ]);
                    $newId++;
                }
                
                DB::statement('ALTER TABLE departments AUTO_INCREMENT = ' . $newId);
            }
            
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        } catch (\Exception $e) {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
            \Log::error('Error reindexing departments: ' . $e->getMessage());
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
                'position:id,name,division_id'
            ])
            ->select('nik', 'name', 'email', 'department_id', 'division_id', 'position_id', 'status', 'created_at', 'updated_at')
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
            // Cache selama 1 jam (3600 detik)
            $departments = Cache::remember('departments_list', 3600, function() {
                return Department::select('id', 'name')
                    ->where('status', 'active')
                    ->orderBy('name')
                    ->get();
            });

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
            // Tidak pakai cache agar data selalu fresh saat reload tabel
            $departments = Department::withCount('employees')
                ->withCount('divisions')
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
     * Mengambil detail departemen dengan divisions dan positions
     * 
     * @param int $id ID Departemen
     * @return \Illuminate\Http\JsonResponse
     */
    public function showDepartment($id)
    {
        try {
            $department = Department::with(['divisions.positions'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $department
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 404);
        }
    }

    /**
     * Mengambil daftar jabatan berdasarkan divisi
     * Query parameter: division_id (required)
     * IMPORTANT: Positions sekarang child dari Divisions, bukan Departments
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPositionsByDivision(Request $request)
    {
        try {
            $divisionId = $request->get('division_id');
            
            if (!$divisionId) {
                return response()->json([
                    'success' => false,
                    'message' => 'division_id required'
                ], 400);
            }

            // Tidak pakai cache agar data selalu fresh
            $positions = Position::where('division_id', $divisionId)
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
     * Menyimpan jabatan baru untuk divisi
     * IMPORTANT: Position sekarang child dari Division, bukan Department
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
                'division_id' => 'required|exists:divisions,id',
            ]);

            // Reset auto-increment jika tabel kosong
            $this->ensureAutoIncrementReset('positions');

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

            // Reindex positions untuk sequential IDs
            $this->reindexPositionIds();

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

    /**
     * Reindex ALL Position IDs untuk sequential 1, 2, 3, ... globally
     * Dipanggil setelah delete position
     */
    private function reindexPositionIds()
    {
        try {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
            
            // Ambil SEMUA positions, sorted by division_id, then created_at
            $allPositions = DB::table('positions')
                ->orderBy('division_id')
                ->orderBy('created_at')
                ->select('id', 'division_id', 'name', 'created_at', 'updated_at')
                ->get();

            if ($allPositions->count() > 0) {
                // Delete semua positions lama
                DB::statement('DELETE FROM positions');
                
                // Reset auto-increment
                DB::statement('ALTER TABLE positions AUTO_INCREMENT = 1');
                
                // Insert kembali dengan ID sequential global
                $newId = 1;
                foreach ($allPositions as $pos) {
                    DB::table('positions')->insert([
                        'id' => $newId,
                        'division_id' => $pos->division_id,
                        'name' => $pos->name,
                        'created_at' => $pos->created_at,
                        'updated_at' => $pos->updated_at,
                    ]);
                    $newId++;
                }
                
                // Set auto-increment ke nomor berikutnya
                DB::statement('ALTER TABLE positions AUTO_INCREMENT = ' . $newId);
            }
            
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        } catch (\Exception $e) {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
            \Log::error('Error reindexing positions: ' . $e->getMessage());
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
                    'employees.name',
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
                    'nama' => $employee->name,
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
            ]);

            // Reset auto-increment jika tabel kosong
            $this->ensureAutoIncrementReset('divisions');

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
            $departmentId = $division->department_id;
            $division->delete();

            // Reindex divisions untuk department ini
            $this->reindexDivisionIds($departmentId);

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
     * Reindex ALL Division IDs untuk sequential 1, 2, 3, ... globally
     * Dipanggil setelah delete divisi
     */
    private function reindexDivisionIds($departmentId)
    {
        try {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
            
            // Ambil SEMUA divisions, sorted by department_id, then created_at
            $allDivisions = DB::table('divisions')
                ->orderBy('department_id')
                ->orderBy('created_at')
                ->select('id', 'department_id', 'name', 'created_at', 'updated_at')
                ->get();

            if ($allDivisions->count() > 0) {
                // Map old ID ke new ID
                $idMapping = [];
                $newId = 1;
                foreach ($allDivisions as $div) {
                    $idMapping[$div->id] = $newId;
                    $newId++;
                }
                
                // Update positions dengan ID mapping (sebelum update divisions)
                foreach ($idMapping as $oldId => $newIdVal) {
                    if ($oldId != $newIdVal) {
                        DB::statement('UPDATE positions SET division_id = ? WHERE division_id = ?', [$newIdVal, $oldId]);
                    }
                }
                
                // Delete semua divisions lama
                DB::statement('DELETE FROM divisions');
                
                // Reset auto-increment
                DB::statement('ALTER TABLE divisions AUTO_INCREMENT = 1');
                
                // Insert kembali dengan ID sequential global
                $newId = 1;
                foreach ($allDivisions as $div) {
                    DB::table('divisions')->insert([
                        'id' => $newId,
                        'department_id' => $div->department_id,
                        'name' => $div->name,
                        'created_at' => $div->created_at,
                        'updated_at' => $div->updated_at,
                    ]);
                    $newId++;
                }
                
                // Set auto-increment ke nomor berikutnya
                DB::statement('ALTER TABLE divisions AUTO_INCREMENT = ' . $newId);
            }
            
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
            
            // Juga reindex positions setelah divisions di-reindex
            $this->reindexPositionIds();
        } catch (\Exception $e) {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
            \Log::error('Error reindexing divisions: ' . $e->getMessage());
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
            
            // Build query - tidak pakai cache agar data selalu fresh
            $query = Division::select('id', 'name', 'department_id');
            
            // Apply filter department jika diberikan
            if ($departmentId) {
                $query->where('department_id', $departmentId);
            }

            // Get data
            $divisions = $query->orderBy('name')->get();

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
                ->where('is_active', true)
                ->get(['id', 'name', 'code']);
            
            return response()->json([
                'success' => true, 
                'skills' => $skills
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => $e->getMessage(),
                'skills' => []
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
                    'nama' => $emp->name,
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

    // ============================================
    // HELPER METHODS
    // ============================================

    /**
     * Memastikan auto-increment direset ke 1 jika tabel kosong
     * Dipanggil sebelum create record baru
     * Mengatasi masalah: ketika semua data dihapus, auto-increment tidak reset
     * 
     * @param string $tableName Nama tabel (departments, divisions, positions)
     */
    private function ensureAutoIncrementReset($tableName)
    {
        try {
            $count = DB::table($tableName)->count();
            
            // Jika tabel kosong, reset auto-increment ke 1
            if ($count === 0) {
                DB::statement("ALTER TABLE {$tableName} AUTO_INCREMENT = 1");
            }
        } catch (\Exception $e) {
            \Log::warning("Could not reset auto-increment for {$tableName}: " . $e->getMessage());
        }
    }

    /**
     * Renumber semua ID di table (fill gaps setelah delete)
     * Menyalin data lama ke table baru dengan ID baru, hapus yang lama, rename
     */
    private function renumberTableIds($tableName)
    {
        try {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
            
            $count = DB::table($tableName)->count();
            $maxId = DB::table($tableName)->max('id');
            
            // Jika tabel kosong atau tidak ada gaps, tidak perlu renumber
            if (!$maxId || $count === $maxId) {
                DB::statement('SET FOREIGN_KEY_CHECKS=1');
                return;
            }

            // Ambil semua records, sorted by id (untuk maintain order)
            $records = DB::table($tableName)->orderBy('id')->get();

            if ($records->count() > 0) {
                // Get semua columns kecuali ID
                $columns = DB::getSchemaBuilder()->getColumnListing($tableName);
                $columnsWithoutId = array_diff($columns, ['id']);
                
                // Truncate dan reset auto-increment
                DB::table($tableName)->truncate();
                DB::statement("ALTER TABLE {$tableName} AUTO_INCREMENT = 1");
                
                // Insert kembali dengan ID sequential
                $newId = 1;
                foreach ($records as $record) {
                    $data = (array) $record;
                    unset($data['id']); // Remove old ID
                    DB::table($tableName)->insert($data);
                    $newId++;
                }
                
                \Log::info("✅ Table {$tableName} renumbered successfully");
            }
            
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        } catch (\Exception $e) {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
            \Log::error("❌ Error renumbering {$tableName}: " . $e->getMessage());
        }
    }
}
