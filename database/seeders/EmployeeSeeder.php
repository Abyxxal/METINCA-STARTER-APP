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
            ['nik' => 'EMP001', 'name' => 'Budi Santoso', 'email' => 'budi.santoso@metinca.com', 'department_id' => 1, 'position_id' => 1, 'status' => 'active'],
            ['nik' => 'EMP002', 'name' => 'Siti Nurhaliza', 'email' => 'siti.nurhaliza@metinca.com', 'department_id' => 1, 'position_id' => 2, 'status' => 'active'],
            ['nik' => 'EMP003', 'name' => 'Ahmad Wijaya', 'email' => 'ahmad.wijaya@metinca.com', 'department_id' => 1, 'position_id' => 2, 'status' => 'active'],
            ['nik' => 'EMP004', 'name' => 'Dewi Lestari', 'email' => 'dewi.lestari@metinca.com', 'department_id' => 1, 'position_id' => 3, 'status' => 'active'],
            ['nik' => 'EMP005', 'name' => 'Rinto Harahap', 'email' => 'rinto.harahap@metinca.com', 'department_id' => 1, 'position_id' => 3, 'status' => 'active'],
            ['nik' => 'EMP006', 'name' => 'Nisa Permata', 'email' => 'nisa.permata@metinca.com', 'department_id' => 1, 'position_id' => 4, 'status' => 'active'],
            ['nik' => 'EMP007', 'name' => 'Bambang Setiawan', 'email' => 'bambang.setiawan@metinca.com', 'department_id' => 1, 'position_id' => 4, 'status' => 'active'],
            ['nik' => 'EMP008', 'name' => 'Endang Suryani', 'email' => 'endang.suryani@metinca.com', 'department_id' => 1, 'position_id' => 2, 'status' => 'active'],
            ['nik' => 'EMP009', 'name' => 'Hendra Gunawan', 'email' => 'hendra.gunawan@metinca.com', 'department_id' => 1, 'position_id' => 3, 'status' => 'active'],
            ['nik' => 'EMP010', 'name' => 'Intan Kusuma', 'email' => 'intan.kusuma@metinca.com', 'department_id' => 1, 'position_id' => 4, 'status' => 'active'],

            // ===== MAINTENANCE (9) =====
            ['nik' => 'EMP011', 'name' => 'Joko Prabowo', 'email' => 'joko.prabowo@metinca.com', 'department_id' => 2, 'position_id' => 5, 'status' => 'active'],
            ['nik' => 'EMP012', 'name' => 'Kurnia Sari', 'email' => 'kurnia.sari@metinca.com', 'department_id' => 2, 'position_id' => 6, 'status' => 'active'],
            ['nik' => 'EMP013', 'name' => 'Luthfi Pratama', 'email' => 'luthfi.pratama@metinca.com', 'department_id' => 2, 'position_id' => 6, 'status' => 'active'],
            ['nik' => 'EMP014', 'name' => 'Meida Chandra', 'email' => 'meida.chandra@metinca.com', 'department_id' => 2, 'position_id' => 7, 'status' => 'active'],
            ['nik' => 'EMP015', 'name' => 'Nando Simanjuntak', 'email' => 'nando.simanjuntak@metinca.com', 'department_id' => 2, 'position_id' => 7, 'status' => 'active'],
            ['nik' => 'EMP016', 'name' => 'Oka Mahendra', 'email' => 'oka.mahendra@metinca.com', 'department_id' => 2, 'position_id' => 8, 'status' => 'active'],
            ['nik' => 'EMP017', 'name' => 'Putri Handayani', 'email' => 'putri.handayani@metinca.com', 'department_id' => 2, 'position_id' => 6, 'status' => 'active'],
            ['nik' => 'EMP018', 'name' => 'Radi Sumarno', 'email' => 'radi.sumarno@metinca.com', 'department_id' => 2, 'position_id' => 8, 'status' => 'active'],
            ['nik' => 'EMP019', 'name' => 'Sandi Murdianto', 'email' => 'sandi.murdianto@metinca.com', 'department_id' => 2, 'position_id' => 7, 'status' => 'active'],

            // ===== PPC (8) =====
            ['nik' => 'EMP020', 'name' => 'Teguh Hartono', 'email' => 'teguh.hartono@metinca.com', 'department_id' => 3, 'position_id' => 9, 'status' => 'active'],
            ['nik' => 'EMP021', 'name' => 'Umi Komalasari', 'email' => 'umi.komalasari@metinca.com', 'department_id' => 3, 'position_id' => 10, 'status' => 'active'],
            ['nik' => 'EMP022', 'name' => 'Vino Pratama', 'email' => 'vino.pratama@metinca.com', 'department_id' => 3, 'position_id' => 10, 'status' => 'active'],
            ['nik' => 'EMP023', 'name' => 'Wili Kurniawan', 'email' => 'wili.kurniawan@metinca.com', 'department_id' => 3, 'position_id' => 11, 'status' => 'active'],
            ['nik' => 'EMP024', 'name' => 'Xena Mentari', 'email' => 'xena.mentari@metinca.com', 'department_id' => 3, 'position_id' => 11, 'status' => 'active'],
            ['nik' => 'EMP025', 'name' => 'Yanto Budiardjo', 'email' => 'yanto.budiardjo@metinca.com', 'department_id' => 3, 'position_id' => 12, 'status' => 'active'],
            ['nik' => 'EMP026', 'name' => 'Zakia Pertiwi', 'email' => 'zakia.pertiwi@metinca.com', 'department_id' => 3, 'position_id' => 12, 'status' => 'active'],
            ['nik' => 'EMP027', 'name' => 'Ari Hendrayana', 'email' => 'ari.hendrayana@metinca.com', 'department_id' => 3, 'position_id' => 10, 'status' => 'active'],

            // ===== PRODUKSI & DEV ENGINEERING (19) =====
            ['nik' => 'EMP028', 'name' => 'Bambang Karyanto', 'email' => 'bambang.karyanto@metinca.com', 'department_id' => 4, 'position_id' => 13, 'status' => 'active'],
            ['nik' => 'EMP029', 'name' => 'Cahyono Widodo', 'email' => 'cahyono.widodo@metinca.com', 'department_id' => 4, 'position_id' => 14, 'status' => 'active'],
            ['nik' => 'EMP030', 'name' => 'Dadang Sutrisno', 'email' => 'dadang.sutrisno@metinca.com', 'department_id' => 4, 'position_id' => 15, 'status' => 'active'],
            ['nik' => 'EMP031', 'name' => 'Eka Mulyadi', 'email' => 'eka.mulyadi@metinca.com', 'department_id' => 4, 'position_id' => 15, 'status' => 'active'],
            ['nik' => 'EMP032', 'name' => 'Fahmi Aziz', 'email' => 'fahmi.aziz@metinca.com', 'department_id' => 4, 'position_id' => 16, 'status' => 'active'],
            ['nik' => 'EMP033', 'name' => 'Gatot Subagio', 'email' => 'gatot.subagio@metinca.com', 'department_id' => 4, 'position_id' => 16, 'status' => 'active'],
            ['nik' => 'EMP034', 'name' => 'Haris Anwar', 'email' => 'haris.anwar@metinca.com', 'department_id' => 4, 'position_id' => 17, 'status' => 'active'],
            ['nik' => 'EMP035', 'name' => 'Indra Permana', 'email' => 'indra.permana@metinca.com', 'department_id' => 4, 'position_id' => 17, 'status' => 'active'],
            ['nik' => 'EMP036', 'name' => 'Jaya Kusuma', 'email' => 'jaya.kusuma@metinca.com', 'department_id' => 4, 'position_id' => 15, 'status' => 'active'],
            ['nik' => 'EMP037', 'name' => 'Karim Budiman', 'email' => 'karim.budiman@metinca.com', 'department_id' => 4, 'position_id' => 16, 'status' => 'active'],
            ['nik' => 'EMP038', 'name' => 'Laksana Distra', 'email' => 'laksana.distra@metinca.com', 'department_id' => 4, 'position_id' => 17, 'status' => 'active'],
            ['nik' => 'EMP039', 'name' => 'Malik Santosa', 'email' => 'malik.santosa@metinca.com', 'department_id' => 4, 'position_id' => 15, 'status' => 'active'],
            ['nik' => 'EMP040', 'name' => 'Nabil Hidayat', 'email' => 'nabil.hidayat@metinca.com', 'department_id' => 4, 'position_id' => 16, 'status' => 'active'],
            ['nik' => 'EMP041', 'name' => 'Oki Trianto', 'email' => 'oki.trianto@metinca.com', 'department_id' => 4, 'position_id' => 17, 'status' => 'active'],
            ['nik' => 'EMP042', 'name' => 'Panca Wibisono', 'email' => 'panca.wibisono@metinca.com', 'department_id' => 4, 'position_id' => 15, 'status' => 'active'],
            ['nik' => 'EMP043', 'name' => 'Qori Firmansyah', 'email' => 'qori.firmansyah@metinca.com', 'department_id' => 4, 'position_id' => 16, 'status' => 'active'],
            ['nik' => 'EMP044', 'name' => 'Rama Darmadi', 'email' => 'rama.darmadi@metinca.com', 'department_id' => 4, 'position_id' => 17, 'status' => 'active'],
            ['nik' => 'EMP045', 'name' => 'Sanjaya Wijaya', 'email' => 'sanjaya.wijaya@metinca.com', 'department_id' => 4, 'position_id' => 15, 'status' => 'active'],
            ['nik' => 'EMP046', 'name' => 'Toni Hermawan', 'email' => 'toni.hermawan@metinca.com', 'department_id' => 4, 'position_id' => 16, 'status' => 'active'],
        ];

        foreach ($employees as $employee) {
            Employee::create($employee);
        }
    }
}
