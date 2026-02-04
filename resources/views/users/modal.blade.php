<!-- Modal Form -->
<div class="modal fade" id="userModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Tambah User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="userForm">
                <div class="modal-body">
                    <input type="hidden" id="userId" name="id">
                    
                    <div class="form-row">
                        <div>
                            <label class="form-label">
                                Nama
                                <span class="badge badge-required">WAJIB</span>
                            </label>
                            <input type="text" class="form-control" id="name" name="name" required placeholder="Nama singkat">
                        </div>

                        <div>
                            <label class="form-label">
                                Nama Lengkap
                                <span class="badge badge-required">WAJIB</span>
                            </label>
                            <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" required placeholder="Nama lengkap">
                        </div>
                    </div>

                    <div class="form-row">
                        <div>
                            <label class="form-label">
                                Email
                                <span class="badge badge-required">WAJIB</span>
                            </label>
                            <input type="email" class="form-control" id="email" name="email" required placeholder="user@example.com">
                        </div>

                        <div>
                            <label class="form-label">
                                Username
                                <span class="badge badge-required">WAJIB</span>
                            </label>
                            <input type="text" class="form-control" id="username" name="username" required placeholder="username">
                        </div>
                    </div>

                    <div class="form-row">
                        <div>
                            <label class="form-label">
                                Password
                                <span class="badge badge-required" id="passwordBadge">WAJIB</span>
                            </label>
                            <div class="password-wrapper">
                                <input type="password" class="form-control" id="password" name="password" placeholder="Minimal 8 karakter">
                                <span class="password-toggle" onclick="togglePassword('password')">👁️</span>
                            </div>
                            <div class="form-text" id="passwordHelp">Min. 8 karakter. Kosongkan jika tidak ingin mengubah password.</div>
                        </div>

                        <div>
                            <label class="form-label">
                                Konfirmasi Password
                                <span class="badge badge-required" id="passwordConfirmBadge">WAJIB</span>
                            </label>
                            <div class="password-wrapper">
                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Ulangi password">
                                <span class="password-toggle" onclick="togglePassword('password_confirmation')">👁️</span>
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div>
                            <label class="form-label">
                                Telepon
                                <span class="badge badge-optional">Opsional</span>
                            </label>
                            <input type="text" class="form-control" id="telepon" name="telepon" placeholder="08xxxxxxxxxx">
                        </div>

                        <div>
                            <label class="form-label">
                                Status
                                <span class="badge badge-required">WAJIB</span>
                            </label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="aktif">✅ Aktif</option>
                                <option value="tidak_aktif">⏸️ Tidak Aktif</option>
                                <option value="banned">🚫 Banned</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row full">
                        <div>
                            <label class="form-label">
                                Alamat
                                <span class="badge badge-optional">Opsional</span>
                            </label>
                            <textarea class="form-control" id="alamat" name="alamat" rows="2" placeholder="Alamat lengkap"></textarea>
                        </div>
                    </div>

                    <div class="form-row full">
                        <div>
                            <label class="form-label">
                                Role
                                <span class="badge badge-required">WAJIB (min. 1)</span>
                            </label>
                            <div class="roles-checkboxes" id="rolesContainer">
                                @foreach($roles as $role)
                                <div class="role-checkbox-item">
                                    <input type="checkbox" name="roles[]" value="{{ $role->id }}" id="role_{{ $role->id }}">
                                    <label for="role_{{ $role->id }}">{{ $role->nama }}</label>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btnSubmit">
                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M15.854 5.146a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3-3a.5.5 0 1 1 .708-.708L8 11.293l6.646-6.647a.5.5 0 0 1 .708 0z"/>
                        </svg>
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>