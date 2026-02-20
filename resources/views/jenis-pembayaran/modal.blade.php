{{-- resources/views/jenis-pembayaran/modal.blade.php --}}
<div class="modal fade" id="jpModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Tambah Jenis Pembayaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="jpForm">
                <div class="modal-body">
                    <input type="hidden" id="jpId">

                    {{-- Row 1: Kode & Nama --}}
                    <div class="form-row">
                        <div>
                            <label class="form-label">
                                Kode
                                <span class="badge badge-required">WAJIB</span>
                            </label>
                            <input type="text" class="form-control" id="kode"
                                   required maxlength="20" placeholder="cth: SPP-BLN"
                                   style="text-transform:uppercase;">
                            <div class="form-text">Kode unik, otomatis uppercase.</div>
                        </div>
                        <div>
                            <label class="form-label">
                                Nama
                                <span class="badge badge-required">WAJIB</span>
                            </label>
                            <input type="text" class="form-control" id="nama"
                                   required maxlength="100" placeholder="cth: SPP Bulanan">
                        </div>
                    </div>

                    {{-- Row 2: Kategori & Nominal --}}
                    <div class="form-row">
                        <div>
                            <label class="form-label">
                                Kategori
                                <span class="badge badge-required">WAJIB</span>
                            </label>
                            <select class="form-select" id="kategori" required>
                                <option value="">Pilih Kategori</option>
                                <option value="bulanan">💳 Bulanan</option>
                                <option value="tahunan">📅 Tahunan</option>
                                <option value="pendaftaran">📝 Pendaftaran</option>
                                <option value="kegiatan">🎯 Kegiatan</option>
                                <option value="lainnya">📦 Lainnya</option>
                            </select>
                        </div>
                        <div>
                            <label class="form-label">
                                Nominal Default
                                <span class="badge badge-required">WAJIB</span>
                                <span class="badge badge-auto">Rupiah</span>
                            </label>
                            <div style="position:relative;">
                                <span style="
                                    position:absolute; left:12px; top:50%;
                                    transform:translateY(-50%);
                                    font-size:0.875rem; font-weight:600;
                                    color:#6b7280; pointer-events:none;">Rp</span>
                                <input type="text" class="form-control" id="nominal"
                                       required placeholder="0"
                                       style="padding-left:2.5rem; text-align:right;"
                                       inputmode="numeric">
                            </div>
                            <div class="form-text">Nominal dalam Rupiah tanpa desimal.</div>
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
                                      rows="3" placeholder="Deskripsi jenis pembayaran..."></textarea>
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