<!-- Modal -->
<div class="modal fade" id="mapelModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Tambah Mata Pelajaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="mapelForm">
                <div class="modal-body">
                    <input type="hidden" id="mapelId">
                    
                    <div class="form-row">
                        <div>
                            <label class="form-label">
                                Kode Mapel
                                <span class="badge badge-required">WAJIB</span>
                            </label>
                            <input type="text" class="form-control" id="kode_mapel" required placeholder="MTK" maxlength="20">
                            <div class="form-text">Kode unik (otomatis uppercase)</div>
                        </div>
                        <div>
                            <label class="form-label">
                                Nama Mata Pelajaran
                                <span class="badge badge-required">WAJIB</span>
                            </label>
                            <input type="text" class="form-control" id="nama_mapel" required placeholder="Matematika" maxlength="100">
                        </div>
                    </div>

                    <div class="form-row">
                        <div>
                            <label class="form-label">
                                Kategori
                                <span class="badge badge-required">WAJIB</span>
                            </label>
                            <select class="form-select" id="kategori" required>
                                <option value="">Pilih Kategori</option>
                                <option value="agama">🕌 Agama</option>
                                <option value="umum">📚 Umum</option>
                                <option value="keterampilan">🛠️ Keterampilan</option>
                                <option value="ekstrakurikuler">⚽ Ekstrakurikuler</option>
                            </select>
                        </div>
                        <div>
                            <label class="form-label">
                                Bobot SKS
                                <span class="badge badge-required">WAJIB</span>
                                <span class="badge badge-info">Jam/Minggu</span>
                            </label>
                            <input type="number" class="form-control" id="bobot_sks" required min="1" max="10" placeholder="1-10">
                            <div class="form-text">Jam per minggu (1-10)</div>
                        </div>
                    </div>

                    <div class="form-row full">
                        <div>
                            <label class="form-label">
                                Deskripsi
                                <span class="badge badge-optional">Opsional</span>
                            </label>
                            <textarea class="form-control" id="deskripsi" rows="3" placeholder="Deskripsi mata pelajaran"></textarea>
                        </div>
                    </div>

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
                </div>
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