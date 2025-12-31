<?php

namespace Database\Seeders;

use App\Models\Employee;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 46 Karyawan dengan distribusi:
     * - Quality: 10
     * - Maintenance: 9
     * - PPC: 8
     * - Produksi & Dev Engineering: 19
     */
    public function run(): void
    {
        $employees = [
            // ===== QUALITY (10) =====
            ['nik' => 'EMP001', 'nama_karyawan' => 'Budi Santoso', 'email' => 'budi.santoso@metinca.com', 'department_id' => 1, 'position_id' => 1, 'status' => 'active'],
            ['nik' => 'EMP002', 'nama_karyawan' => 'Siti Nurhaliza', 'email' => 'siti.nurhaliza@metinca.com', 'department_id' => 1, 'position_id' => 2, 'status' => 'active'],
            ['nik' => 'EMP003', 'nama_karyawan' => 'Ahmad Wijaya', 'email' => 'ahmad.wijaya@metinca.com', 'department_id' => 1, 'position_id' => 2, 'status' => 'active'],
            ['nik' => 'EMP004', 'nama_karyawan' => 'Dewi Lestari', 'email' => 'dewi.lestari@metinca.com', 'department_id' => 1, 'position_id' => 3, 'status' => 'active'],
            ['nik' => 'EMP005', 'nama_karyawan' => 'Rinto Harahap', 'email' => 'rinto.harahap@metinca.com', 'department_id' => 1, 'position_id' => 3, 'status' => 'active'],
            ['nik' => 'EMP006', 'nama_karyawan' => 'Nisa Permata', 'email' => 'nisa.permata@metinca.com', 'department_id' => 1, 'position_id' => 4, 'status' => 'active'],
            ['nik' => 'EMP007', 'nama_karyawan' => 'Bambang Setiawan', 'email' => 'bambang.setiawan@metinca.com', 'department_id' => 1, 'position_id' => 4, 'status' => 'active'],
            ['nik' => 'EMP008', 'nama_karyawan' => 'Endang Suryani', 'email' => 'endang.suryani@metinca.com', 'department_id' => 1, 'position_id' => 2, 'status' => 'active'],
            ['nik' => 'EMP009', 'nama_karyawan' => 'Hendra Gunawan', 'email' => 'hendra.gunawan@metinca.com', 'department_id' => 1, 'position_id' => 3, 'status' => 'active'],
            ['nik' => 'EMP010', 'nama_karyawan' => 'Intan Kusuma', 'email' => 'intan.kusuma@metinca.com', 'department_id' => 1, 'position_id' => 4, 'status' => 'active'],

            // ===== MAINTENANCE (9) =====
            ['nik' => 'EMP011', 'nama_karyawan' => 'Joko Prabowo', 'email' => 'joko.prabowo@metinca.com', 'department_id' => 2, 'position_id' => 5, 'status' => 'active'],
            ['nik' => 'EMP012', 'nama_karyawan' => 'Kurnia Sari', 'email' => 'kurnia.sari@metinca.com', 'department_id' => 2, 'position_id' => 6, 'status' => 'active'],
            ['nik' => 'EMP013', 'nama_karyawan' => 'Luthfi Pratama', 'email' => 'luthfi.pratama@metinca.com', 'department_id' => 2, 'position_id' => 6, 'status' => 'active'],
            ['nik' => 'EMP014', 'nama_karyawan' => 'Meida Chandra', 'email' => 'meida.chandra@metinca.com', 'department_id' => 2, 'position_id' => 7, 'status' => 'active'],
            ['nik' => 'EMP015', 'nama_karyawan' => 'Nando Simanjuntak', 'email' => 'nando.simanjuntak@metinca.com', 'department_id' => 2, 'position_id' => 7, 'status' => 'active'],
            ['nik' => 'EMP016', 'nama_karyawan' => 'Oka Mahendra', 'email' => 'oka.mahendra@metinca.com', 'department_id' => 2, 'position_id' => 8, 'status' => 'active'],
            ['nik' => 'EMP017', 'nama_karyawan' => 'Putri Handayani', 'email' => 'putri.handayani@metinca.com', 'department_id' => 2, 'position_id' => 6, 'status' => 'active'],
            ['nik' => 'EMP018', 'nama_karyawan' => 'Radi Sumarno', 'email' => 'radi.sumarno@metinca.com', 'department_id' => 2, 'position_id' => 8, 'status' => 'active'],
            ['nik' => 'EMP019', 'nama_karyawan' => 'Sandi Murdianto', 'email' => 'sandi.murdianto@metinca.com', 'department_id' => 2, 'position_id' => 7, 'status' => 'active'],

            // ===== PPC (8) =====
            ['nik' => 'EMP020', 'nama_karyawan' => 'Teguh Hartono', 'email' => 'teguh.hartono@metinca.com', 'department_id' => 3, 'position_id' => 9, 'status' => 'active'],
            ['nik' => 'EMP021', 'nama_karyawan' => 'Umi Komalasari', 'email' => 'umi.komalasari@metinca.com', 'department_id' => 3, 'position_id' => 10, 'status' => 'active'],
            ['nik' => 'EMP022', 'nama_karyawan' => 'Vino Pratama', 'email' => 'vino.pratama@metinca.com', 'department_id' => 3, 'position_id' => 10, 'status' => 'active'],
            ['nik' => 'EMP023', 'nama_karyawan' => 'Wili Kurniawan', 'email' => 'wili.kurniawan@metinca.com', 'department_id' => 3, 'position_id' => 11, 'status' => 'active'],
            ['nik' => 'EMP024', 'nama_karyawan' => 'Xena Mentari', 'email' => 'xena.mentari@metinca.com', 'department_id' => 3, 'position_id' => 11, 'status' => 'active'],
            ['nik' => 'EMP025', 'nama_karyawan' => 'Yanto Budiardjo', 'email' => 'yanto.budiardjo@metinca.com', 'department_id' => 3, 'position_id' => 12, 'status' => 'active'],
            ['nik' => 'EMP026', 'nama_karyawan' => 'Zakia Pertiwi', 'email' => 'zakia.pertiwi@metinca.com', 'department_id' => 3, 'position_id' => 12, 'status' => 'active'],
            ['nik' => 'EMP027', 'nama_karyawan' => 'Ari Hendrayana', 'email' => 'ari.hendrayana@metinca.com', 'department_id' => 3, 'position_id' => 10, 'status' => 'active'],

            // ===== PRODUKSI & DEV ENGINEERING (19) =====
            ['nik' => 'EMP028', 'nama_karyawan' => 'Bambang Karyanto', 'email' => 'bambang.karyanto@metinca.com', 'department_id' => 4, 'position_id' => 13, 'status' => 'active'],
            ['nik' => 'EMP029', 'nama_karyawan' => 'Cahyono Widodo', 'email' => 'cahyono.widodo@metinca.com', 'department_id' => 4, 'position_id' => 14, 'status' => 'active'],
            ['nik' => 'EMP030', 'nama_karyawan' => 'Dadang Sutrisno', 'email' => 'dadang.sutrisno@metinca.com', 'department_id' => 4, 'position_id' => 15, 'status' => 'active'],
            ['nik' => 'EMP031', 'nama_karyawan' => 'Eka Mulyadi', 'email' => 'eka.mulyadi@metinca.com', 'department_id' => 4, 'position_id' => 15, 'status' => 'active'],
            ['nik' => 'EMP032', 'nama_karyawan' => 'Fahmi Aziz', 'email' => 'fahmi.aziz@metinca.com', 'department_id' => 4, 'position_id' => 16, 'status' => 'active'],
            ['nik' => 'EMP033', 'nama_karyawan' => 'Gatot Subagio', 'email' => 'gatot.subagio@metinca.com', 'department_id' => 4, 'position_id' => 16, 'status' => 'active'],
            ['nik' => 'EMP034', 'nama_karyawan' => 'Haris Anwar', 'email' => 'haris.anwar@metinca.com', 'department_id' => 4, 'position_id' => 17, 'status' => 'active'],
            ['nik' => 'EMP035', 'nama_karyawan' => 'Indra Permana', 'email' => 'indra.permana@metinca.com', 'department_id' => 4, 'position_id' => 17, 'status' => 'active'],
            ['nik' => 'EMP036', 'nama_karyawan' => 'Jaya Kusuma', 'email' => 'jaya.kusuma@metinca.com', 'department_id' => 4, 'position_id' => 15, 'status' => 'active'],
            ['nik' => 'EMP037', 'nama_karyawan' => 'Karim Budiman', 'email' => 'karim.budiman@metinca.com', 'department_id' => 4, 'position_id' => 16, 'status' => 'active'],
            ['nik' => 'EMP038', 'nama_karyawan' => 'Laksana Distra', 'email' => 'laksana.distra@metinca.com', 'department_id' => 4, 'position_id' => 17, 'status' => 'active'],
            ['nik' => 'EMP039', 'nama_karyawan' => 'Malik Santosa', 'email' => 'malik.santosa@metinca.com', 'department_id' => 4, 'position_id' => 15, 'status' => 'active'],
            ['nik' => 'EMP040', 'nama_karyawan' => 'Nabil Hidayat', 'email' => 'nabil.hidayat@metinca.com', 'department_id' => 4, 'position_id' => 16, 'status' => 'active'],
            ['nik' => 'EMP041', 'nama_karyawan' => 'Oki Trianto', 'email' => 'oki.trianto@metinca.com', 'department_id' => 4, 'position_id' => 17, 'status' => 'active'],
            ['nik' => 'EMP042', 'nama_karyawan' => 'Panca Wibisono', 'email' => 'panca.wibisono@metinca.com', 'department_id' => 4, 'position_id' => 15, 'status' => 'active'],
            ['nik' => 'EMP043', 'nama_karyawan' => 'Qori Firmansyah', 'email' => 'qori.firmansyah@metinca.com', 'department_id' => 4, 'position_id' => 16, 'status' => 'active'],
            ['nik' => 'EMP044', 'nama_karyawan' => 'Rama Darmadi', 'email' => 'rama.darmadi@metinca.com', 'department_id' => 4, 'position_id' => 17, 'status' => 'active'],
            ['nik' => 'EMP045', 'nama_karyawan' => 'Sanjaya Wijaya', 'email' => 'sanjaya.wijaya@metinca.com', 'department_id' => 4, 'position_id' => 15, 'status' => 'active'],
            ['nik' => 'EMP046', 'nama_karyawan' => 'Toni Hermawan', 'email' => 'toni.hermawan@metinca.com', 'department_id' => 4, 'position_id' => 16, 'status' => 'active'],
        ];
        foreach ($employees as $employee) {
            // Add password with default value
            $employee['password'] = bcrypt('password123');
            Employee::create($employee);
        }
    }
}
