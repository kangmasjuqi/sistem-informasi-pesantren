<?php

namespace App\Http\Controllers;

use App\Models\PenghuniKamar;
use App\Models\Kamar;
use App\Models\Santri;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PenghuniKamarController extends Controller
{
    public function index()
    {
        $kamars  = Kamar::active()->with('gedung')->orderBy('nomor_kamar')->get();
        // Note: $santris no longer passed — santri search handled via Select2 AJAX
        return view('penghuni-kamar.index', compact('kamars'));
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
        $query = PenghuniKamar::with(['santri', 'kamar.gedung']);

        // Global search
        if (!empty($request->search['value'])) {
            $search = $request->search['value'];
            $query->where(function ($q) use ($search) {
                $q->whereHas('santri', fn($s) => $s->where('nama_lengkap', 'like', "%{$search}%")
                                                    ->orWhere('nis', 'like', "%{$search}%"))
                  ->orWhereHas('kamar', fn($k) => $k->where('nomor_kamar', 'like', "%{$search}%"));
            });
        }

        // Column filters
        if (!empty($request->kamar_id)) {
            $query->where('kamar_id', $request->kamar_id);
        }
        if (!empty($request->santri_id)) {
            $query->where('santri_id', $request->santri_id);
        }
        if (!empty($request->status)) {
            $query->where('status', $request->status);
        }
        if (!empty($request->gedung_id)) {
            $query->whereHas('kamar', fn($k) => $k->where('gedung_id', $request->gedung_id));
        }
        if (!empty($request->tanggal_dari)) {
            $query->whereDate('tanggal_masuk', '>=', $request->tanggal_dari);
        }
        if (!empty($request->tanggal_sampai)) {
            $query->whereDate('tanggal_masuk', '<=', $request->tanggal_sampai);
        }

        $totalRecords    = PenghuniKamar::count();
        $filteredRecords = $query->count();

        // Ordering
        $columns     = ['id', 'santri_id', 'kamar_id', 'tanggal_masuk', 'tanggal_keluar', 'status'];
        $orderColIdx = $request->order[0]['column'] ?? 0;
        $orderDir    = $request->order[0]['dir'] ?? 'desc';
        $orderCol    = $columns[$orderColIdx] ?? 'id';

        $query->orderBy($orderCol, $orderDir);

        $start  = $request->start  ?? 0;
        $length = $request->length ?? 10;

        $data = $query->skip($start)->take($length)->get();

        $formatted = $data->map(fn($ph) => [
            'id'                  => $ph->id,
            'santri_id'           => $ph->santri_id,
            'santri_nama'         => $ph->santri?->nama_lengkap ?? '—',
            'santri_nis'          => $ph->santri?->nis ?? '—',
            'kamar_id'            => $ph->kamar_id,
            'kamar_nomor'         => $ph->kamar?->nomor_kamar ?? '—',
            'kamar_nama'          => $ph->kamar?->nama_kamar,
            'gedung_nama'         => $ph->kamar?->gedung?->nama_gedung ?? '—',
            'tanggal_masuk'       => $ph->tanggal_masuk?->format('Y-m-d'),
            'tanggal_masuk_fmt'   => $ph->tanggal_masuk?->format('d M Y'),
            'tanggal_keluar'      => $ph->tanggal_keluar?->format('Y-m-d'),
            'tanggal_keluar_fmt'  => $ph->tanggal_keluar?->format('d M Y'),
            'durasi_hari'         => $ph->durasi_hari,
            'status'              => $ph->status,
            'status_label'        => $ph->status_label,
            'keterangan'          => $ph->keterangan,
            'created_at'          => $ph->created_at?->format('d M Y H:i'),
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

        // Check kapasitas
        $kamar       = Kamar::findOrFail($request->kamar_id);
        $penghuniNow = $kamar->penghuniAktif()->count();

        if ($penghuniNow >= $kamar->kapasitas) {
            return response()->json(['success' => false, 'message' => "Kamar {$kamar->nomor_kamar} sudah penuh (kapasitas: {$kamar->kapasitas})."], 422);
        }

        // Check if santri already has active kamar
        $existing = PenghuniKamar::where('santri_id', $request->santri_id)
                                  ->where('status', 'aktif')->first();

        if ($existing) {
            return response()->json(['success' => false, 'message' => 'Santri ini sudah memiliki kamar aktif. Pindahkan atau keluarkan terlebih dahulu.'], 422);
        }

        try {
            $ph = PenghuniKamar::create([
                'santri_id'      => $request->santri_id,
                'kamar_id'       => $request->kamar_id,
                'tanggal_masuk'  => $request->tanggal_masuk,
                'tanggal_keluar' => $request->tanggal_keluar,
                'status'         => $request->status ?? 'aktif',
                'keterangan'     => $request->keterangan,
            ]);

            return response()->json(['success' => true, 'message' => 'Penghuni berhasil ditambahkan', 'data' => $ph->load(['santri', 'kamar'])]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        $ph = PenghuniKamar::with(['santri', 'kamar.gedung'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data'    => [
                'id'             => $ph->id,
                'santri_id'      => $ph->santri_id,
                'santri_nama'    => $ph->santri?->nama_lengkap,
                'santri_nis'     => $ph->santri?->nis,
                'kamar_id'       => $ph->kamar_id,
                'tanggal_masuk'  => $ph->tanggal_masuk?->format('Y-m-d'),
                'tanggal_keluar' => $ph->tanggal_keluar?->format('Y-m-d'),
                'status'         => $ph->status,
                'keterangan'     => $ph->keterangan,
            ],
        ]);
    }

    public function update(Request $request, $id)
    {
        $ph = PenghuniKamar::findOrFail($id);

        $validator = Validator::make($request->all(), $this->rules($id), $this->messages());

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validasi gagal', 'errors' => $validator->errors()], 422);
        }

        // If moving to a different kamar, re-check capacity
        if ($request->kamar_id != $ph->kamar_id) {
            $kamar       = Kamar::findOrFail($request->kamar_id);
            $penghuniNow = $kamar->penghuniAktif()->count();
            if ($penghuniNow >= $kamar->kapasitas) {
                return response()->json(['success' => false, 'message' => "Kamar tujuan {$kamar->nomor_kamar} sudah penuh."], 422);
            }
        }

        try {
            $ph->update([
                'santri_id'      => $request->santri_id,
                'kamar_id'       => $request->kamar_id,
                'tanggal_masuk'  => $request->tanggal_masuk,
                'tanggal_keluar' => $request->tanggal_keluar,
                'status'         => $request->status,
                'keterangan'     => $request->keterangan,
            ]);

            return response()->json(['success' => true, 'message' => 'Data penghuni berhasil diperbarui', 'data' => $ph]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        $ph = PenghuniKamar::findOrFail($id);

        try {
            $ph->delete();
            return response()->json(['success' => true, 'message' => 'Data penghuni berhasil dihapus']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    private function rules($ignoreId = null): array
    {
        return [
            'santri_id'      => 'required|exists:santri,id',
            'kamar_id'       => 'required|exists:kamar,id',
            'tanggal_masuk'  => 'required|date',
            'tanggal_keluar' => 'nullable|date|after_or_equal:tanggal_masuk',
            'status'         => 'required|in:aktif,keluar,pindah',
            'keterangan'     => 'nullable|string',
        ];
    }

    private function messages(): array
    {
        return [
            'santri_id.required'     => 'Santri harus dipilih',
            'santri_id.exists'       => 'Santri tidak ditemukan',
            'kamar_id.required'      => 'Kamar harus dipilih',
            'kamar_id.exists'        => 'Kamar tidak ditemukan',
            'tanggal_masuk.required' => 'Tanggal masuk harus diisi',
            'tanggal_keluar.after_or_equal' => 'Tanggal keluar tidak boleh sebelum tanggal masuk',
            'status.required'        => 'Status harus dipilih',
        ];
    }
}