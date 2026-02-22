<?php

namespace App\Http\Controllers;

use App\Models\Perizinan;
use App\Models\Santri;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PerizinanController extends Controller
{
    public function index()
    {
        return view('perizinan.index');
    }

    public function getData(Request $request)
    {
        $query = Perizinan::with(['santri', 'disetujuiOleh']);

        // Global search
        if (!empty($request->search['value'])) {
            $search = $request->search['value'];
            $query->where(function ($q) use ($search) {
                $q->where('nomor_izin', 'like', "%{$search}%")
                  ->orWhere('keperluan', 'like', "%{$search}%")
                  ->orWhereHas('santri', fn($s) => $s->where('nama_lengkap', 'like', "%{$search}%")
                                                      ->orWhere('nis', 'like', "%{$search}%"));
            });
        }

        // Column filters
        if (!empty($request->nomor_izin)) {
            $query->where('nomor_izin', 'like', "%{$request->nomor_izin}%");
        }
        if (!empty($request->santri_id)) {
            $query->where('santri_id', $request->santri_id);
        }
        if (!empty($request->jenis_izin)) {
            $query->where('jenis_izin', $request->jenis_izin);
        }
        if (!empty($request->status)) {
            $query->where('status', $request->status);
        }
        if (!empty($request->tanggal_dari)) {
            $query->whereDate('tanggal_mulai', '>=', $request->tanggal_dari);
        }
        if (!empty($request->tanggal_sampai)) {
            $query->whereDate('tanggal_mulai', '<=', $request->tanggal_sampai);
        }

        $totalRecords    = Perizinan::count();
        $filteredRecords = $query->count();

        // Ordering
        $columns     = ['id', 'nomor_izin', 'santri_id', 'jenis_izin', 'tanggal_mulai', 'tanggal_selesai', 'status'];
        $orderColIdx = $request->order[0]['column'] ?? 0;
        $orderDir    = $request->order[0]['dir'] ?? 'desc';
        $orderCol    = $columns[$orderColIdx] ?? 'id';

        $query->orderBy($orderCol, $orderDir);

        $start  = $request->start  ?? 0;
        $length = $request->length ?? 10;

        $data = $query->skip($start)->take($length)->get();

        $formatted = $data->map(fn($p) => [
            'id'                    => $p->id,
            'nomor_izin'            => $p->nomor_izin,
            'santri_id'             => $p->santri_id,
            'santri_nama'           => $p->santri?->nama_lengkap ?? '—',
            'santri_nis'            => $p->santri?->nis ?? '—',
            'jenis_izin'            => $p->jenis_izin,
            'jenis_label'           => $p->jenis_label,
            'tanggal_mulai'         => $p->tanggal_mulai?->format('Y-m-d'),
            'tanggal_mulai_fmt'     => $p->tanggal_mulai?->format('d M Y'),
            'tanggal_selesai'       => $p->tanggal_selesai?->format('Y-m-d'),
            'tanggal_selesai_fmt'   => $p->tanggal_selesai?->format('d M Y'),
            'durasi_hari'           => $p->durasi_hari,
            'waktu_keluar'          => $p->waktu_keluar,
            'waktu_kembali'         => $p->waktu_kembali,
            'keperluan'             => $p->keperluan,
            'tujuan'                => $p->tujuan,
            'penjemput_nama'        => $p->penjemput_nama,
            'penjemput_hubungan'    => $p->penjemput_hubungan,
            'penjemput_telepon'     => $p->penjemput_telepon,
            'penjemput_identitas'   => $p->penjemput_identitas,
            'status'                => $p->status,
            'status_label'          => $p->status_label,
            'disetujui_nama'        => $p->disetujuiOleh?->name ?? '—',
            'waktu_persetujuan'     => $p->waktu_persetujuan?->format('d M Y H:i'),
            'catatan_persetujuan'   => $p->catatan_persetujuan,
            'waktu_kembali_aktual'  => $p->waktu_kembali_aktual?->format('d M Y H:i'),
            'keterangan'            => $p->keterangan,
            'created_at'            => $p->created_at?->format('d M Y H:i'),
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
            $perizinan = Perizinan::create([
                'nomor_izin'         => Perizinan::generateNomorIzin(),
                'santri_id'          => $request->santri_id,
                'jenis_izin'         => $request->jenis_izin,
                'tanggal_mulai'      => $request->tanggal_mulai,
                'tanggal_selesai'    => $request->tanggal_selesai,
                'waktu_keluar'       => $request->waktu_keluar,
                'waktu_kembali'      => $request->waktu_kembali,
                'keperluan'          => $request->keperluan,
                'tujuan'             => $request->tujuan,
                'penjemput_nama'     => $request->penjemput_nama,
                'penjemput_hubungan' => $request->penjemput_hubungan,
                'penjemput_telepon'  => $request->penjemput_telepon,
                'penjemput_identitas'=> $request->penjemput_identitas,
                'status'             => 'diajukan',
                'keterangan'         => $request->keterangan,
            ]);

            return response()->json(['success' => true, 'message' => 'Perizinan berhasil diajukan', 'data' => $perizinan->load('santri')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        $p = Perizinan::with(['santri', 'disetujuiOleh'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data'    => [
                'id'                   => $p->id,
                'nomor_izin'           => $p->nomor_izin,
                'santri_id'            => $p->santri_id,
                'santri_nama'          => $p->santri?->nama_lengkap,
                'santri_nis'           => $p->santri?->nis,
                'jenis_izin'           => $p->jenis_izin,
                'tanggal_mulai'        => $p->tanggal_mulai?->format('Y-m-d'),
                'tanggal_selesai'      => $p->tanggal_selesai?->format('Y-m-d'),
                'waktu_keluar'         => $p->waktu_keluar,
                'waktu_kembali'        => $p->waktu_kembali,
                'keperluan'            => $p->keperluan,
                'tujuan'               => $p->tujuan,
                'penjemput_nama'       => $p->penjemput_nama,
                'penjemput_hubungan'   => $p->penjemput_hubungan,
                'penjemput_telepon'    => $p->penjemput_telepon,
                'penjemput_identitas'  => $p->penjemput_identitas,
                'status'               => $p->status,
                'catatan_persetujuan'  => $p->catatan_persetujuan,
                'waktu_kembali_aktual' => $p->waktu_kembali_aktual?->format('Y-m-d\TH:i'),
                'keterangan'           => $p->keterangan,
            ],
        ]);
    }

    public function update(Request $request, $id)
    {
        $perizinan = Perizinan::findOrFail($id);

        // Block editing approved/done records unless admin
        if (in_array($perizinan->status, ['disetujui', 'selesai'])) {
            return response()->json(['success' => false, 'message' => 'Perizinan yang sudah disetujui tidak dapat diubah melalui form ini. Gunakan fitur persetujuan.'], 422);
        }

        $validator = Validator::make($request->all(), $this->rules($id), $this->messages());

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validasi gagal', 'errors' => $validator->errors()], 422);
        }

        try {
            $perizinan->update([
                'santri_id'           => $request->santri_id,
                'jenis_izin'          => $request->jenis_izin,
                'tanggal_mulai'       => $request->tanggal_mulai,
                'tanggal_selesai'     => $request->tanggal_selesai,
                'waktu_keluar'        => $request->waktu_keluar,
                'waktu_kembali'       => $request->waktu_kembali,
                'keperluan'           => $request->keperluan,
                'tujuan'              => $request->tujuan,
                'penjemput_nama'      => $request->penjemput_nama,
                'penjemput_hubungan'  => $request->penjemput_hubungan,
                'penjemput_telepon'   => $request->penjemput_telepon,
                'penjemput_identitas' => $request->penjemput_identitas,
                'keterangan'          => $request->keterangan,
            ]);

            return response()->json(['success' => true, 'message' => 'Perizinan berhasil diperbarui', 'data' => $perizinan]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Approve or reject a perizinan
     */
    public function approve(Request $request, $id)
    {
        $perizinan = Perizinan::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'action'               => 'required|in:disetujui,ditolak',
            'catatan_persetujuan'  => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validasi gagal', 'errors' => $validator->errors()], 422);
        }

        try {
            $perizinan->update([
                'status'              => $request->action,
                'disetujui_oleh'      => Auth::id(),
                'waktu_persetujuan'   => now(),
                'catatan_persetujuan' => $request->catatan_persetujuan,
            ]);

            $label = $request->action === 'disetujui' ? 'disetujui' : 'ditolak';

            return response()->json(['success' => true, 'message' => "Perizinan berhasil {$label}"]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Mark perizinan as selesai (santri returned)
     */
    public function selesai(Request $request, $id)
    {
        $perizinan = Perizinan::findOrFail($id);

        if ($perizinan->status !== 'disetujui') {
            return response()->json(['success' => false, 'message' => 'Hanya perizinan berstatus "Disetujui" yang dapat diselesaikan.'], 422);
        }

        try {
            $perizinan->update([
                'status'               => 'selesai',
                'waktu_kembali_aktual' => $request->waktu_kembali_aktual ?? now(),
                'keterangan'           => $request->keterangan,
            ]);

            return response()->json(['success' => true, 'message' => 'Santri telah kembali. Perizinan diselesaikan.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        $perizinan = Perizinan::findOrFail($id);

        try {
            $perizinan->delete();
            return response()->json(['success' => true, 'message' => 'Perizinan berhasil dihapus']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    private function rules($ignoreId = null): array
    {
        return [
            'santri_id'           => 'required|exists:santri,id',
            'jenis_izin'          => 'required|in:pulang,kunjungan,sakit,keluar_sementara',
            'tanggal_mulai'       => 'required|date',
            'tanggal_selesai'     => 'required|date|after_or_equal:tanggal_mulai',
            'waktu_keluar'        => 'nullable|date_format:H:i',
            'waktu_kembali'       => 'nullable|date_format:H:i',
            'keperluan'           => 'required|string',
            'tujuan'              => 'nullable|string|max:200',
            'penjemput_nama'      => 'nullable|string|max:100',
            'penjemput_hubungan'  => 'nullable|string|max:50',
            'penjemput_telepon'   => 'nullable|string|max:20',
            'penjemput_identitas' => 'nullable|string|max:50',
            'keterangan'          => 'nullable|string',
        ];
    }

    private function messages(): array
    {
        return [
            'santri_id.required'          => 'Santri harus dipilih',
            'santri_id.exists'            => 'Santri tidak ditemukan',
            'jenis_izin.required'         => 'Jenis izin harus dipilih',
            'tanggal_mulai.required'      => 'Tanggal mulai harus diisi',
            'tanggal_selesai.required'    => 'Tanggal selesai harus diisi',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai tidak boleh sebelum tanggal mulai',
            'keperluan.required'          => 'Keperluan/alasan harus diisi',
        ];
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
}