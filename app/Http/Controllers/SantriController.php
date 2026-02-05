<?php

namespace App\Http\Controllers;

use App\Models\Santri;
use App\Models\Pembayaran;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SantriController extends Controller
{
    public function dashboard()
    {
        $user = auth()->user();
        
        // Get santri data for current logged-in user
        $santri = Santri::where('user_id', $user->id)->first();
        
        if (!$santri) {
            abort(404, 'Data santri tidak ditemukan untuk user ini');
        }

        // Get santri profile data
        $profile = [
            'nis' => $santri->nis,
            'nisn' => $santri->nisn,
            'nama_lengkap' => $santri->nama_lengkap,
            'jenis_kelamin' => $santri->jenis_kelamin,
            'status' => $santri->status,
        ];

        // Get kelas info
        $kelas = null;
        try {
            $kelasData = DB::table('kelas_santri')
                ->join('kelas', 'kelas_santri.kelas_id', '=', 'kelas.id')
                ->where('kelas_santri.santri_id', $santri->id)
                ->where('kelas_santri.status', 'aktif')
                ->select('kelas.nama_kelas')
                ->first();

            $kelas = $kelasData ? $kelasData->nama_kelas : 'Belum ada kelas yang aktif';
        } catch (\Exception $e) {
            $kelas = 'Belum ada kelas yang aktif';
        }

        // Get kamar info
        $kamar = null;
        try {
            $kamarData = DB::table('penghuni_kamar')
                ->join('kamar', 'penghuni_kamar.kamar_id', '=', 'kamar.id')
                ->where('penghuni_kamar.santri_id', $santri->id)
                ->where('penghuni_kamar.status', 'aktif')
                ->select('kamar.nama_kamar')
                ->first();
            
            $kamar = $kamarData ? $kamarData->nama_kamar : 'Belum ada kamar yang aktif';
        } catch (\Exception $e) {
            $kamar = 'Belum ada kamar yang aktif';
        }

        // Get payment status for current and next month
        $currentMonth = Carbon::now();
        $nextMonth = Carbon::now()->addMonth();
        
        // Get SPP payment type ID
        $sppTypeId = DB::table('jenis_pembayaran')
            ->where('kategori', 'bulanan')
            ->where('nama', 'LIKE', '%SPP%')
            ->value('id');

        // Current month payment
        $currentMonthPayment = null;
        if ($sppTypeId) {
            $currentMonthPayment = Pembayaran::where('santri_id', $santri->id)
                ->where('jenis_pembayaran_id', $sppTypeId)
                ->whereMonth('tanggal_pembayaran', $currentMonth->month)
                ->whereYear('tanggal_pembayaran', $currentMonth->year)
                ->first();
        }

        // Next month payment
        $nextMonthPayment = null;
        if ($sppTypeId) {
            $nextMonthPayment = Pembayaran::where('santri_id', $santri->id)
                ->where('jenis_pembayaran_id', $sppTypeId)
                ->whereMonth('tanggal_pembayaran', $nextMonth->month)
                ->whereYear('tanggal_pembayaran', $nextMonth->year)
                ->first();
        }

        // Get default SPP amount
        $defaultSppAmount = 500000; // Default fallback
        if ($sppTypeId) {
            $sppType = DB::table('jenis_pembayaran')->find($sppTypeId);
            $defaultSppAmount = $sppType ? $sppType->nominal : 500000;
        }

        $payments = [
            'current' => [
                'month' => $currentMonth->isoFormat('MMMM YYYY'),
                'status' => $currentMonthPayment ? $currentMonthPayment->status : 'belum_lunas',
                'amount' => $currentMonthPayment ? $currentMonthPayment->total_bayar : $defaultSppAmount,
            ],
            'next' => [
                'month' => $nextMonth->isoFormat('MMMM YYYY'),
                'status' => $nextMonthPayment ? $nextMonthPayment->status : 'belum_lunas',
                'amount' => $nextMonthPayment ? $nextMonthPayment->total_bayar : $defaultSppAmount,
            ],
        ];

        return view('santri.dashboard', compact('profile', 'kelas', 'kamar', 'payments', 'santri'));
    }
    
    public function profile()
    {
        // Get santri data
        $user = auth()->user();
        $santri = Santri::where('user_id', $user->id)->first();
        // dd($santri);
        
        if (!$santri) {
            abort(404, 'Santri tidak ditemukan');
        }

        // Get complete profile data
        $data = Santri::getCompleteProfile($santri->id);
        
        if (!$data) {
            abort(404, 'Santri tidak ditemukan');
        }

        // Add payment summary
        $data['pembayaran_summary'] = Santri::getPembayaranSummary($santri->id);

        return view('santri.show', $data);
    }

    /**
     * Display santri profile with role-based access control
     */
        // Note: Access control is now handled based on user role
        // - SANTRI: Can only view their own profile (user_id match)
        // - WALI: Can only view their children (wali_santri table check)
        // - STAFF: Can view all profiles (no restriction)

    public function show($id)
    {
        // Get santri data
        $santri = Santri::find($id);
        
        if (!$santri) {
            abort(404, 'Santri tidak ditemukan');
        }

        $user = auth()->user();
        
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