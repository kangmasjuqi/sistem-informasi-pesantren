{{-- resources/views/wali-santri/modal.blade.php --}}
<div class="modal fade" id="waliModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Tambah Wali Santri</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="waliForm">
                <div class="modal-body">
                    <input type="hidden" id="waliId">

                    {{-- ── 1. Link ke Santri & Jenis Wali ── --}}
                    <div style="margin-bottom:1.25rem;">
                        <div style="font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#6b7280;margin-bottom:.75rem;padding-bottom:.4rem;border-bottom:1px solid #e5e7eb;">
                            Relasi ke Santri
                        </div>
                        <div class="form-row">
                            <div>
                                <label class="form-label">Santri <span class="badge badge-required">WAJIB</span></label>
                                <select class="form-select" id="santri_id" name="santri_id" required style="width:100%;">
                                    {{-- Populated via Select2 AJAX --}}
                                </select>
                                <div class="form-text">Ketik minimal 2 huruf untuk mencari santri.</div>
                            </div>
                            <div>
                                <label class="form-label">Jenis Wali <span class="badge badge-required">WAJIB</span></label>
                                <select class="form-select" id="jenis_wali" name="jenis_wali" required>
                                    <option value="">Pilih Jenis</option>
                                    <option value="ayah">👨 Ayah</option>
                                    <option value="ibu">👩 Ibu</option>
                                    <option value="wali">🧑 Wali</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- ── 2. Identitas Wali ── --}}
                    <div style="margin-bottom:1.25rem;">
                        <div style="font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#6b7280;margin-bottom:.75rem;padding-bottom:.4rem;border-bottom:1px solid #e5e7eb;">
                            Identitas Wali
                        </div>
                        <div class="form-row" style="grid-template-columns: repeat(4, 1fr);">
                            <div style="grid-column: span 2;">
                                <label class="form-label">Nama Lengkap <span class="badge badge-required">WAJIB</span></label>
                                <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" required>
                            </div>
                            <div>
                                <label class="form-label">NIK </label>
                                <input type="text" class="form-control" id="nik" name="nik" maxlength="20">
                            </div>
                            <div>
                                <label class="form-label">Status <span class="badge badge-required">WAJIB</span></label>
                                <select class="form-select" id="status" name="status" required>
                                    <option value="hidup">✅ Hidup</option>
                                    <option value="meninggal">🕊️ Meninggal</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-row" style="grid-template-columns: repeat(4, 1fr);">
                            <div>
                                <label class="form-label">Tempat Lahir </label>
                                <input type="text" class="form-control" id="tempat_lahir" name="tempat_lahir">
                            </div>
                            <div>
                                <label class="form-label">Tanggal Lahir </label>
                                <input type="date" class="form-control" id="tanggal_lahir" name="tanggal_lahir">
                            </div>
                            <div>
                                <label class="form-label">Telepon </label>
                                <input type="text" class="form-control" id="telepon" name="telepon" placeholder="08xx...">
                            </div>
                            <div>
                                <label class="form-label">Email </label>
                                <input type="email" class="form-control" id="email" name="email">
                            </div>
                        </div>
                    </div>

                    {{-- ── 3. Pekerjaan & Ekonomi ── --}}
                    <div style="margin-bottom:1.25rem;">
                        <div style="font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#6b7280;margin-bottom:.75rem;padding-bottom:.4rem;border-bottom:1px solid #e5e7eb;">
                            Pekerjaan & Ekonomi
                        </div>
                        <div class="form-row" style="grid-template-columns: repeat(3, 1fr);">
                            <div>
                                <label class="form-label">Pendidikan Terakhir </label>
                                <input type="text" class="form-control" id="pendidikan_terakhir" name="pendidikan_terakhir" placeholder="SMA, S1, S2...">
                            </div>
                            <div>
                                <label class="form-label">Pekerjaan </label>
                                <input type="text" class="form-control" id="pekerjaan" name="pekerjaan" placeholder="PNS, Wiraswasta...">
                            </div>
                            <div>
                                <label class="form-label">Penghasilan/Bulan </label>
                                <div style="position:relative;">
                                    <span style="position:absolute;left:.75rem;top:50%;transform:translateY(-50%);color:#6b7280;font-size:.875rem;pointer-events:none;">Rp</span>
                                    <input type="text" class="form-control" id="penghasilan" name="penghasilan" style="padding-left:2.25rem;" placeholder="0">
                                </div>
                                <div class="form-text">Format: 1.500.000</div>
                            </div>
                        </div>
                    </div>

                    {{-- ── 4. Alamat & Catatan ── --}}
                    <div>
                        <div style="font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#6b7280;margin-bottom:.75rem;padding-bottom:.4rem;border-bottom:1px solid #e5e7eb;">
                            Alamat & Catatan
                        </div>
                        <div class="form-row">
                            <div>
                                <label class="form-label">Alamat </label>
                                <textarea class="form-control" id="alamat" name="alamat" rows="2" placeholder="Jika berbeda dengan alamat santri..."></textarea>
                            </div>
                            <div>
                                <label class="form-label">Keterangan </label>
                                <textarea class="form-control" id="keterangan" name="keterangan" rows="2"></textarea>
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