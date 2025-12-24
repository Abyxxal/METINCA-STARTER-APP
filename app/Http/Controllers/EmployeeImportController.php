<?php

namespace App\Http\Controllers;

use App\Exports\EmployeeTemplate;
use App\Imports\EmployeeImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class EmployeeImportController extends Controller
{
    /**
     * Show import form
     */
    public function showImportForm()
    {
        return view('employee-import');
    }

    /**
     * Handle Excel file upload and import
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv'
        ]);

        try {
            Excel::import(new EmployeeImport(), $request->file('file'));

            return back()->with('success', 'Data karyawan berhasil diimport!');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();

            return back()
                ->withErrors(['file' => 'Ada error di file Excel'])
                ->with('failures', $failures);
        } catch (\Exception $e) {
            return back()->withErrors(['file' => 'Error: ' . $e->getMessage()]);
        }
    }

    /**
     * Download template Excel
     */
    public function downloadTemplate()
    {
        return Excel::download(new EmployeeTemplate(), 'employee_template.xlsx');
    }
}
