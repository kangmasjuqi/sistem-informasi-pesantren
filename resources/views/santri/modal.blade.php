{{-- resources/views/santri/modal.blade.php --}}
<div class="modal fade" id="santriModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Tambah Santri Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="santriForm" enctype="multipart/form-data">
                <div class="modal-body">
                    @csrf
                    <input type="hidden" id="santriId" name="_id">

                    {{-- ── 1. Identitas Utama ── --}}
                    <div style="margin-bottom:1.25rem;">
                        <div style="font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.08em; color:#6b7280; margin-bottom:.75rem; padding-bottom:.4rem; border-bottom:1px solid #e5e7eb;">
                            Identitas Utama
                        </div>
                        <div class="form-row" style="grid-template-columns: repeat(4, 1fr);">
                            <div>
                                <label class="form-label">NIS <span class="badge badge-required">WAJIB</span></label>
                                <input type="text" class="form-control" id="nis" name="nis" required style="text-transform:uppercase;" placeholder="Nomor Induk Santri">
                            </div>
                            <div>
                                <label class="form-label">NISN </label>
                                <input type="text" class="form-control" id="nisn" name="nisn" placeholder="Nomor Induk Siswa Nasional">
                            </div>
                            <div>
                                <label class="form-label">Nama Lengkap <span class="badge badge-required">WAJIB</span></label>
                                <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" required placeholder="Nama sesuai akta lahir">
                            </div>
                            <div>
                                <label class="form-label">Nama Panggilan </label>
                                <input type="text" class="form-control" id="nama_panggilan" name="nama_panggilan" placeholder="Nama sehari-hari">
                            </div>
                        </div>
                        <div class="form-row" style="grid-template-columns: repeat(4, 1fr);">
                            <div>
                                <label class="form-label">Jenis Kelamin <span class="badge badge-required">WAJIB</span></label>
                                <select class="form-select" id="jenis_kelamin" name="jenis_kelamin" required>
                                    <option value="">Pilih</option>
                                    <option value="laki-laki">Laki-laki</option>
                                    <option value="perempuan">Perempuan</option>
                                </select>
                            </div>
                            <div>
                                <label class="form-label">Tempat Lahir <span class="badge badge-required">WAJIB</span></label>
                                <input type="text" class="form-control" id="tempat_lahir" name="tempat_lahir" required>
                            </div>
                            <div>
                                <label class="form-label">Tanggal Lahir <span class="badge badge-required">WAJIB</span></label>
                                <input type="date" class="form-control" id="tanggal_lahir" name="tanggal_lahir" required>
                            </div>
                            <div>
                                <label class="form-label">NIK </label>
                                <input type="text" class="form-control" id="nik" name="nik" placeholder="16 digit" maxlength="20">
                            </div>
                        </div>
                        <div class="form-row" style="grid-template-columns: repeat(4, 1fr);">
                            <div>
                                <label class="form-label">Golongan Darah </label>
                                <select class="form-select" id="golongan_darah" name="golongan_darah">
                                    <option value="">Tidak Diketahui</option>
                                    @foreach(['A','B','AB','O','A+','A-','B+','B-','AB+','AB-','O+','O-'] as $gd)
                                        <option value="{{ $gd }}">{{ $gd }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="form-label">Telepon </label>
                                <input type="text" class="form-control" id="telepon" name="telepon" placeholder="08xx...">
                            </div>
                            <div>
                                <label class="form-label">Anak ke- </label>
                                <input type="number" class="form-control" id="anak_ke" name="anak_ke" min="1" placeholder="1">
                            </div>
                            <div>
                                <label class="form-label">Jumlah Saudara </label>
                                <input type="number" class="form-control" id="jumlah_saudara" name="jumlah_saudara" min="0" placeholder="0">
                            </div>
                        </div>
                    </div>

                    {{-- ── 2. Alamat ── --}}
                    <div style="margin-bottom:1.25rem;">
                        <div style="font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.08em; color:#6b7280; margin-bottom:.75rem; padding-bottom:.4rem; border-bottom:1px solid #e5e7eb;">
                            Alamat
                        </div>
                        <div class="form-row full">
                            <div>
                                <label class="form-label">Alamat Lengkap <span class="badge badge-required">WAJIB</span></label>
                                <textarea class="form-control" id="alamat_lengkap" name="alamat_lengkap" rows="2" required placeholder="Jalan, RT/RW, Nomor Rumah..."></textarea>
                            </div>
                        </div>
                        <div class="form-row" style="grid-template-columns: repeat(5, 1fr);">
                            <div>
                                <label class="form-label">Provinsi</label>
                                <input type="text" class="form-control" id="provinsi" name="provinsi">
                            </div>
                            <div>
                                <label class="form-label">Kabupaten/Kota</label>
                                <input type="text" class="form-control" id="kabupaten" name="kabupaten">
                            </div>
                            <div>
                                <label class="form-label">Kecamatan</label>
                                <input type="text" class="form-control" id="kecamatan" name="kecamatan">
                            </div>
                            <div>
                                <label class="form-label">Kelurahan/Desa</label>
                                <input type="text" class="form-control" id="kelurahan" name="kelurahan">
                            </div>
                            <div>
                                <label class="form-label">Kode Pos</label>
                                <input type="text" class="form-control" id="kode_pos" name="kode_pos" maxlength="10">
                            </div>
                        </div>
                    </div>

                    {{-- ── 3. Data Masuk & Status ── --}}
                    <div style="margin-bottom:1.25rem;">
                        <div style="font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.08em; color:#6b7280; margin-bottom:.75rem; padding-bottom:.4rem; border-bottom:1px solid #e5e7eb;">
                            Data Masuk & Status
                        </div>
                        <div class="form-row" style="grid-template-columns: repeat(3, 1fr);">
                            <div>
                                <label class="form-label">Tanggal Masuk <span class="badge badge-required">WAJIB</span></label>
                                <input type="date" class="form-control" id="tanggal_masuk" name="tanggal_masuk" required>
                            </div>
                            <div>
                                <label class="form-label">Tanggal Keluar </label>
                                <input type="date" class="form-control" id="tanggal_keluar" name="tanggal_keluar">
                                <div class="form-text">Kosongkan jika masih aktif.</div>
                            </div>
                            <div>
                                <label class="form-label">Status <span class="badge badge-required">WAJIB</span></label>
                                <select class="form-select" id="status" name="status" required>
                                    <option value="aktif">✅ Aktif</option>
                                    <option value="lulus">🎓 Lulus</option>
                                    <option value="pindah">🔄 Pindah</option>
                                    <option value="keluar">🚪 Keluar</option>
                                    <option value="cuti">⏸ Cuti</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- ── 4. Kesehatan & Catatan ── --}}
                    <div style="margin-bottom:1.25rem;">
                        <div style="font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.08em; color:#6b7280; margin-bottom:.75rem; padding-bottom:.4rem; border-bottom:1px solid #e5e7eb;">
                            Kesehatan & Catatan
                        </div>
                        <div class="form-row">
                            <div>
                                <label class="form-label">Riwayat Penyakit </label>
                                <textarea class="form-control" id="riwayat_penyakit" name="riwayat_penyakit" rows="2" placeholder="Alergi, kondisi kronis, dll..."></textarea>
                            </div>
                            <div>
                                <label class="form-label">Keterangan </label>
                                <textarea class="form-control" id="keterangan" name="keterangan" rows="2" placeholder="Catatan tambahan..."></textarea>
                            </div>
                        </div>
                    </div>

                    {{-- ── 5. Foto Profil ── --}}
                    <div style="margin-bottom:1.25rem;">
                        <div style="font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.08em; color:#6b7280; margin-bottom:.75rem; padding-bottom:.4rem; border-bottom:1px solid #e5e7eb;">
                            Foto Profil
                        </div>
                        <div style="display:flex; gap:1.5rem; align-items:flex-start;">
                            <div style="flex-shrink:0;">
                                <div style="width:100px; height:100px; border-radius:50%; border:2px dashed #d1d5db; overflow:hidden; display:flex; align-items:center; justify-content:center; background:#f9fafb;">
                                    <img id="fotoPreview" src="" style="width:100%;height:100%;object-fit:cover;display:none;">
                                    <span id="fotoPlaceholder" style="color:#9ca3af; font-size:.75rem; text-align:center; padding:.5rem;">
                                        <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                        <br>Foto
                                    </span>
                                </div>
                            </div>
                            <div style="flex:1;">
                                <label class="form-label">Upload Foto </label>
                                <input type="file" class="form-control" id="foto" name="foto" accept="image/jpg,image/jpeg,image/png,image/webp">
                                <div class="form-text">JPG, PNG, WebP. Maks 2MB.</div>
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