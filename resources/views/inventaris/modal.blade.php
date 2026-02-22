{{-- resources/views/inventaris/modal.blade.php --}}
<div class="modal fade" id="inventarisModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Tambah Inventaris</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="inventarisForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" id="inventarisId">

                    {{-- Identitas Barang --}}
                    <div class="form-row">
                        <div>
                            <label class="form-label">Kategori <span class="badge badge-required">WAJIB</span></label>
                            <select class="form-select" id="kategori_inventaris_id" name="kategori_inventaris_id" required>
                                <option value="">Pilih Kategori</option>
                                @foreach($kategoris as $kat)
                                    <option value="{{ $kat->id }}">{{ $kat->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="form-label">Gedung / Lokasi <span class="badge badge-optional">Opsional</span></label>
                            <select class="form-select" id="gedung_id" name="gedung_id">
                                <option value="">Tidak ada / Tidak diketahui</option>
                                @foreach($gedungs as $g)
                                    <option value="{{ $g->id }}">{{ $g->nama_gedung }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div>
                            <label class="form-label">Kode Inventaris <span class="badge badge-optional">Opsional</span> <span class="badge badge-auto">Auto-generate</span></label>
                            <input type="text" class="form-control" id="kode_inventaris" name="kode_inventaris" maxlength="30" placeholder="Kosongkan untuk auto-generate">
                        </div>
                        <div>
                            <label class="form-label">Nama Barang <span class="badge badge-required">WAJIB</span></label>
                            <input type="text" class="form-control" id="nama_barang" name="nama_barang" required maxlength="255" placeholder="contoh: Meja Belajar Kayu">
                        </div>
                    </div>

                    <div class="form-row" style="grid-template-columns: repeat(3, 1fr);">
                        <div>
                            <label class="form-label">Merk <span class="badge badge-optional">Opsional</span></label>
                            <input type="text" class="form-control" id="merk" name="merk" maxlength="100" placeholder="contoh: Brother">
                        </div>
                        <div>
                            <label class="form-label">Tipe / Model <span class="badge badge-optional">Opsional</span></label>
                            <input type="text" class="form-control" id="tipe_model" name="tipe_model" maxlength="100" placeholder="contoh: DCP-T420W">
                        </div>
                        <div>
                            <label class="form-label">No. Seri <span class="badge badge-optional">Opsional</span></label>
                            <input type="text" class="form-control" id="nomor_seri" name="nomor_seri" maxlength="100" placeholder="Serial number">
                        </div>
                    </div>

                    {{-- Kuantitas & Kondisi --}}
                    <div class="form-row" style="grid-template-columns: repeat(4, 1fr);">
                        <div>
                            <label class="form-label">Jumlah <span class="badge badge-required">WAJIB</span></label>
                            <input type="number" class="form-control" id="jumlah" name="jumlah" required min="1" value="1">
                        </div>
                        <div>
                            <label class="form-label">Satuan <span class="badge badge-required">WAJIB</span></label>
                            <select class="form-select" id="satuan" name="satuan" required>
                                <option value="unit">unit</option>
                                <option value="buah">buah</option>
                                <option value="set">set</option>
                                <option value="lembar">lembar</option>
                                <option value="lusin">lusin</option>
                                <option value="box">box</option>
                                <option value="lainnya">lainnya</option>
                            </select>
                        </div>
                        <div>
                            <label class="form-label">Kondisi <span class="badge badge-required">WAJIB</span></label>
                            <select class="form-select" id="kondisi" name="kondisi" required>
                                <option value="baik">✅ Baik</option>
                                <option value="rusak_ringan">⚠️ Rusak Ringan</option>
                                <option value="rusak_berat">🔴 Rusak Berat</option>
                                <option value="hilang">❌ Hilang</option>
                            </select>
                        </div>
                        <div>
                            <label class="form-label">Status <span class="badge badge-required">WAJIB</span></label>
                            <select class="form-select" id="is_active" name="is_active" required>
                                <option value="1">✅ Aktif</option>
                                <option value="0">⏸️ Tidak Aktif</option>
                            </select>
                        </div>
                    </div>

                    {{-- Perolehan --}}
                    <div class="form-row" style="grid-template-columns: repeat(3, 1fr);">
                        <div>
                            <label class="form-label">Tanggal Perolehan <span class="badge badge-required">WAJIB</span></label>
                            <input type="date" class="form-control" id="tanggal_perolehan" name="tanggal_perolehan" required>
                        </div>
                        <div>
                            <label class="form-label">Harga Perolehan <span class="badge badge-optional">Opsional</span></label>
                            <div style="position:relative;">
                                <span style="position:absolute; left:12px; top:50%; transform:translateY(-50%); font-size:.875rem; font-weight:600; color:#6b7280; pointer-events:none;">Rp</span>
                                <input type="text" class="form-control" id="harga_perolehan" name="harga_perolehan" placeholder="0" style="padding-left:2.5rem; text-align:right;" inputmode="numeric">
                            </div>
                        </div>
                        <div>
                            <label class="form-label">Nilai Penyusutan <span class="badge badge-optional">Opsional</span></label>
                            <div style="position:relative;">
                                <span style="position:absolute; left:12px; top:50%; transform:translateY(-50%); font-size:.875rem; font-weight:600; color:#6b7280; pointer-events:none;">Rp</span>
                                <input type="text" class="form-control" id="nilai_penyusutan" name="nilai_penyusutan" placeholder="0" style="padding-left:2.5rem; text-align:right;" inputmode="numeric">
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div>
                            <label class="form-label">Sumber Dana <span class="badge badge-optional">Opsional</span></label>
                            <input type="text" class="form-control" id="sumber_dana" name="sumber_dana" maxlength="100" placeholder="contoh: Donasi, APBN, Yayasan">
                        </div>
                        <div>
                            <label class="form-label">Lokasi Detail <span class="badge badge-optional">Opsional</span></label>
                            <input type="text" class="form-control" id="lokasi" name="lokasi" maxlength="200" placeholder="contoh: Rak B-3, Gudang Lantai 2">
                        </div>
                    </div>

                    <div class="form-row">
                        <div>
                            <label class="form-label">Penanggung Jawab <span class="badge badge-optional">Opsional</span></label>
                            <input type="text" class="form-control" id="penanggung_jawab" name="penanggung_jawab" maxlength="100" placeholder="Nama penanggung jawab">
                        </div>
                        <div>
                            <label class="form-label">Tgl. Maintenance Terakhir <span class="badge badge-optional">Opsional</span></label>
                            <input type="date" class="form-control" id="tanggal_maintenance_terakhir" name="tanggal_maintenance_terakhir">
                        </div>
                    </div>

                    <div class="form-row">
                        <div>
                            <label class="form-label">Spesifikasi <span class="badge badge-optional">Opsional</span></label>
                            <textarea class="form-control" id="spesifikasi" name="spesifikasi" rows="3" placeholder="Spesifikasi teknis barang..."></textarea>
                        </div>
                        <div>
                            <label class="form-label">Foto Barang <span class="badge badge-optional">Opsional</span></label>
                            <input type="file" class="form-control" id="foto" name="foto" accept="image/jpg,image/jpeg,image/png,image/webp">
                            <div class="form-text">JPG/PNG/WebP, maks. 2MB.</div>
                            <img id="fotoPreview" src="" alt="Preview" style="display:none; margin-top:.75rem; max-height:120px; border-radius:8px; border:1px solid var(--border-color);">
                        </div>
                    </div>

                    <div class="form-row full">
                        <div>
                            <label class="form-label">Keterangan <span class="badge badge-optional">Opsional</span></label>
                            <textarea class="form-control" id="keterangan" name="keterangan" rows="2" placeholder="Catatan tambahan..."></textarea>
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

<script>
// Live foto preview
document.getElementById('foto').addEventListener('change', function () {
    const file = this.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = e => { document.getElementById('fotoPreview').src = e.target.result; document.getElementById('fotoPreview').style.display = 'block'; };
        reader.readAsDataURL(file);
    }
});
</script>