{{-- resources/views/kamar/modal.blade.php --}}
<div class="modal fade" id="kamarModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Tambah Kamar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="kamarForm">
                <div class="modal-body">
                    <input type="hidden" id="kamarId">

                    <div class="form-row">
                        <div>
                            <label class="form-label">Gedung <span class="badge badge-required">WAJIB</span></label>
                            <select class="form-select" id="gedung_id" required>
                                <option value="">Pilih Gedung</option>
                                @foreach($gedungs as $g)
                                    <option value="{{ $g->id }}">{{ $g->nama_gedung }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="form-label">Nomor Kamar <span class="badge badge-required">WAJIB</span></label>
                            <input type="text" class="form-control" id="nomor_kamar" required maxlength="20" placeholder="contoh: A-101">
                            <div class="form-text">Unik per gedung.</div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div>
                            <label class="form-label">Nama Kamar <span class="badge badge-optional">Opsional</span></label>
                            <input type="text" class="form-control" id="nama_kamar" maxlength="100" placeholder="contoh: Kamar Al-Farabi">
                        </div>
                        <div>
                            <label class="form-label">Lantai <span class="badge badge-required">WAJIB</span></label>
                            <input type="number" class="form-control" id="lantai" required min="1" value="1">
                        </div>
                    </div>

                    <div class="form-row" style="grid-template-columns: repeat(3, 1fr);">
                        <div>
                            <label class="form-label">Kapasitas <span class="badge badge-required">WAJIB</span> <span class="badge badge-info">Orang</span></label>
                            <input type="number" class="form-control" id="kapasitas" required min="1" placeholder="contoh: 8">
                        </div>
                        <div>
                            <label class="form-label">Luas <span class="badge badge-optional">Opsional</span> <span class="badge badge-info">m²</span></label>
                            <input type="number" class="form-control" id="luas" min="0" step="0.01" placeholder="contoh: 24.5">
                        </div>
                        <div>
                            <label class="form-label">Kondisi <span class="badge badge-required">WAJIB</span></label>
                            <select class="form-select" id="kondisi" required>
                                <option value="baik">✅ Baik</option>
                                <option value="rusak_ringan">⚠️ Rusak Ringan</option>
                                <option value="rusak_berat">🔴 Rusak Berat</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row full">
                        <div>
                            <label class="form-label">Fasilitas <span class="badge badge-optional">Opsional</span></label>
                            <input type="text" class="form-control" id="fasilitas" placeholder="contoh: Lemari, Kasur, Kipas Angin, Kamar Mandi">
                            <div class="form-text">Pisahkan dengan koma.</div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div>
                            <label class="form-label">Status <span class="badge badge-required">WAJIB</span></label>
                            <select class="form-select" id="is_active" required>
                                <option value="1">✅ Aktif</option>
                                <option value="0">⏸️ Tidak Aktif</option>
                            </select>
                        </div>
                        <div>
                            <label class="form-label">Keterangan <span class="badge badge-optional">Opsional</span></label>
                            <textarea class="form-control" id="keterangan" rows="2" placeholder="Catatan..."></textarea>
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