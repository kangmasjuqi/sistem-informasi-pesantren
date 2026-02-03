<?php

namespace App\Http\Controllers;

use App\Models\Santri;

class SantriController extends Controller
{
    /**
     * Display santri dashboard with complete profile
     */
    public function show($id)
    {
        $data = Santri::getCompleteProfile($id);
        
        if (!$data) {
            abort(404, 'Santri tidak ditemukan');
        }

        // Add payment summary
        $data['pembayaran_summary'] = Santri::getPembayaranSummary($id);

        return view('santri.dashboard', $data);
    }
}