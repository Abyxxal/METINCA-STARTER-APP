<?php

namespace App\Http\Controllers;

/**
 * PageController
 *
 * Controller tipis untuk halaman-halaman statis/katalog yang sebelumnya
 * dirender langsung dari closure route (R2: routes murni tanpa logika).
 */
class PageController extends Controller
{
    // ============================================
    // COMPANY PROFILE PUBLIK
    // ============================================

    public function homeMain()
    {
        return view('home.main');
    }

    public function homeProducts()
    {
        return view('home.products');
    }

    public function homeDivisions()
    {
        return view('home.divisions');
    }

    public function homeFacilities()
    {
        return view('home.facilities');
    }

    public function homeGallery()
    {
        return view('home.galleries');
    }

    // ============================================
    // HALAMAN KATALOG / INFORMASI ADMIN
    // ============================================

    public function materialManagement()
    {
        return view('admin.material-management');
    }

    public function evaluationAndExam()
    {
        return view('admin.evaluation-and-exam');
    }

    public function reportAndAudit()
    {
        return view('admin.report-and-audit');
    }

    public function settings()
    {
        return view('admin.settings');
    }

    public function departmentsShow($id)
    {
        return view('admin.departments.show');
    }

    public function machiningMonitoringIndex()
    {
        return view('admin.machining.monitoring.index');
    }
}
