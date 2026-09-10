<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        $pageTitle = 'Beranda Admin';

        $totalJenisBarang = 0;
        $totalDistribusi = 0;
        $permohonanMenunggu = 0;

        return view('admin.beranda', compact(
            'pageTitle',
            'totalJenisBarang',
            'totalDistribusi',
            'permohonanMenunggu'
        ));
    }
}
