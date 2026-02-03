<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use App\Models\Santri;
use App\Models\JenisPembayaran;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PembayaranController extends Controller
{
    /**
     * Display the pembayaran index page
     */
    public function index()
    {
        $jenisPembayaran = JenisPembayaran::where('is_active', 1)->get();
        $tahunAjaran = TahunAjaran::orderBy('tahun_mulai', 'desc')->get();
        
        return view('pembayaran.index', compact('jenisPembayaran', 'tahunAjaran'));
    }

    /**
     * Get pembayaran data for DataTables (AJAX)
     */
    public function getData(Request $request)
    {
        $query = Pembayaran::with(['santri', 'jenisPembayaran', 'tahunAjaran', 'petugas'])
            ->select('pembayaran.*');

        // Search functionality
        if ($request->has('search') && $request->search['value']) {
            $searchValue = $request->search['value'];
            $query->where(function($q) use ($searchValue) {
                $q->where('kode_pembayaran', 'like', "%{$searchValue}%")
                  ->orWhere('nomor_referensi', 'like', "%{$searchValue}%")
                  ->orWhere('nominal', 'like', "%{$searchValue}%")
                  ->orWhereHas('santri', function($sq) use ($searchValue) {
                      $sq->where('nama_lengkap', 'like', "%{$searchValue}%")
                         ->orWhere('nis', 'like', "%{$searchValue}%");
                  });
            });
        }

        // Column search
        if ($request->has('santri_name') && $request->santri_name) {
            $query->whereHas('santri', function($q) use ($request) {
                $q->where('nama_lengkap', 'like', "%{$request->santri_name}%");
            });
        }

        if ($request->has('jenis_pembayaran_id') && $request->jenis_pembayaran_id) {
            $query->where('jenis_pembayaran_id', $request->jenis_pembayaran_id);
        }

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('tanggal_pembayaran', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('tanggal_pembayaran', '<=', $request->date_to);
        }

        // Get total records before pagination
        $totalRecords = Pembayaran::count();
        $filteredRecords = $query->count();

        // Ordering
        $orderColumnIndex = $request->order[0]['column'] ?? 0;
        $orderDirection = $request->order[0]['dir'] ?? 'desc';
        
        $columns = ['id', 'kode_pembayaran', 'santri_id', 'jenis_pembayaran_id', 'tanggal_pembayaran', 'total_bayar', 'status'];
        $orderColumn = $columns[$orderColumnIndex] ?? 'id';
        
        $query->orderBy($orderColumn, $orderDirection);

        // Pagination
        $start = $request->start ?? 0;
        $length = $request->length ?? 10;
        
        $data = $query->skip($start)->take($length)->get();

        // Format data for DataTables
        $formattedData = $data->map(function($pembayaran) {
            return [
                'id' => $pembayaran->id,
                'kode_pembayaran' => $pembayaran->kode_pembayaran,
                'santri_nama' => $pembayaran->santri->nama_lengkap ?? '-',
                'santri_nis' => $pembayaran->santri->nis ?? '-',
                'jenis_pembayaran' => $pembayaran->jenisPembayaran->nama ?? '-',
                'jenis_kategori' => $pembayaran->jenisPembayaran->kategori ?? '-',
                'tanggal_pembayaran' => $pembayaran->tanggal_pembayaran->format('d M Y'),
                'bulan_tahun' => $pembayaran->bulan && $pembayaran->tahun 
                    ? date('F Y', mktime(0, 0, 0, $pembayaran->bulan, 1, $pembayaran->tahun))
                    : '-',
                'nominal' => number_format($pembayaran->nominal, 0, ',', '.'),
                'potongan' => number_format($pembayaran->potongan, 0, ',', '.'),
                'denda' => number_format($pembayaran->denda, 0, ',', '.'),
                'total_bayar' => number_format($pembayaran->total_bayar, 0, ',', '.'),
                'metode_pembayaran' => ucfirst($pembayaran->metode_pembayaran),
                'status' => $pembayaran->status,
                'petugas_nama' => $pembayaran->petugas->nama_lengkap ?? 'System',
                'created_at' => $pembayaran->created_at->format('d M Y H:i'),
            ];
        });

        return response()->json([
            'draw' => intval($request->draw),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $formattedData
        ]);
    }

    /**
     * Store a newly created pembayaran
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'santri_id' => 'required|exists:santri,id',
            'jenis_pembayaran_id' => 'required|exists:jenis_pembayaran,id',
            'tahun_ajaran_id' => 'nullable|exists:tahun_ajaran,id',
            'tanggal_pembayaran' => 'required|date',
            'bulan' => 'nullable|integer|min:1|max:12',
            'tahun' => 'nullable|integer|min:2000|max:2100',
            'nominal' => 'required|numeric|min:0',
            'potongan' => 'nullable|numeric|min:0',
            'denda' => 'nullable|numeric|min:0',
            'metode_pembayaran' => 'required|in:tunai,transfer,qris,lainnya',
            'nomor_referensi' => 'nullable|string|max:100',
            'status' => 'required|in:lunas,belum_lunas,cicilan',
            'keterangan' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Generate unique payment code
            $kode = $this->generateKodePembayaran();

            // Calculate total
            $nominal = $request->nominal;
            $potongan = $request->potongan ?? 0;
            $denda = $request->denda ?? 0;
            $totalBayar = $nominal - $potongan + $denda;

            $pembayaran = Pembayaran::create([
                'kode_pembayaran' => $kode,
                'santri_id' => $request->santri_id,
                'jenis_pembayaran_id' => $request->jenis_pembayaran_id,
                'tahun_ajaran_id' => $request->tahun_ajaran_id,
                'tanggal_pembayaran' => $request->tanggal_pembayaran,
                'bulan' => $request->bulan,
                'tahun' => $request->tahun,
                'nominal' => $nominal,
                'potongan' => $potongan,
                'denda' => $denda,
                'total_bayar' => $totalBayar,
                'metode_pembayaran' => $request->metode_pembayaran,
                'nomor_referensi' => $request->nomor_referensi,
                'status' => $request->status,
                'petugas_id' => 1, // Hardcoded until auth is implemented
                'keterangan' => $request->keterangan,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pembayaran berhasil disimpan',
                'data' => $pembayaran
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified pembayaran
     */
    public function show($id)
    {
        $pembayaran = Pembayaran::with(['santri', 'jenisPembayaran', 'tahunAjaran', 'petugas'])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $pembayaran->id,
                'kode_pembayaran' => $pembayaran->kode_pembayaran,
                'santri_id' => $pembayaran->santri_id,
                'santri_nama' => $pembayaran->santri->nama_lengkap,
                'santri_nis' => $pembayaran->santri->nis,
                'jenis_pembayaran_id' => $pembayaran->jenis_pembayaran_id,
                'jenis_pembayaran_nama' => $pembayaran->jenisPembayaran->nama,
                'tahun_ajaran_id' => $pembayaran->tahun_ajaran_id,
                'tanggal_pembayaran' => $pembayaran->tanggal_pembayaran->format('Y-m-d'),
                'bulan' => $pembayaran->bulan,
                'tahun' => $pembayaran->tahun,
                'nominal' => $pembayaran->nominal,
                'potongan' => $pembayaran->potongan,
                'denda' => $pembayaran->denda,
                'total_bayar' => $pembayaran->total_bayar,
                'metode_pembayaran' => $pembayaran->metode_pembayaran,
                'nomor_referensi' => $pembayaran->nomor_referensi,
                'status' => $pembayaran->status,
                'keterangan' => $pembayaran->keterangan,
            ]
        ]);
    }

    /**
     * Update the specified pembayaran
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'santri_id' => 'required|exists:santri,id',
            'jenis_pembayaran_id' => 'required|exists:jenis_pembayaran,id',
            'tahun_ajaran_id' => 'nullable|exists:tahun_ajaran,id',
            'tanggal_pembayaran' => 'required|date',
            'bulan' => 'nullable|integer|min:1|max:12',
            'tahun' => 'nullable|integer|min:2000|max:2100',
            'nominal' => 'required|numeric|min:0',
            'potongan' => 'nullable|numeric|min:0',
            'denda' => 'nullable|numeric|min:0',
            'metode_pembayaran' => 'required|in:tunai,transfer,qris,lainnya',
            'nomor_referensi' => 'nullable|string|max:100',
            'status' => 'required|in:lunas,belum_lunas,cicilan',
            'keterangan' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $pembayaran = Pembayaran::findOrFail($id);

            // Calculate total
            $nominal = $request->nominal;
            $potongan = $request->potongan ?? 0;
            $denda = $request->denda ?? 0;
            $totalBayar = $nominal - $potongan + $denda;

            $pembayaran->update([
                'santri_id' => $request->santri_id,
                'jenis_pembayaran_id' => $request->jenis_pembayaran_id,
                'tahun_ajaran_id' => $request->tahun_ajaran_id,
                'tanggal_pembayaran' => $request->tanggal_pembayaran,
                'bulan' => $request->bulan,
                'tahun' => $request->tahun,
                'nominal' => $nominal,
                'potongan' => $potongan,
                'denda' => $denda,
                'total_bayar' => $totalBayar,
                'metode_pembayaran' => $request->metode_pembayaran,
                'nomor_referensi' => $request->nomor_referensi,
                'status' => $request->status,
                'keterangan' => $request->keterangan,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pembayaran berhasil diperbarui',
                'data' => $pembayaran
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified pembayaran
     */
    public function destroy($id)
    {
        try {
            $pembayaran = Pembayaran::findOrFail($id);
            $pembayaran->delete();

            return response()->json([
                'success' => true,
                'message' => 'Pembayaran berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk delete pembayaran
     */
    public function bulkDestroy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ids' => 'required|array',
            'ids.*' => 'exists:pembayaran,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $count = Pembayaran::whereIn('id', $request->ids)->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "{$count} pembayaran berhasil dihapus"
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search santri for select2
     */
    public function searchSantri(Request $request)
    {
        $search = $request->get('q', '');
        
        $santri = Santri::where('status', 'aktif')
            ->where(function($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('nis', 'like', "%{$search}%");
            })
            ->limit(20)
            ->get(['id', 'nis', 'nama_lengkap']);

        $results = $santri->map(function($s) {
            return [
                'id' => $s->id,
                'text' => "{$s->nis} - {$s->nama_lengkap}"
            ];
        });

        return response()->json(['results' => $results]);
    }

    /**
     * Get jenis pembayaran details
     */
    public function getJenisPembayaran($id)
    {
        $jenis = JenisPembayaran::findOrFail($id);
        
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $jenis->id,
                'nama' => $jenis->nama,
                'kategori' => $jenis->kategori,
                'nominal' => $jenis->nominal,
            ]
        ]);
    }

    /**
     * Generate unique payment code
     */
    private function generateKodePembayaran()
    {
        $prefix = 'PAY';
        $date = date('Ymd');
        
        // Get last payment code for today
        $lastPayment = Pembayaran::where('kode_pembayaran', 'like', "{$prefix}-{$date}-%")
            ->orderBy('kode_pembayaran', 'desc')
            ->first();

        if ($lastPayment) {
            $lastNumber = intval(substr($lastPayment->kode_pembayaran, -4));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return sprintf("%s-%s-%04d", $prefix, $date, $newNumber);
    }
}