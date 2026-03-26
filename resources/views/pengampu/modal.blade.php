{{-- ══════════════════════════════════════════════════════════
     MODAL 1: Batch Assign (primary add flow)
     Pengajar + Semester fixed at top, dynamic rows below
══════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="batchModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title">Tambah Penugasan Pengajar</h5>
                    <small class="text-muted">Pilih pengajar & semester, lalu tambahkan baris kelas × mata pelajaran.</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="batchForm">
                @csrf
                <div class="modal-body">

                    {{-- ── Header: Pengajar & Semester (fixed for batch) ── --}}
                    <div style="background:#f8faff; border:1.5px solid #c7d2fe; border-radius:10px; padding:1rem 1.25rem; margin-bottom:1.25rem;">
                        <div style="font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#4f46e5;margin-bottom:.75rem;">
                            📌 Header Penugasan
                        </div>
                        <div class="form-row" style="grid-template-columns: 4fr 4fr 4fr;">
                            <div>
                                <label class="form-label">Pengajar <span class="badge badge-required">WAJIB</span></label>
                                <select class="form-select" id="batch_pengajar_id" name="pengajar_id" required style="width:100%;"></select>
                                <div class="form-text">Ketik NIP atau nama pengajar.</div>
                            </div>
                            <div>
                                <label class="form-label">Semester <span class="badge badge-required">WAJIB</span></label>
                                <select class="form-select" id="batch_semester_id" name="semester_id" required style="width:100%;"></select>
                            </div>
                            <div>
                                <label class="form-label">Keterangan</label>
                                <input type="text" class="form-control" id="batch_keterangan" name="keterangan" placeholder="Catatan opsional...">
                            </div>
                        </div>
                    </div>

                    {{-- ── Dynamic rows: Kelas × Mata Pelajaran ── --}}
                    <div style="font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#6b7280;margin-bottom:.6rem;padding-bottom:.4rem;border-bottom:1px solid #e5e7eb;">
                        Baris Penugasan
                        <span id="rowCounter" style="font-weight:400; text-transform:none; margin-left:.5rem; color:#9ca3af;"></span>
                    </div>

                    {{-- Column headers --}}
                    <div style="display:grid; grid-template-columns: 2fr 2fr 1fr 1fr 36px; gap:.5rem; padding:0 .25rem; margin-bottom:.4rem;">
                        <div style="font-size:.75rem; font-weight:600; color:#6b7280;">Kelas</div>
                        <div style="font-size:.75rem; font-weight:600; color:#6b7280;">Mata Pelajaran</div>
                        <div style="font-size:.75rem; font-weight:600; color:#6b7280;">Tgl Mulai</div>
                        <div style="font-size:.75rem; font-weight:600; color:#6b7280;">Tgl Selesai</div>
                        <div></div>
                    </div>

                    <div id="batchRows">
                        {{-- rows injected by JS --}}
                    </div>

                    <button type="button" class="btn btn-outline-primary btn-sm" id="btnAddRow" style="margin-top:.6rem; width:100%;">
                        <svg width="14" height="14" fill="currentColor" viewBox="0 0 16 16"><path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/></svg>
                        Tambah Baris
                    </button>

                    {{-- Duplicate warning area --}}
                    <div id="batchWarning" style="display:none; background:#fef3c7; border:1px solid #f59e0b; border-radius:8px; padding:.6rem .9rem; margin-top:.75rem; font-size:.8rem; color:#92400e;"></div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btnBatchSubmit">
                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M15.854 5.146a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3-3a.5.5 0 1 1 .708-.708L8 11.293l6.646-6.647a.5.5 0 0 1 .708 0z"/></svg>
                        Simpan Semua
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════
     MODAL 2: Edit single record
══════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Penugasan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editForm">
                @csrf
                <div class="modal-body">
                    <input type="hidden" id="editId">

                    <div style="margin-bottom:1.25rem;">
                        <div style="font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#6b7280;margin-bottom:.75rem;padding-bottom:.4rem;border-bottom:1px solid #e5e7eb;">
                            Pengajar & Konteks
                        </div>
                        <div class="form-row" style="grid-template-columns: 2fr 1fr;">
                            <div>
                                <label class="form-label">Pengajar <span class="badge badge-required">WAJIB</span></label>
                                <select class="form-select" id="edit_pengajar_id" name="pengajar_id" required style="width:100%;"></select>
                            </div>
                            <div>
                                <label class="form-label">Semester <span class="badge badge-required">WAJIB</span></label>
                                <select class="form-select" id="edit_semester_id" name="semester_id" required style="width:100%;"></select>
                            </div>
                        </div>
                        <div class="form-row" style="grid-template-columns: 1fr 1fr;">
                            <div>
                                <label class="form-label">Kelas <span class="badge badge-required">WAJIB</span></label>
                                <select class="form-select" id="edit_kelas_id" name="kelas_id" required style="width:100%;"></select>
                            </div>
                            <div>
                                <label class="form-label">Mata Pelajaran <span class="badge badge-required">WAJIB</span></label>
                                <select class="form-select" id="edit_mata_pelajaran_id" name="mata_pelajaran_id" required style="width:100%;"></select>
                            </div>
                        </div>
                    </div>

                    <div>
                        <div style="font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#6b7280;margin-bottom:.75rem;padding-bottom:.4rem;border-bottom:1px solid #e5e7eb;">
                            Detail & Status
                        </div>
                        <div class="form-row" style="grid-template-columns: 1fr 1fr 1fr;">
                            <div>
                                <label class="form-label">Tanggal Mulai <span class="badge badge-required">WAJIB</span></label>
                                <input type="date" class="form-control" id="edit_tanggal_mulai" name="tanggal_mulai" required>
                            </div>
                            <div>
                                <label class="form-label">Tanggal Selesai</label>
                                <input type="date" class="form-control" id="edit_tanggal_selesai" name="tanggal_selesai">
                            </div>
                            <div>
                                <label class="form-label">Status <span class="badge badge-required">WAJIB</span></label>
                                <select class="form-select" id="edit_status" name="status">
                                    <option value="aktif">✅ Aktif</option>
                                    <option value="selesai">🏁 Selesai</option>
                                    <option value="diganti">🔄 Diganti</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-row full" style="margin-top:.5rem;">
                            <div>
                                <label class="form-label">Keterangan</label>
                                <textarea class="form-control" id="edit_keterangan" name="keterangan" rows="2"></textarea>
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