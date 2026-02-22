{{-- resources/views/perizinan/modal-approve.blade.php --}}
<div class="modal fade" id="approveModal" tabindex="-1">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Persetujuan Perizinan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="approveForm">
                <div class="modal-body">
                    <input type="hidden" id="approveId">

                    <div class="alert alert-info" style="margin-bottom:1.5rem;">
                        Perizinan No. <strong id="approveNomor"></strong>
                    </div>

                    <div class="form-row full" style="margin-bottom:1rem;">
                        <div>
                            <label class="form-label">Keputusan <span class="badge badge-required">WAJIB</span></label>
                            <div style="display:flex; gap:1rem; margin-top:.5rem;">
                                <label style="display:flex; align-items:center; gap:.5rem; cursor:pointer; font-weight:600; color:#065f46;">
                                    <input type="radio" name="action" value="disetujui" checked style="accent-color:#10b981; width:18px; height:18px;">
                                    ✅ Setujui
                                </label>
                                <label style="display:flex; align-items:center; gap:.5rem; cursor:pointer; font-weight:600; color:#991b1b;">
                                    <input type="radio" name="action" value="ditolak" style="accent-color:#ef4444; width:18px; height:18px;">
                                    ❌ Tolak
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="form-row full">
                        <div>
                            <label class="form-label">Catatan <span class="badge badge-optional">Opsional</span></label>
                            <textarea class="form-control" id="catatan_persetujuan" rows="3" placeholder="Catatan persetujuan atau alasan penolakan..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M15.854 5.146a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3-3a.5.5 0 1 1 .708-.708L8 11.293l6.646-6.647a.5.5 0 0 1 .708 0z"/></svg>
                        Konfirmasi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>