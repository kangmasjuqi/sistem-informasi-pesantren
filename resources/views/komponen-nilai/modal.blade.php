{{-- resources/views/komponen-nilai/modal.blade.php --}}
<div class="modal fade" id="knModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Tambah Komponen Nilai</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="knForm">
                <div class="modal-body">
                    <input type="hidden" id="knId">

                    {{-- Row 1: Kode & Nama --}}
                    <div class="form-row">
                        <div>
                            <label class="form-label">
                                Kode
                                <span class="badge badge-required">WAJIB</span>
                            </label>
                            <input type="text" class="form-control" id="kode"
                                   required maxlength="20" placeholder="contoh: UTS"
                                   style="text-transform:uppercase;">
                            <div class="form-text">Kode unik, otomatis uppercase.</div>
                        </div>
                        <div>
                            <label class="form-label">
                                Nama Komponen
                                <span class="badge badge-required">WAJIB</span>
                            </label>
                            <input type="text" class="form-control" id="nama"
                                   required maxlength="100"
                                   placeholder="contoh: Ujian Tengah Semester">
                        </div>
                    </div>

                    {{-- Row 2: Bobot with visual progress bar --}}
                    <div class="form-row full">
                        <div>
                            <label class="form-label">
                                Bobot
                                <span class="badge badge-required">WAJIB</span>
                                <span class="badge badge-auto">%</span>
                            </label>
                            <div style="display:flex; align-items:center; gap:1rem;">
                                <input type="number" class="form-control" id="bobot"
                                       required min="0" max="100" placeholder="0 – 100"
                                       style="max-width:120px;">
                                <div style="flex:1;">
                                    <div style="background:#e5e7eb; border-radius:99px; height:10px; overflow:hidden;">
                                        <div id="bobotBar"
                                             class="bg-success"
                                             style="width:0%; height:100%; border-radius:99px; transition:width .3s, background .3s;"></div>
                                    </div>
                                    <div style="display:flex; justify-content:space-between; margin-top:4px; font-size:0.75rem; color:#6b7280;">
                                        <span>0%</span>
                                        <span id="bobotLabel" style="font-weight:700; color:#374151;">0%</span>
                                        <span>100%</span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-text">
                                Persentase kontribusi terhadap nilai akhir (0–100).
                                <span style="color:#f59e0b;">⚠ Pastikan total bobot semua komponen = 100%.</span>
                            </div>
                        </div>
                    </div>

                    {{-- Row 3: Deskripsi --}}
                    <div class="form-row full">
                        <div>
                            <label class="form-label">
                                Deskripsi
                                <span class="badge badge-optional">Opsional</span>
                            </label>
                            <textarea class="form-control" id="deskripsi"
                                      rows="3" placeholder="Deskripsi singkat komponen nilai..."></textarea>
                        </div>
                    </div>

                    {{-- Row 4: Status --}}
                    <div class="form-row">
                        <div>
                            <label class="form-label">
                                Status
                                <span class="badge badge-required">WAJIB</span>
                            </label>
                            <select class="form-select" id="is_active" required>
                                <option value="1">✅ Aktif</option>
                                <option value="0">⏸️ Tidak Aktif</option>
                            </select>
                        </div>
                    </div>

                </div>{{-- /.modal-body --}}

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
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