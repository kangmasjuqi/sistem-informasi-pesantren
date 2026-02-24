<?php

namespace App\Http\Controllers;

use App\Models\Santri;
use App\Models\Pembayaran;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class SantriController extends Controller
{
    public function index()
    {
        return view('santri.index');
    }

    /**
     * Select2 AJAX: search santri by name or NIS
     */
    public function searchSantri(Request $request)
    {
        $q = trim($request->get('q', ''));

        $results = Santri::where(function ($query) use ($q) {
                $query->where('nama_lengkap', 'like', "%{$q}%")
                      ->orWhere('nis', 'like', "%{$q}%");
            })
            ->orderBy('nama_lengkap')
            ->limit(20)
            ->get(['id', 'nama_lengkap', 'nis'])
            ->map(fn($s) => [
                'id'   => $s->id,
                'text' => "{$s->nis} – {$s->nama_lengkap}",
            ]);

        return response()->json(['results' => $results]);
    }

    public function getData(Request $request)
    {
        $query = Santri::query();

        // Global search
        // if (!empty($request->search['value'])) {
        //     $s = $request->search['value'];
        //     $query->where(function ($q) use ($s) {
        //         $q->where('nis', 'like', "%{$s}%")
        //           ->orWhere('nama_lengkap', 'like', "%{$s}%")
        //           ->orWhere('telepon', 'like', "%{$s}%")
        //           ->orWhere('nisn', 'like', "%{$s}%");
        //     });
        // }

        // Column filters
        // if (!empty($request->nis)) {
        //     $query->where('nis', 'like', "%{$request->nis}%");
        // }
        // if (!empty($request->nama_lengkap)) {
        //     $query->where('nama_lengkap', 'like', "%{$request->nama_lengkap}%");
        // }
        // if (!empty($request->jenis_kelamin)) {
        //     $query->where('jenis_kelamin', $request->jenis_kelamin);
        // }
        // if (!empty($request->status)) {
        //     $query->where('status', $request->status);
        // }
        // if (!empty($request->tanggal_dari)) {
        //     $query->whereDate('tanggal_masuk', '>=', $request->tanggal_dari);
        // }
        // if (!empty($request->tanggal_sampai)) {
        //     $query->whereDate('tanggal_masuk', '<=', $request->tanggal_sampai);
        // }

        $totalRecords    = Santri::count();
        $filteredRecords = $query->count();

        $columns     = ['id', 'nis', 'nama_lengkap', 'jenis_kelamin', 'tanggal_masuk', 'status'];
        $orderColIdx = $request->order[0]['column'] ?? 2;
        $orderDir    = $request->order[0]['dir']    ?? 'asc';
        $orderCol    = $columns[$orderColIdx] ?? 'nama_lengkap';

        $query->orderBy($orderCol, $orderDir);

        $start  = $request->start  ?? 0;
        $length = $request->length ?? 10;

        $data = $query->skip($start)->take($length)->get();

        $formatted = $data->map(fn($s) => [
            'id'                  => $s->id,
            'nis'                 => $s->nis,
            'nisn'                => $s->nisn,
            'nama_lengkap'        => $s->nama_lengkap,
            'nama_panggilan'      => $s->nama_panggilan,
            'jenis_kelamin'       => $s->jenis_kelamin,
            'tempat_lahir'        => $s->tempat_lahir,
            'tanggal_lahir'       => $s->tanggal_lahir?->format('Y-m-d'),
            'tanggal_lahir_fmt'   => $s->tanggal_lahir?->format('d M Y'),
            'umur'                => $s->umur,
            'telepon'             => $s->telepon,
            'golongan_darah'      => $s->golongan_darah,
            'foto_url'            => $s->foto ? Storage::url($s->foto) : null,
            'tanggal_masuk'       => $s->tanggal_masuk?->format('Y-m-d'),
            'tanggal_masuk_fmt'   => $s->tanggal_masuk?->format('d M Y'),
            'tanggal_keluar'      => $s->tanggal_keluar?->format('Y-m-d'),
            'tanggal_keluar_fmt'  => $s->tanggal_keluar?->format('d M Y'),
            'status'              => $s->status,
            'status_label'        => $s->status_label,
            'alamat_lengkap'      => $s->alamat_lengkap,
            'kabupaten'           => $s->kabupaten,
            'provinsi'            => $s->provinsi,
        ]);

        return response()->json([
            'draw'            => intval($request->draw),
            'recordsTotal'    => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data'            => $formatted,
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), $this->rules(), $this->messages());

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validasi gagal', 'errors' => $validator->errors()], 422);
        }

        try {
            $fotoPath = null;
            if ($request->hasFile('foto')) {
                $fotoPath = $request->file('foto')->store('santri/foto', 'public');
            }

            $user_id = 0; // TODO create User first 

            $santri = Santri::create([
                'user_id'         => $user_id,
                'nis'             => strtoupper(trim($request->nis)),
                'nisn'            => $request->nisn ? trim($request->nisn) : null,
                'nama_lengkap'    => $request->nama_lengkap,
                'nama_panggilan'  => $request->nama_panggilan,
                'jenis_kelamin'   => $request->jenis_kelamin,
                'tempat_lahir'    => $request->tempat_lahir,
                'tanggal_lahir'   => $request->tanggal_lahir,
                'nik'             => $request->nik,
                'alamat_lengkap'  => $request->alamat_lengkap,
                'provinsi'        => $request->provinsi,
                'kabupaten'       => $request->kabupaten,
                'kecamatan'       => $request->kecamatan,
                'kelurahan'       => $request->kelurahan,
                'kode_pos'        => $request->kode_pos,
                'telepon'         => $request->telepon,
                'anak_ke'         => $request->anak_ke,
                'jumlah_saudara'  => $request->jumlah_saudara,
                'golongan_darah'  => $request->golongan_darah,
                'riwayat_penyakit'=> $request->riwayat_penyakit,
                'foto'            => $fotoPath,
                'tanggal_masuk'   => $request->tanggal_masuk,
                'tanggal_keluar'  => $request->tanggal_keluar,
                'status'          => $request->status ?? 'aktif',
                'keterangan'      => $request->keterangan,
            ]);

            return response()->json(['success' => true, 'message' => 'Santri berhasil ditambahkan', 'data' => $santri]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $santri = Santri::findOrFail($id);

        $rules         = $this->rules();
        $rules['nis']  = ['required', 'string', 'max:20', Rule::unique('santri')->ignore($santri->id)];
        $rules['nisn'] = ['nullable', 'string', 'max:20', Rule::unique('santri')->ignore($santri->id)];
        $rules['nik']  = ['nullable', 'string', 'max:20', Rule::unique('santri')->ignore($santri->id)];

        $validator = Validator::make($request->all(), $rules, $this->messages());

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validasi gagal', 'errors' => $validator->errors()], 422);
        }

        try {
            $fotoPath = $santri->foto;
            if ($request->hasFile('foto')) {
                if ($fotoPath) Storage::disk('public')->delete($fotoPath);
                $fotoPath = $request->file('foto')->store('santri/foto', 'public');
            }

            $santri->update([
                'nis'             => strtoupper(trim($request->nis)),
                'nisn'            => $request->nisn ? trim($request->nisn) : null,
                'nama_lengkap'    => $request->nama_lengkap,
                'nama_panggilan'  => $request->nama_panggilan,
                'jenis_kelamin'   => $request->jenis_kelamin,
                'tempat_lahir'    => $request->tempat_lahir,
                'tanggal_lahir'   => $request->tanggal_lahir,
                'nik'             => $request->nik,
                'alamat_lengkap'  => $request->alamat_lengkap,
                'provinsi'        => $request->provinsi,
                'kabupaten'       => $request->kabupaten,
                'kecamatan'       => $request->kecamatan,
                'kelurahan'       => $request->kelurahan,
                'kode_pos'        => $request->kode_pos,
                'telepon'         => $request->telepon,
                'anak_ke'         => $request->anak_ke,
                'jumlah_saudara'  => $request->jumlah_saudara,
                'golongan_darah'  => $request->golongan_darah,
                'riwayat_penyakit'=> $request->riwayat_penyakit,
                'foto'            => $fotoPath,
                'tanggal_masuk'   => $request->tanggal_masuk,
                'tanggal_keluar'  => $request->tanggal_keluar,
                'status'          => $request->status,
                'keterangan'      => $request->keterangan,
            ]);

            return response()->json(['success' => true, 'message' => 'Data santri berhasil diperbarui', 'data' => $santri]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        $santri = Santri::findOrFail($id);

        try {
            if ($santri->foto) Storage::disk('public')->delete($santri->foto);
            $santri->delete();
            return response()->json(['success' => true, 'message' => 'Data santri berhasil dihapus']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    private function rules(): array
    {
        return [
            'nis'              => 'required|string|max:20|unique:santri,nis',
            'nisn'             => 'nullable|string|max:20|unique:santri,nisn',
            'nama_lengkap'     => 'required|string|max:255',
            'nama_panggilan'   => 'nullable|string|max:50',
            'jenis_kelamin'    => 'required|in:laki-laki,perempuan',
            'tempat_lahir'     => 'required|string|max:100',
            'tanggal_lahir'    => 'required|date',
            'nik'              => 'nullable|string|max:20|unique:santri,nik',
            'alamat_lengkap'   => 'required|string',
            'provinsi'         => 'nullable|string|max:100',
            'kabupaten'        => 'nullable|string|max:100',
            'kecamatan'        => 'nullable|string|max:100',
            'kelurahan'        => 'nullable|string|max:100',
            'kode_pos'         => 'nullable|string|max:10',
            'telepon'          => 'nullable|string|max:20',
            'anak_ke'          => 'nullable|integer|min:1',
            'jumlah_saudara'   => 'nullable|integer|min:0',
            'golongan_darah'   => 'nullable|string|max:5',
            'riwayat_penyakit' => 'nullable|string',
            'foto'             => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'tanggal_masuk'    => 'required|date',
            'tanggal_keluar'   => 'nullable|date|after_or_equal:tanggal_masuk',
            'status'           => 'required|in:aktif,lulus,pindah,keluar,cuti',
            'keterangan'       => 'nullable|string',
        ];
    }

    private function messages(): array
    {
        return [
            'nis.required'            => 'NIS harus diisi',
            'nis.unique'              => 'NIS sudah digunakan',
            'nisn.unique'             => 'NISN sudah digunakan',
            'nama_lengkap.required'   => 'Nama lengkap harus diisi',
            'jenis_kelamin.required'  => 'Jenis kelamin harus dipilih',
            'tempat_lahir.required'   => 'Tempat lahir harus diisi',
            'tanggal_lahir.required'  => 'Tanggal lahir harus diisi',
            'nik.unique'              => 'NIK sudah digunakan',
            'alamat_lengkap.required' => 'Alamat lengkap harus diisi',
            'foto.image'              => 'File harus berupa gambar',
            'foto.max'                => 'Ukuran foto maksimal 2MB',
            'tanggal_masuk.required'  => 'Tanggal masuk harus diisi',
            'tanggal_keluar.after_or_equal' => 'Tanggal keluar tidak boleh sebelum tanggal masuk',
            'status.required'         => 'Status harus dipilih',
        ];
    }

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

    public function showDetail($id)
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

        return response()->json([
            'success' => true,
            'data'    => [
                'id'              => $santri->id,
                'nis'             => $santri->nis,
                'nisn'            => $santri->nisn,
                'nama_lengkap'    => $santri->nama_lengkap,
                'jenis_kelamin'   => $santri->jenis_kelamin,
                'tempat_lahir'    => $santri->tempat_lahir,
                'tanggal_lahir'   => $santri->tanggal_lahir?->format('Y-m-d'),
                'telepon'         => $santri->telepon,
                'golongan_darah'  => $santri->golongan_darah,
                'foto_url'        => $santri->foto ? Storage::url($santri->foto) : null,
                'tanggal_masuk'   => $santri->tanggal_masuk?->format('Y-m-d'),
                'tanggal_keluar'  => $santri->tanggal_keluar?->format('Y-m-d'),
                'status'          => $santri->status,
                'alamat_lengkap'  => $santri->alamat_lengkap,
                'kabupaten'       => $santri->kabupaten,
                'provinsi'        => $santri->provinsi,
            ]
        ]);
    }
}