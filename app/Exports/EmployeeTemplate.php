<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\PatternFill;

class EmployeeTemplate implements FromCollection, WithHeadings, WithStyles
{
    /**
     * Return collection of sample data
     */
    public function collection()
    {
        return collect([
            [
                'nik' => 'EMP047',
                'name' => 'Contoh Karyawan',
                'email' => 'contoh@metinca.com',
                'department_id' => '1',
                'position_id' => '1',
                'phone' => '081234567890',
                'address' => 'Jl. Contoh No. 123',
                'photo_path' => 'path/to/photo.jpg',
                'hire_date' => '2024-01-15',
                'status' => 'active',
            ],
        ]);
    }

    /**
     * Define headings
     */
    public function headings(): array
    {
        return [
            'nik',
            'name',
            'email',
            'department_id',
            'position_id',
            'phone',
            'address',
            'photo_path',
            'hire_date',
            'status',
        ];
    }

    /**
     * Apply styles to the sheet
     */
    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => PatternFill::FILL_SOLID, 'startColor' => ['rgb' => '0D6EFD']],
            ],
        ];
    }
}
