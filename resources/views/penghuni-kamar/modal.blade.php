{{-- resources/views/penghuni-kamar/modal.blade.php --}}
<div class="modal fade" id="penghuniModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Assign Penghuni Kamar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="penghuniForm">
                <div class="modal-body">
                    <input type="hidden" id="penghuniId">

                    <div class="form-row">
                        <div>
                            <label class="form-label">Santri <span class="badge badge-required">WAJIB</span></label>
                            <select class="form-select" id="santri_id" required style="width:100%;">
                                {{-- Options populated dynamically via Select2 AJAX --}}
                            </select>
                            <div class="form-text">Ketik minimal 2 huruf untuk mencari santri.</div>
                        </div>
                        <div>
                            <label class="form-label">Kamar <span class="badge badge-required">WAJIB</span></label>
                            <select class="form-select" id="kamar_id" required>
                                <option value="">Pilih Kamar...</option>
                                @foreach($kamars as $k)
                                    <option value="{{ $k->id }}">{{ $k->gedung?->nama_gedung }} – {{ $k->nomor_kamar }}{{ $k->nama_kamar ? ' ('.$k->nama_kamar.')' : '' }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-row" style="grid-template-columns: repeat(3, 1fr);">
                        <div>
                            <label class="form-label">Tanggal Masuk <span class="badge badge-required">WAJIB</span></label>
                            <input type="date" class="form-control" id="tanggal_masuk" required>
                        </div>
                        <div>
                            <label class="form-label">Tanggal Keluar <span class="badge badge-optional">Opsional</span></label>
                            <input type="date" class="form-control" id="tanggal_keluar">
                            <div class="form-text">Kosongkan jika masih aktif.</div>
                        </div>
                        <div>
                            <label class="form-label">Status <span class="badge badge-required">WAJIB</span></label>
                            <select class="form-select" id="status" required>
                                <option value="aktif">✅ Aktif</option>
                                <option value="keluar">🚪 Keluar</option>
                                <option value="pindah">🔄 Pindah</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row full">
                        <div>
                            <label class="form-label">Keterangan <span class="badge badge-optional">Opsional</span></label>
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