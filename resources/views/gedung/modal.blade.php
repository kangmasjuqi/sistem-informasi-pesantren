{{-- resources/views/gedung/modal.blade.php --}}
<div class="modal fade" id="gedungModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Tambah Gedung Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="gedungForm">
                <div class="modal-body">
                    <input type="hidden" id="gedungId">

                    {{-- Row 1: Kode & Nama --}}
                    <div class="form-row">
                        <div>
                            <label class="form-label">
                                Kode Gedung <span class="badge badge-required">WAJIB</span>
                            </label>
                            <input type="text" class="form-control" id="kode_gedung"
                                   required maxlength="20" placeholder="cth: GD-A"
                                   style="text-transform:uppercase;">
                            <div class="form-text">Kode unik, otomatis uppercase.</div>
                        </div>
                        <div>
                            <label class="form-label">
                                Nama Gedung <span class="badge badge-required">WAJIB</span>
                            </label>
                            <input type="text" class="form-control" id="nama_gedung"
                                   required maxlength="255" placeholder="cth: Gedung Asrama A">
                        </div>
                    </div>

                    {{-- Row 2: Jenis & Kondisi --}}
                    <div class="form-row">
                        <div>
                            <label class="form-label">
                                Jenis Gedung <span class="badge badge-required">WAJIB</span>
                            </label>
                            <select class="form-select" id="jenis_gedung" required>
                                <option value="">Pilih Jenis</option>
                                <option value="asrama_putra">🏠 Asrama Putra</option>
                                <option value="asrama_putri">🏠 Asrama Putri</option>
                                <option value="kelas">🏫 Kelas</option>
                                <option value="serbaguna">🏟️ Serbaguna</option>
                                <option value="masjid">🕌 Masjid</option>
                                <option value="kantor">🏢 Kantor</option>
                                <option value="perpustakaan">📚 Perpustakaan</option>
                                <option value="lab">🔬 Laboratorium</option>
                                <option value="dapur">🍳 Dapur</option>
                                <option value="lainnya">🏗️ Lainnya</option>
                            </select>
                        </div>
                        <div>
                            <label class="form-label">
                                Kondisi <span class="badge badge-required">WAJIB</span>
                            </label>
                            <select class="form-select" id="kondisi" required>
                                <option value="baik">✅ Baik</option>
                                <option value="rusak_ringan">⚠️ Rusak Ringan</option>
                                <option value="rusak_berat">🔴 Rusak Berat</option>
                            </select>
                        </div>
                    </div>

                    {{-- Row 3: Jumlah Lantai, Kapasitas, Tahun Dibangun --}}
                    <div class="form-row" style="grid-template-columns: repeat(3, 1fr);">
                        <div>
                            <label class="form-label">
                                Jumlah Lantai <span class="badge badge-required">WAJIB</span>
                            </label>
                            <input type="number" class="form-control" id="jumlah_lantai"
                                   required min="1" max="99" value="1" placeholder="1">
                        </div>
                        <div>
                            <label class="form-label">
                                Kapasitas Total <span class="badge badge-optional">Opsional</span>
                                <span class="badge badge-info">Orang</span>
                            </label>
                            <input type="number" class="form-control" id="kapasitas_total"
                                   min="1" placeholder="cth: 100">
                        </div>
                        <div>
                            <label class="form-label">
                                Tahun Dibangun <span class="badge badge-optional">Opsional</span>
                            </label>
                            <input type="number" class="form-control" id="tahun_dibangun"
                                   min="1900" max="{{ date('Y') }}" placeholder="cth: 2010">
                        </div>
                    </div>

                    {{-- Row 4: Alamat --}}
                    <div class="form-row full">
                        <div>
                            <label class="form-label">
                                Alamat / Lokasi <span class="badge badge-optional">Opsional</span>
                            </label>
                            <textarea class="form-control" id="alamat_lokasi" rows="2"
                                      placeholder="Lokasi gedung dalam area pesantren..."></textarea>
                        </div>
                    </div>

                    {{-- Row 5: Fasilitas --}}
                    <div class="form-row full">
                        <div>
                            <label class="form-label">
                                Fasilitas <span class="badge badge-optional">Opsional</span>
                            </label>
                            <input type="text" class="form-control" id="fasilitas"
                                   placeholder="cth: AC, Proyektor, Toilet, WiFi">
                            <div class="form-text">Pisahkan dengan koma. Disimpan sebagai JSON array.</div>
                        </div>
                    </div>

                    {{-- Row 6: Keterangan & Status --}}
                    <div class="form-row">
                        <div>
                            <label class="form-label">
                                Keterangan <span class="badge badge-optional">Opsional</span>
                            </label>
                            <textarea class="form-control" id="keterangan" rows="2"
                                      placeholder="Catatan tambahan tentang gedung..."></textarea>
                        </div>
                        <div>
                            <label class="form-label">
                                Status <span class="badge badge-required">WAJIB</span>
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