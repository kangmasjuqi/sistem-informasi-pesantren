{{-- resources/views/kelas/modal.blade.php --}}
<div class="modal fade" id="kelasModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Tambah Kelas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="kelasForm">
                <div class="modal-body">
                    <input type="hidden" id="kelasId">

                    {{-- ── 1. Tahun Ajaran & Identitas ── --}}
                    <div style="margin-bottom:1.25rem;">
                        <div style="font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#6b7280;margin-bottom:.75rem;padding-bottom:.4rem;border-bottom:1px solid #e5e7eb;">
                            Identitas Kelas
                        </div>
                        <div class="form-row" style="grid-template-columns: repeat(4, 1fr);">
                            <div style="grid-column: span 2;">
                                <label class="form-label">Tahun Ajaran <span class="badge badge-required">WAJIB</span></label>
                                <select class="form-select" id="tahun_ajaran_id" name="tahun_ajaran_id" required style="width:100%;">
                                    {{-- Populated via Select2 AJAX --}}
                                </select>
                                <div class="form-text">Ketik untuk mencari tahun ajaran.</div>
                            </div>
                            <div>
                                <label class="form-label">Nama Kelas <span class="badge badge-required">WAJIB</span></label>
                                <input type="text" class="form-control" id="nama_kelas" name="nama_kelas" placeholder="Contoh: 1A, Tahfidz 1" required maxlength="100">
                            </div>
                            <div>
                                <label class="form-label">Tingkat <span class="badge badge-required">WAJIB</span></label>
                                <select class="form-select" id="tingkat" name="tingkat" required>
                                    <option value="">Pilih Tingkat</option>
                                    <option value="1">Tingkat 1</option>
                                    <option value="2">Tingkat 2</option>
                                    <option value="3">Tingkat 3</option>
                                    <option value="Ibtidaiyah">Ibtidaiyah</option>
                                    <option value="Tsanawiyah">Tsanawiyah</option>
                                    <option value="Aliyah">Aliyah</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- ── 2. Wali Kelas & Kapasitas ── --}}
                    <div style="margin-bottom:1.25rem;">
                        <div style="font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#6b7280;margin-bottom:.75rem;padding-bottom:.4rem;border-bottom:1px solid #e5e7eb;">
                            Pengajar & Kapasitas
                        </div>
                        <div class="form-row" style="grid-template-columns: repeat(3, 1fr);">
                            <div style="grid-column: span 2;">
                                <label class="form-label">Wali Kelas</label>
                                <select class="form-select" id="wali_kelas_id" name="wali_kelas_id" style="width:100%;">
                                    {{-- Populated via Select2 AJAX --}}
                                </select>
                                <div class="form-text">Opsional. Ketik nama pengajar untuk mencari.</div>
                            </div>
                            <div>
                                <label class="form-label">Kapasitas</label>
                                <input type="number" class="form-control" id="kapasitas" name="kapasitas" min="1" max="200" value="30">
                                <div class="form-text">Maks. jumlah santri dalam kelas.</div>
                            </div>
                        </div>
                    </div>

                    {{-- ── 3. Status & Deskripsi ── --}}
                    <div>
                        <div style="font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#6b7280;margin-bottom:.75rem;padding-bottom:.4rem;border-bottom:1px solid #e5e7eb;">
                            Status & Keterangan
                        </div>
                        <div class="form-row">
                            <div>
                                <label class="form-label">Deskripsi</label>
                                <textarea class="form-control" id="deskripsi" name="deskripsi" rows="2" placeholder="Catatan atau keterangan tambahan..."></textarea>
                            </div>
                            <div>
                                <label class="form-label">Status Kelas</label>
                                <select class="form-select" id="is_active" name="is_active">
                                    <option value="1">✅ Aktif</option>
                                    <option value="0">⛔ Tidak Aktif</option>
                                </select>
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