<?php

namespace App\Http\Controllers;

use App\Models\Pengajar;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PengajarController extends Controller
{
    public function index()
    {
        return view('pengajar.index');
    }

    public function getData(Request $request)
    {
        $query = Pengajar::with('user');

        // Global search
        if (!empty($request->search['value'])) {
            $s = $request->search['value'];
            $query->where(function ($q) use ($s) {
                $q->where('nip', 'like', "%{$s}%")
                  ->orWhere('nama_lengkap', 'like', "%{$s}%")
                  ->orWhere('telepon', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%");
            });
        }

        // Column filters
        if (!empty($request->nip)) {
            $query->where('nip', 'like', "%{$request->nip}%");
        }
        if (!empty($request->nama_lengkap)) {
            $query->where('nama_lengkap', 'like', "%{$request->nama_lengkap}%");
        }
        if (!empty($request->jenis_kelamin)) {
            $query->where('jenis_kelamin', $request->jenis_kelamin);
        }
        if (!empty($request->status_kepegawaian)) {
            $query->where('status_kepegawaian', $request->status_kepegawaian);
        }
        if (!empty($request->status)) {
            $query->where('status', $request->status);
        }

        $totalRecords    = Pengajar::count();
        $filteredRecords = $query->count();

        $columns     = ['id', 'nip', 'nama_lengkap', 'jenis_kelamin', 'status_kepegawaian', 'tanggal_bergabung', 'status'];
        $orderColIdx = $request->order[0]['column'] ?? 2;
        $orderDir    = $request->order[0]['dir']    ?? 'asc';
        $orderCol    = $columns[$orderColIdx] ?? 'nama_lengkap';

        $query->orderBy($orderCol, $orderDir);

        $start  = $request->start  ?? 0;
        $length = $request->length ?? 10;

        $data = $query->skip($start)->take($length)->get();

        $formatted = $data->map(fn($p) => [
            'id'                  => $p->id,
            'nip'                 => $p->nip,
            'nama_lengkap'        => $p->nama_lengkap,
            'jenis_kelamin'       => $p->jenis_kelamin,
            'tempat_lahir'        => $p->tempat_lahir,
            'tanggal_lahir'       => $p->tanggal_lahir?->format('Y-m-d'),
            'tanggal_lahir_fmt'   => $p->tanggal_lahir?->format('d M Y'),
            'umur'                => $p->umur,
            'telepon'             => $p->telepon,
            'email'               => $p->email,
            'pendidikan_terakhir' => $p->pendidikan_terakhir,
            'universitas'         => $p->universitas,
            'keahlian'            => $p->keahlian ?? [],
            'foto_url'            => $p->foto ? Storage::url($p->foto) : null,
            'tanggal_bergabung'   => $p->tanggal_bergabung?->format('Y-m-d'),
            'tanggal_bergabung_fmt'=> $p->tanggal_bergabung?->format('d M Y'),
            'masa_kerja'          => $p->masa_kerja,
            'status_kepegawaian'  => $p->status_kepegawaian,
            'kepegawaian_label'   => $p->kepegawaian_label,
            'status'              => $p->status,
            'status_label'        => $p->status_label,
            'user_name'           => $p->user?->name,
            'keterangan'          => $p->keterangan,
            'created_at'          => $p->created_at?->format('d M Y H:i'),
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
                $fotoPath = $request->file('foto')->store('pengajar/foto', 'public');
            }

            $user_id = 0; // TODO create User first 

            $pengajar = Pengajar::create([
                'user_id'             => $user_id,
                'nip'                 => strtoupper(trim($request->nip)),
                'nama_lengkap'        => $request->nama_lengkap,
                'jenis_kelamin'       => $request->jenis_kelamin,
                'tempat_lahir'        => $request->tempat_lahir,
                'tanggal_lahir'       => $request->tanggal_lahir,
                'nik'                 => $request->nik,
                'alamat_lengkap'      => $request->alamat_lengkap,
                'telepon'             => $request->telepon,
                'email'               => $request->email,
                'pendidikan_terakhir' => $request->pendidikan_terakhir,
                'jurusan'             => $request->jurusan,
                'universitas'         => $request->universitas,
                'tahun_lulus'         => $request->tahun_lulus,
                'keahlian'            => $request->keahlian
                    ? array_filter(array_map('trim', explode(',', $request->keahlian)))
                    : null,
                'foto'                => $fotoPath,
                'tanggal_bergabung'   => $request->tanggal_bergabung,
                'tanggal_keluar'      => $request->tanggal_keluar,
                'status_kepegawaian'  => $request->status_kepegawaian,
                'status'              => $request->status,
                'keterangan'          => $request->keterangan,
            ]);

            return response()->json(['success' => true, 'message' => 'Pengajar berhasil ditambahkan', 'data' => $pengajar]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        $p = Pengajar::with('user')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data'    => [
                'id'                  => $p->id,
                'user_id'             => $p->user_id,
                'nip'                 => $p->nip,
                'nama_lengkap'        => $p->nama_lengkap,
                'jenis_kelamin'       => $p->jenis_kelamin,
                'tempat_lahir'        => $p->tempat_lahir,
                'tanggal_lahir'       => $p->tanggal_lahir?->format('Y-m-d'),
                'nik'                 => $p->nik,
                'alamat_lengkap'      => $p->alamat_lengkap,
                'telepon'             => $p->telepon,
                'email'               => $p->email,
                'pendidikan_terakhir' => $p->pendidikan_terakhir,
                'jurusan'             => $p->jurusan,
                'universitas'         => $p->universitas,
                'tahun_lulus'         => $p->tahun_lulus,
                'keahlian'            => $p->keahlian ? implode(', ', $p->keahlian) : '',
                'foto_url'            => $p->foto ? Storage::url($p->foto) : null,
                'tanggal_bergabung'   => $p->tanggal_bergabung?->format('Y-m-d'),
                'tanggal_keluar'      => $p->tanggal_keluar?->format('Y-m-d'),
                'status_kepegawaian'  => $p->status_kepegawaian,
                'status'              => $p->status,
                'keterangan'          => $p->keterangan,
            ],
        ]);
    }

    public function update(Request $request, $id)
    {
        $pengajar = Pengajar::findOrFail($id);

        $rules              = $this->rules();
        $rules['nip']       = ['required', 'string', 'max:20', Rule::unique('pengajar')->ignore($pengajar->id)];
        $rules['nik']       = ['nullable', 'string', 'max:20', Rule::unique('pengajar')->ignore($pengajar->id)];

        $validator = Validator::make($request->all(), $rules, $this->messages());

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validasi gagal', 'errors' => $validator->errors()], 422);
        }

        try {
            $fotoPath = $pengajar->foto;
            if ($request->hasFile('foto')) {
                if ($fotoPath) Storage::disk('public')->delete($fotoPath);
                $fotoPath = $request->file('foto')->store('pengajar/foto', 'public');
            }

            $pengajar->update([
                'nip'                 => strtoupper(trim($request->nip)),
                'nama_lengkap'        => $request->nama_lengkap,
                'jenis_kelamin'       => $request->jenis_kelamin,
                'tempat_lahir'        => $request->tempat_lahir,
                'tanggal_lahir'       => $request->tanggal_lahir,
                'nik'                 => $request->nik,
                'alamat_lengkap'      => $request->alamat_lengkap,
                'telepon'             => $request->telepon,
                'email'               => $request->email,
                'pendidikan_terakhir' => $request->pendidikan_terakhir,
                'jurusan'             => $request->jurusan,
                'universitas'         => $request->universitas,
                'tahun_lulus'         => $request->tahun_lulus,
                'keahlian'            => $request->keahlian
                    ? array_filter(array_map('trim', explode(',', $request->keahlian)))
                    : null,
                'foto'                => $fotoPath,
                'tanggal_bergabung'   => $request->tanggal_bergabung,
                'tanggal_keluar'      => $request->tanggal_keluar,
                'status_kepegawaian'  => $request->status_kepegawaian,
                'status'              => $request->status,
                'keterangan'          => $request->keterangan,
            ]);

            return response()->json(['success' => true, 'message' => 'Data pengajar berhasil diperbarui', 'data' => $pengajar]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        $pengajar = Pengajar::findOrFail($id);

        try {
            if ($pengajar->foto) Storage::disk('public')->delete($pengajar->foto);
            $pengajar->delete();
            return response()->json(['success' => true, 'message' => 'Pengajar berhasil dihapus']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    private function rules(): array
    {
        return [
            'nip'                 => 'required|string|max:20|unique:pengajar,nip',
            'nama_lengkap'        => 'required|string|max:255',
            'jenis_kelamin'       => 'required|in:laki-laki,perempuan',
            'tempat_lahir'        => 'required|string|max:100',
            'tanggal_lahir'       => 'required|date',
            'nik'                 => 'nullable|string|max:20|unique:pengajar,nik',
            'alamat_lengkap'      => 'required|string',
            'telepon'             => 'nullable|string|max:20',
            'email'               => 'nullable|email|max:100',
            'pendidikan_terakhir' => 'nullable|string|max:100',
            'jurusan'             => 'nullable|string|max:100',
            'universitas'         => 'nullable|string|max:150',
            'tahun_lulus'         => 'nullable|digits:4|integer|min:1970|max:' . date('Y'),
            'keahlian'            => 'nullable|string',
            'foto'                => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'tanggal_bergabung'   => 'required|date',
            'tanggal_keluar'      => 'nullable|date|after_or_equal:tanggal_bergabung',
            'status_kepegawaian'  => 'required|in:tetap,tidak_tetap,honorer',
            'status'              => 'required|in:aktif,non_aktif,pensiun',
            'keterangan'          => 'nullable|string',
        ];
    }

    private function messages(): array
    {
        return [
            'nip.required'               => 'NIP harus diisi',
            'nip.unique'                 => 'NIP sudah digunakan',
            'nama_lengkap.required'      => 'Nama lengkap harus diisi',
            'jenis_kelamin.required'     => 'Jenis kelamin harus dipilih',
            'tempat_lahir.required'      => 'Tempat lahir harus diisi',
            'tanggal_lahir.required'     => 'Tanggal lahir harus diisi',
            'nik.unique'                 => 'NIK sudah digunakan',
            'alamat_lengkap.required'    => 'Alamat lengkap harus diisi',
            'email.email'                => 'Format email tidak valid',
            'foto.image'                 => 'File harus berupa gambar',
            'foto.max'                   => 'Ukuran foto maksimal 2MB',
            'tanggal_bergabung.required' => 'Tanggal bergabung harus diisi',
            'tanggal_keluar.after_or_equal' => 'Tanggal keluar tidak boleh sebelum tanggal bergabung',
            'status_kepegawaian.required'=> 'Status kepegawaian harus dipilih',
            'status.required'            => 'Status harus dipilih',
        ];
    }
}