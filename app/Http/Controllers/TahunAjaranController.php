<?php

namespace App\Http\Controllers;

use App\Models\TahunAjaran;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class TahunAjaranController extends Controller
{
    /**
     * Display the tahun ajaran & semester index page
     */
    public function index()
    {
        return view('tahun-ajaran.index');
    }

    /**
     * Get paginated data for DataTable
     */
    public function getData(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $search = $request->get('search', '');

        $query = TahunAjaran::with(['semesters' => function($q) {
            $q->orderBy('jenis_semester', 'asc');
        }]);

        // Search functionality
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('tahun_mulai', 'like', "%{$search}%")
                  ->orWhere('tahun_selesai', 'like', "%{$search}%")
                  ->orWhere('keterangan', 'like', "%{$search}%");
            });
        }

        $tahunAjaran = $query->orderBy('tahun_mulai', 'desc')
                             ->orderBy('created_at', 'desc')
                             ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $tahunAjaran
        ]);
    }

    /**
     * Store a newly created tahun ajaran with semesters
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:50|unique:tahun_ajaran,nama',
            'tahun_mulai' => 'required|integer|min:2000|max:2100',
            'tahun_selesai' => 'required|integer|min:2000|max:2100|gt:tahun_mulai',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'is_active' => 'required|boolean',
            'keterangan' => 'nullable|string',
            
            // Semester Ganjil
            'semester_ganjil_tanggal_mulai' => 'required|date',
            'semester_ganjil_tanggal_selesai' => 'required|date|after:semester_ganjil_tanggal_mulai',
            'semester_ganjil_is_active' => 'required|boolean',
            'semester_ganjil_keterangan' => 'nullable|string',
            
            // Semester Genap
            'semester_genap_tanggal_mulai' => 'required|date|after:semester_ganjil_tanggal_selesai',
            'semester_genap_tanggal_selesai' => 'required|date|after:semester_genap_tanggal_mulai',
            'semester_genap_is_active' => 'required|boolean',
            'semester_genap_keterangan' => 'nullable|string',
        ], [
            'nama.required' => 'Nama tahun ajaran harus diisi',
            'nama.unique' => 'Nama tahun ajaran sudah digunakan',
            'tahun_mulai.required' => 'Tahun mulai harus diisi',
            'tahun_selesai.required' => 'Tahun selesai harus diisi',
            'tahun_selesai.gt' => 'Tahun selesai harus lebih besar dari tahun mulai',
            'tanggal_mulai.required' => 'Tanggal mulai harus diisi',
            'tanggal_selesai.required' => 'Tanggal selesai harus diisi',
            'tanggal_selesai.after' => 'Tanggal selesai harus setelah tanggal mulai',
            'is_active.required' => 'Status aktif harus dipilih',
            
            'semester_ganjil_tanggal_mulai.required' => 'Tanggal mulai semester ganjil harus diisi',
            'semester_ganjil_tanggal_selesai.required' => 'Tanggal selesai semester ganjil harus diisi',
            'semester_ganjil_tanggal_selesai.after' => 'Tanggal selesai semester ganjil harus setelah tanggal mulai',
            
            'semester_genap_tanggal_mulai.required' => 'Tanggal mulai semester genap harus diisi',
            'semester_genap_tanggal_mulai.after' => 'Tanggal mulai semester genap harus setelah semester ganjil selesai',
            'semester_genap_tanggal_selesai.required' => 'Tanggal selesai semester genap harus diisi',
            'semester_genap_tanggal_selesai.after' => 'Tanggal selesai semester genap harus setelah tanggal mulai',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();

        try {
            // If this tahun ajaran is set to active, deactivate others
            if ($request->is_active) {
                TahunAjaran::where('is_active', true)->update(['is_active' => false]);
            }

            // Create Tahun Ajaran
            $tahunAjaran = TahunAjaran::create([
                'nama' => $request->nama,
                'tahun_mulai' => $request->tahun_mulai,
                'tahun_selesai' => $request->tahun_selesai,
                'tanggal_mulai' => $request->tanggal_mulai,
                'tanggal_selesai' => $request->tanggal_selesai,
                'is_active' => $request->is_active,
                'keterangan' => $request->keterangan,
            ]);

            // Deactivate all semesters if any semester is being set to active
            if ($request->semester_ganjil_is_active || $request->semester_genap_is_active) {
                Semester::where('is_active', true)->update(['is_active' => false]);
            }

            // Create Semester Ganjil
            $semesterGanjil = Semester::create([
                'tahun_ajaran_id' => $tahunAjaran->id,
                'jenis_semester' => 'ganjil',
                'nama' => 'Semester Ganjil ' . $request->nama,
                'tanggal_mulai' => $request->semester_ganjil_tanggal_mulai,
                'tanggal_selesai' => $request->semester_ganjil_tanggal_selesai,
                'is_active' => $request->semester_ganjil_is_active,
                'keterangan' => $request->semester_ganjil_keterangan,
            ]);

            // Create Semester Genap
            $semesterGenap = Semester::create([
                'tahun_ajaran_id' => $tahunAjaran->id,
                'jenis_semester' => 'genap',
                'nama' => 'Semester Genap ' . $request->nama,
                'tanggal_mulai' => $request->semester_genap_tanggal_mulai,
                'tanggal_selesai' => $request->semester_genap_tanggal_selesai,
                'is_active' => $request->semester_genap_is_active,
                'keterangan' => $request->semester_genap_keterangan,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Tahun ajaran dan semester berhasil ditambahkan',
                'data' => $tahunAjaran->load('semesters')
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
     * Display the specified tahun ajaran with semesters
     */
    public function show($id)
    {
        $tahunAjaran = TahunAjaran::with('semesters')->findOrFail($id);

        $semesterGanjil = $tahunAjaran->semesters->where('jenis_semester', 'ganjil')->first();
        $semesterGenap = $tahunAjaran->semesters->where('jenis_semester', 'genap')->first();

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $tahunAjaran->id,
                'nama' => $tahunAjaran->nama,
                'tahun_mulai' => $tahunAjaran->tahun_mulai,
                'tahun_selesai' => $tahunAjaran->tahun_selesai,
                'tanggal_mulai' => optional($tahunAjaran->tanggal_mulai)->format('Y-m-d'),
                'tanggal_selesai' => optional($tahunAjaran->tanggal_selesai)->format('Y-m-d'),
                'is_active' => $tahunAjaran->is_active,
                'keterangan' => $tahunAjaran->keterangan,
                
                'semester_ganjil_id' => $semesterGanjil?->id,
                'semester_ganjil_tanggal_mulai' => optional($semesterGanjil->tanggal_mulai)->format('Y-m-d'),
                'semester_ganjil_tanggal_selesai' => optional($semesterGanjil->tanggal_selesai)->format('Y-m-d'),
                'semester_ganjil_is_active' => $semesterGanjil?->is_active ?? false,
                'semester_ganjil_keterangan' => $semesterGanjil?->keterangan,
                
                'semester_genap_id' => $semesterGenap?->id,
                'semester_genap_tanggal_mulai' => optional($semesterGenap->tanggal_mulai)->format('Y-m-d'),
                'semester_genap_tanggal_selesai' => optional($semesterGenap->tanggal_selesai)->format('Y-m-d'),
                'semester_genap_is_active' => $semesterGenap?->is_active ?? false,
                'semester_genap_keterangan' => $semesterGenap?->keterangan,
            ]
        ]);
    }

    /**
     * Update the specified tahun ajaran with semesters
     */
    public function update(Request $request, $id)
    {
        $tahunAjaran = TahunAjaran::with('semesters')->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'nama' => ['required', 'string', 'max:50', Rule::unique('tahun_ajaran')->ignore($tahunAjaran->id)],
            'tahun_mulai' => 'required|integer|min:2000|max:2100',
            'tahun_selesai' => 'required|integer|min:2000|max:2100|gt:tahun_mulai',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'is_active' => 'required|boolean',
            'keterangan' => 'nullable|string',
            
            // Semester Ganjil
            'semester_ganjil_tanggal_mulai' => 'required|date',
            'semester_ganjil_tanggal_selesai' => 'required|date|after:semester_ganjil_tanggal_mulai',
            'semester_ganjil_is_active' => 'required|boolean',
            'semester_ganjil_keterangan' => 'nullable|string',
            
            // Semester Genap
            'semester_genap_tanggal_mulai' => 'required|date|after:semester_ganjil_tanggal_selesai',
            'semester_genap_tanggal_selesai' => 'required|date|after:semester_genap_tanggal_mulai',
            'semester_genap_is_active' => 'required|boolean',
            'semester_genap_keterangan' => 'nullable|string',
        ], [
            'nama.required' => 'Nama tahun ajaran harus diisi',
            'nama.unique' => 'Nama tahun ajaran sudah digunakan',
            'tahun_mulai.required' => 'Tahun mulai harus diisi',
            'tahun_selesai.required' => 'Tahun selesai harus diisi',
            'tahun_selesai.gt' => 'Tahun selesai harus lebih besar dari tahun mulai',
            'tanggal_mulai.required' => 'Tanggal mulai harus diisi',
            'tanggal_selesai.required' => 'Tanggal selesai harus diisi',
            'tanggal_selesai.after' => 'Tanggal selesai harus setelah tanggal mulai',
            'is_active.required' => 'Status aktif harus dipilih',
            
            'semester_ganjil_tanggal_mulai.required' => 'Tanggal mulai semester ganjil harus diisi',
            'semester_ganjil_tanggal_selesai.required' => 'Tanggal selesai semester ganjil harus diisi',
            'semester_ganjil_tanggal_selesai.after' => 'Tanggal selesai semester ganjil harus setelah tanggal mulai',
            
            'semester_genap_tanggal_mulai.required' => 'Tanggal mulai semester genap harus diisi',
            'semester_genap_tanggal_mulai.after' => 'Tanggal mulai semester genap harus setelah semester ganjil selesai',
            'semester_genap_tanggal_selesai.required' => 'Tanggal selesai semester genap harus diisi',
            'semester_genap_tanggal_selesai.after' => 'Tanggal selesai semester genap harus setelah tanggal mulai',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();

        try {
            // If this tahun ajaran is set to active, deactivate others
            if ($request->is_active && !$tahunAjaran->is_active) {
                TahunAjaran::where('is_active', true)
                    ->where('id', '!=', $tahunAjaran->id)
                    ->update(['is_active' => false]);
            }

            // Update Tahun Ajaran
            $tahunAjaran->update([
                'nama' => $request->nama,
                'tahun_mulai' => $request->tahun_mulai,
                'tahun_selesai' => $request->tahun_selesai,
                'tanggal_mulai' => $request->tanggal_mulai,
                'tanggal_selesai' => $request->tanggal_selesai,
                'is_active' => $request->is_active,
                'keterangan' => $request->keterangan,
            ]);

            // Deactivate all other semesters if any semester is being set to active
            if ($request->semester_ganjil_is_active || $request->semester_genap_is_active) {
                Semester::where('is_active', true)
                    ->where('tahun_ajaran_id', '!=', $tahunAjaran->id)
                    ->update(['is_active' => false]);
            }

            // Update or Create Semester Ganjil
            $semesterGanjil = $tahunAjaran->semesters->where('jenis_semester', 'ganjil')->first();
            if ($semesterGanjil) {
                $semesterGanjil->update([
                    'nama' => 'Semester Ganjil ' . $request->nama,
                    'tanggal_mulai' => $request->semester_ganjil_tanggal_mulai,
                    'tanggal_selesai' => $request->semester_ganjil_tanggal_selesai,
                    'is_active' => $request->semester_ganjil_is_active,
                    'keterangan' => $request->semester_ganjil_keterangan,
                ]);
            }

            // Update or Create Semester Genap
            $semesterGenap = $tahunAjaran->semesters->where('jenis_semester', 'genap')->first();
            if ($semesterGenap) {
                $semesterGenap->update([
                    'nama' => 'Semester Genap ' . $request->nama,
                    'tanggal_mulai' => $request->semester_genap_tanggal_mulai,
                    'tanggal_selesai' => $request->semester_genap_tanggal_selesai,
                    'is_active' => $request->semester_genap_is_active,
                    'keterangan' => $request->semester_genap_keterangan,
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Tahun ajaran dan semester berhasil diperbarui',
                'data' => $tahunAjaran->fresh()->load('semesters')
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
     * Remove the specified tahun ajaran (soft delete)
     */
    public function destroy($id)
    {
        try {
            $tahunAjaran = TahunAjaran::findOrFail($id);
            
            // Check if this is the active tahun ajaran
            if ($tahunAjaran->is_active) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak dapat menghapus tahun ajaran yang sedang aktif'
                ], 422);
            }

            $tahunAjaran->delete(); // Soft delete will also cascade to semesters

            return response()->json([
                'success' => true,
                'message' => 'Tahun ajaran dan semester berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}