{{-- resources/views/pengajar/modal.blade.php --}}
<div class="modal fade" id="pengajarModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Tambah Pengajar Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="pengajarForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" id="pengajarId" name="_id">

                    {{-- ── 1. Identitas ── --}}
                    <div style="margin-bottom:1.25rem;">
                        <div style="font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#6b7280;margin-bottom:.75rem;padding-bottom:.4rem;border-bottom:1px solid #e5e7eb;">
                            Identitas Pengajar
                        </div>
                        <div class="form-row" style="grid-template-columns: repeat(4, 1fr);">
                            <div>
                                <label class="form-label">NIP <span class="badge badge-required">WAJIB</span></label>
                                <input type="text" class="form-control" id="nip" name="nip" required style="text-transform:uppercase;" placeholder="Nomor Induk Pengajar">
                            </div>
                            <div>
                                <label class="form-label">Nama Lengkap <span class="badge badge-required">WAJIB</span></label>
                                <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" required>
                            </div>
                            <div>
                                <label class="form-label">Jenis Kelamin <span class="badge badge-required">WAJIB</span></label>
                                <select class="form-select" id="jenis_kelamin" name="jenis_kelamin" required>
                                    <option value="">Pilih</option>
                                    <option value="laki-laki">Laki-laki</option>
                                    <option value="perempuan">Perempuan</option>
                                </select>
                            </div>
                            <div>
                                <label class="form-label">NIK <span class="badge badge-optional">Opsional</span></label>
                                <input type="text" class="form-control" id="nik" name="nik" maxlength="20">
                            </div>
                        </div>
                        <div class="form-row" style="grid-template-columns: repeat(4, 1fr);">
                            <div>
                                <label class="form-label">Tempat Lahir <span class="badge badge-required">WAJIB</span></label>
                                <input type="text" class="form-control" id="tempat_lahir" name="tempat_lahir" required>
                            </div>
                            <div>
                                <label class="form-label">Tanggal Lahir <span class="badge badge-required">WAJIB</span></label>
                                <input type="date" class="form-control" id="tanggal_lahir" name="tanggal_lahir" required>
                            </div>
                            <div>
                                <label class="form-label">Telepon <span class="badge badge-optional">Opsional</span></label>
                                <input type="text" class="form-control" id="telepon" name="telepon" placeholder="08xx...">
                            </div>
                            <div>
                                <label class="form-label">Email <span class="badge badge-optional">Opsional</span></label>
                                <input type="email" class="form-control" id="email" name="email">
                            </div>
                        </div>
                        <div class="form-row full">
                            <div>
                                <label class="form-label">Alamat Lengkap <span class="badge badge-required">WAJIB</span></label>
                                <textarea class="form-control" id="alamat_lengkap" name="alamat_lengkap" rows="2" required></textarea>
                            </div>
                        </div>
                    </div>

                    {{-- ── 2. Pendidikan & Keahlian ── --}}
                    <div style="margin-bottom:1.25rem;">
                        <div style="font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#6b7280;margin-bottom:.75rem;padding-bottom:.4rem;border-bottom:1px solid #e5e7eb;">
                            Pendidikan & Keahlian
                        </div>
                        <div class="form-row" style="grid-template-columns: repeat(4, 1fr);">
                            <div>
                                <label class="form-label">Pendidikan Terakhir <span class="badge badge-optional">Opsional</span></label>
                                <input type="text" class="form-control" id="pendidikan_terakhir" name="pendidikan_terakhir" placeholder="S1, S2, D3...">
                            </div>
                            <div>
                                <label class="form-label">Jurusan <span class="badge badge-optional">Opsional</span></label>
                                <input type="text" class="form-control" id="jurusan" name="jurusan">
                            </div>
                            <div>
                                <label class="form-label">Universitas <span class="badge badge-optional">Opsional</span></label>
                                <input type="text" class="form-control" id="universitas" name="universitas">
                            </div>
                            <div>
                                <label class="form-label">Tahun Lulus <span class="badge badge-optional">Opsional</span></label>
                                <input type="number" class="form-control" id="tahun_lulus" name="tahun_lulus" min="1970" max="{{ date('Y') }}" placeholder="{{ date('Y') }}">
                            </div>
                        </div>
                        <div class="form-row full">
                            <div>
                                <label class="form-label">Keahlian <span class="badge badge-optional">Opsional</span></label>
                                <input type="text" class="form-control" id="keahlian" name="keahlian" placeholder="Fiqih, Bahasa Arab, Matematika...">
                                <div class="form-text">Pisahkan dengan koma. Disimpan sebagai JSON array.</div>
                            </div>
                        </div>
                    </div>

                    {{-- ── 3. Kepegawaian ── --}}
                    <div style="margin-bottom:1.25rem;">
                        <div style="font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#6b7280;margin-bottom:.75rem;padding-bottom:.4rem;border-bottom:1px solid #e5e7eb;">
                            Kepegawaian
                        </div>
                        <div class="form-row" style="grid-template-columns: repeat(4, 1fr);">
                            <div>
                                <label class="form-label">Tanggal Bergabung <span class="badge badge-required">WAJIB</span></label>
                                <input type="date" class="form-control" id="tanggal_bergabung" name="tanggal_bergabung" required>
                            </div>
                            <div>
                                <label class="form-label">Tanggal Keluar <span class="badge badge-optional">Opsional</span></label>
                                <input type="date" class="form-control" id="tanggal_keluar" name="tanggal_keluar">
                                <div class="form-text">Kosongkan jika masih aktif.</div>
                            </div>
                            <div>
                                <label class="form-label">Status Kepegawaian <span class="badge badge-required">WAJIB</span></label>
                                <select class="form-select" id="status_kepegawaian" name="status_kepegawaian" required>
                                    <option value="tidak_tetap">Tidak Tetap</option>
                                    <option value="tetap">Tetap</option>
                                    <option value="honorer">Honorer</option>
                                </select>
                            </div>
                            <div>
                                <label class="form-label">Status <span class="badge badge-required">WAJIB</span></label>
                                <select class="form-select" id="status" name="status" required>
                                    <option value="aktif">✅ Aktif</option>
                                    <option value="non_aktif">⛔ Non Aktif</option>
                                    <option value="pensiun">🏁 Pensiun</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-row full">
                            <div>
                                <label class="form-label">Keterangan <span class="badge badge-optional">Opsional</span></label>
                                <textarea class="form-control" id="keterangan" name="keterangan" rows="2"></textarea>
                            </div>
                        </div>
                    </div>

                    {{-- ── 4. Foto & Akun ── --}}
                    <div>
                        <div style="font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#6b7280;margin-bottom:.75rem;padding-bottom:.4rem;border-bottom:1px solid #e5e7eb;">
                            Foto & Akun User
                        </div>
                        <div style="display:flex; gap:2rem; align-items:flex-start;">
                            <div style="flex-shrink:0; text-align:center;">
                                <div style="width:100px;height:100px;border-radius:50%;border:2px dashed #d1d5db;overflow:hidden;display:flex;align-items:center;justify-content:center;background:#f9fafb;">
                                    <img id="fotoPreview" src="" style="width:100%;height:100%;object-fit:cover;display:none;">
                                    <span id="fotoPlaceholder" style="color:#9ca3af;font-size:.75rem;text-align:center;padding:.5rem;">
                                        <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                        <br>Foto
                                    </span>
                                </div>
                                <div style="margin-top:.5rem;">
                                    <label class="form-label" style="font-size:.75rem;">Foto Profil</label>
                                    <input type="file" class="form-control form-control-sm" id="foto" name="foto" accept="image/*">
                                    <div class="form-text">JPG, PNG, WebP. Maks 2MB.</div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M15.854 5.146a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3-3a.5.5 0 1 1 .708-.708L8 11.293l6.646-6.647a.5.5 0 0 1 .708 0z"/></svg>
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>