{{-- resources/views/perizinan/modal.blade.php --}}
<div class="modal fade" id="perizinanModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Ajukan Perizinan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="perizinanForm">
                <div class="modal-body">
                    <input type="hidden" id="perizinanId">

                    {{-- Santri & Jenis --}}
                    <div class="form-row">
                        <div>
                            <label class="form-label">Santri <span class="badge badge-required">WAJIB</span></label>
                            <select class="form-select" id="santri_id" name="santri_id" required>
                                <option value="">Pilih Santri...</option>
                            </select>
                            <div class="form-text">Cari berdasarkan NIS atau nama santri</div>
                        </div>
                        <div>
                            <label class="form-label">Jenis Izin <span class="badge badge-required">WAJIB</span></label>
                            <select class="form-select" id="jenis_izin" required>
                                <option value="">Pilih Jenis</option>
                                <option value="pulang">🏠 Pulang</option>
                                <option value="kunjungan">👥 Kunjungan</option>
                                <option value="sakit">🏥 Sakit</option>
                                <option value="keluar_sementara">🚶 Keluar Sementara</option>
                            </select>
                        </div>
                    </div>

                    {{-- Tanggal & Waktu --}}
                    <div class="form-row" style="grid-template-columns: repeat(4, 1fr);">
                        <div>
                            <label class="form-label">Tanggal Mulai <span class="badge badge-required">WAJIB</span></label>
                            <input type="date" class="form-control" id="tanggal_mulai" required>
                        </div>
                        <div>
                            <label class="form-label">Tanggal Selesai <span class="badge badge-required">WAJIB</span></label>
                            <input type="date" class="form-control" id="tanggal_selesai" required>
                        </div>
                        <div>
                            <label class="form-label">Waktu Keluar <span class="badge badge-optional">Opsional</span></label>
                            <input type="time" class="form-control" id="waktu_keluar">
                        </div>
                        <div>
                            <label class="form-label">Rencana Kembali <span class="badge badge-optional">Opsional</span></label>
                            <input type="time" class="form-control" id="waktu_kembali">
                        </div>
                    </div>

                    {{-- Keperluan & Tujuan --}}
                    <div class="form-row">
                        <div>
                            <label class="form-label">Keperluan / Alasan <span class="badge badge-required">WAJIB</span></label>
                            <textarea class="form-control" id="keperluan" rows="3" required placeholder="Jelaskan keperluan izin..."></textarea>
                        </div>
                        <div>
                            <label class="form-label">Tujuan / Alamat <span class="badge badge-optional">Opsional</span></label>
                            <textarea class="form-control" id="tujuan" rows="3" placeholder="Alamat tujuan..."></textarea>
                        </div>
                    </div>

                    {{-- Penjemput --}}
                    <div style="background:var(--bg-light); padding:1.25rem; border-radius:8px; border:1px solid var(--border-color); margin-bottom:1.5rem;">
                        <p style="font-weight:700; font-size:.875rem; color:#374151; margin:0 0 1rem 0;">👤 Data Penjemput <span style="font-weight:400; color:var(--text-muted);">(opsional untuk sakit)</span></p>
                        <div class="form-row" style="margin-bottom:1rem;">
                            <div>
                                <label class="form-label">Nama Penjemput</label>
                                <input type="text" class="form-control" id="penjemput_nama" maxlength="100" placeholder="Nama lengkap penjemput">
                            </div>
                            <div>
                                <label class="form-label">Hubungan dengan Santri</label>
                                <input type="text" class="form-control" id="penjemput_hubungan" maxlength="50" placeholder="contoh: Ayah, Ibu, Wali">
                            </div>
                        </div>
                        <div class="form-row" style="margin-bottom:0;">
                            <div>
                                <label class="form-label">No. Telepon</label>
                                <input type="text" class="form-control" id="penjemput_telepon" maxlength="20" placeholder="08xx-xxxx-xxxx">
                            </div>
                            <div>
                                <label class="form-label">No. KTP / SIM</label>
                                <input type="text" class="form-control" id="penjemput_identitas" maxlength="50" placeholder="Nomor identitas">
                            </div>
                        </div>
                    </div>

                    {{-- Keterangan --}}
                    <div class="form-row full">
                        <div>
                            <label class="form-label">Keterangan Tambahan <span class="badge badge-optional">Opsional</span></label>
                            <textarea class="form-control" id="keterangan" rows="2" placeholder="Catatan tambahan..."></textarea>
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