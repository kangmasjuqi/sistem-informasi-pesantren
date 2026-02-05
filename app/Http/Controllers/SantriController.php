<?php

namespace App\Http\Controllers;

use App\Models\Santri;
use Illuminate\Support\Facades\DB;

class SantriController extends Controller
{
    /**
     * Display santri profile with role-based access control
     */
        // Note: Access control is now handled based on user role
        // - SANTRI: Can only view their own profile (user_id match)
        // - WALI: Can only view their children (wali_santri table check)
        // - STAFF: Can view all profiles (no restriction)

    public function show($id)
    {
        $user = auth()->user();
        
        // Get santri data
        $santri = Santri::find($id);
        
        if (!$santri) {
            abort(404, 'Santri tidak ditemukan');
        }

        // Role-based access control
        if ($user->hasRole('SANTRI')) {
            // Santri can only view their own profile
            // Assuming santri has user_id column linking to users table
            if ($santri->user_id !== $user->id) {
                abort(403, 'Anda tidak memiliki akses untuk melihat profil santri ini');
            }
        } 
        elseif ($user->hasRole('WALI')) {
            // Wali can only view their children
            $isMyChild = DB::table('wali_santri')
                ->where('id', $user->id)
                ->where('santri_id', $id)
                ->exists();
            
            if (!$isMyChild) {
                abort(403, 'Anda tidak memiliki akses untuk melihat profil santri ini');
            }
        }
        // Staff roles (SUPERADMIN, ADMIN, KEPSEK, etc.) can view all profiles - no restriction

        // Get complete profile data
        $data = Santri::getCompleteProfile($id);
        
        if (!$data) {
            abort(404, 'Santri tidak ditemukan');
        }

        // Add payment summary
        $data['pembayaran_summary'] = Santri::getPembayaranSummary($id);

        return view('santri.show', $data);
    }
}