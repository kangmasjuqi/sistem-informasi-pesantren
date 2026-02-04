<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display the user index page
     */
    public function index()
    {
        $roles = Role::where('is_active', 1)->orderBy('nama')->get();
        return view('users.index', compact('roles'));
    }

    /**
     * Get user data for DataTables (AJAX)
     */
    public function getData(Request $request)
    {
        $query = User::with('roles')->select('users.*');

        // Search functionality
        if ($request->has('search') && $request->search['value']) {
            $searchValue = $request->search['value'];
            $query->where(function($q) use ($searchValue) {
                $q->where('name', 'like', "%{$searchValue}%")
                  ->orWhere('nama_lengkap', 'like', "%{$searchValue}%")
                  ->orWhere('email', 'like', "%{$searchValue}%")
                  ->orWhere('username', 'like', "%{$searchValue}%")
                  ->orWhere('telepon', 'like', "%{$searchValue}%");
            });
        }

        // Column search
        if ($request->has('name') && $request->name) {
            $query->where('nama_lengkap', 'like', "%{$request->name}%");
        }

        if ($request->has('email') && $request->email) {
            $query->where('email', 'like', "%{$request->email}%");
        }

        if ($request->has('role_id') && $request->role_id) {
            $query->whereHas('roles', function($q) use ($request) {
                $q->where('roles.id', $request->role_id);
            });
        }

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Get total records before pagination
        $totalRecords = User::count();
        $filteredRecords = $query->count();

        // Ordering
        $orderColumnIndex = $request->order[0]['column'] ?? 0;
        $orderDirection = $request->order[0]['dir'] ?? 'desc';
        
        $columns = ['id', 'name', 'email', 'username', 'status', 'created_at'];
        $orderColumn = $columns[$orderColumnIndex] ?? 'id';
        
        $query->orderBy($orderColumn, $orderDirection);

        // Pagination
        $start = $request->start ?? 0;
        $length = $request->length ?? 10;
        
        $data = $query->skip($start)->take($length)->get();

        // Format data for DataTables
        $formattedData = $data->map(function($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'nama_lengkap' => $user->nama_lengkap,
                'email' => $user->email,
                'username' => $user->username,
                'telepon' => $user->telepon ?? '-',
                'roles' => $user->roles->pluck('nama')->toArray(),
                'role_codes' => $user->roles->pluck('kode')->toArray(),
                'status' => $user->status,
                'last_login_at' => $user->last_login_at ? $user->last_login_at->format('d M Y H:i') : 'Belum pernah login',
                'created_at' => $user->created_at->format('d M Y H:i'),
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
     * Store a newly created user
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'nama_lengkap' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'username' => 'required|string|max:50|unique:users,username',
            'password' => 'required|string|min:8|confirmed',
            'telepon' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'status' => 'required|in:aktif,tidak_aktif,banned',
            'roles' => 'required|array|min:1',
            'roles.*' => 'exists:roles,id',
        ], [
            'name.required' => 'Nama harus diisi',
            'nama_lengkap.required' => 'Nama lengkap harus diisi',
            'email.required' => 'Email harus diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah digunakan',
            'username.required' => 'Username harus diisi',
            'username.unique' => 'Username sudah digunakan',
            'password.required' => 'Password harus diisi',
            'password.min' => 'Password minimal 8 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
            'roles.required' => 'Minimal pilih 1 role',
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

            $user = User::create([
                'name' => $request->name,
                'nama_lengkap' => $request->nama_lengkap,
                'email' => $request->email,
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'telepon' => $request->telepon,
                'alamat' => $request->alamat,
                'status' => $request->status,
            ]);

            // Attach roles
            $user->roles()->attach($request->roles);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'User berhasil ditambahkan',
                'data' => $user
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
     * Display the specified user
     */
    public function show($id)
    {
        $user = User::with('roles')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'nama_lengkap' => $user->nama_lengkap,
                'email' => $user->email,
                'username' => $user->username,
                'telepon' => $user->telepon,
                'alamat' => $user->alamat,
                'status' => $user->status,
                'roles' => $user->roles->pluck('id')->toArray(),
            ]
        ]);
    }

    /**
     * Update the specified user
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'nama_lengkap' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'username' => ['required', 'string', 'max:50', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8|confirmed',
            'telepon' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'status' => 'required|in:aktif,tidak_aktif,banned',
            'roles' => 'required|array|min:1',
            'roles.*' => 'exists:roles,id',
        ], [
            'name.required' => 'Nama harus diisi',
            'nama_lengkap.required' => 'Nama lengkap harus diisi',
            'email.required' => 'Email harus diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah digunakan',
            'username.required' => 'Username harus diisi',
            'username.unique' => 'Username sudah digunakan',
            'password.min' => 'Password minimal 8 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
            'roles.required' => 'Minimal pilih 1 role',
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

            $updateData = [
                'name' => $request->name,
                'nama_lengkap' => $request->nama_lengkap,
                'email' => $request->email,
                'username' => $request->username,
                'telepon' => $request->telepon,
                'alamat' => $request->alamat,
                'status' => $request->status,
            ];

            // Only update password if provided
            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($request->password);
            }

            $user->update($updateData);

            // Sync roles
            $user->roles()->sync($request->roles);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'User berhasil diperbarui',
                'data' => $user
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
     * Remove the specified user
     */
    public function destroy($id)
    {
        try {
            // Prevent deleting own account
            if (auth()->id() == $id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak dapat menghapus akun sendiri'
                ], 403);
            }

            $user = User::findOrFail($id);
            
            // Check if user is SuperAdmin
            if ($user->hasRole('SUPERADMIN')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak dapat menghapus SuperAdmin'
                ], 403);
            }

            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'User berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reset user password
     */
    public function resetPassword(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required|string|min:8|confirmed',
        ], [
            'password.required' => 'Password harus diisi',
            'password.min' => 'Password minimal 8 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = User::findOrFail($id);
            
            $user->update([
                'password' => Hash::make($request->password)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Password berhasil direset'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}