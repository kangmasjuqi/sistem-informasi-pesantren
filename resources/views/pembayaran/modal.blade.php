<!-- Modal Form -->
<div class="modal fade" id="pembayaranModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Tambah Pembayaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="pembayaranForm">
                <div class="modal-body">
                    <input type="hidden" id="pembayaranId" name="id">
                    
                    <div class="form-row">
                        <div>
                            <label class="form-label">
                                Santri
                                <span class="badge badge-required">WAJIB</span>
                            </label>
                            <select class="form-select" id="santri_id" name="santri_id" required>
                                <option value="">Pilih Santri...</option>
                            </select>
                            <div class="form-text">Cari berdasarkan NIS atau nama santri</div>
                        </div>

                        <div>
                            <label class="form-label">
                                Jenis Pembayaran
                                <span class="badge badge-required">WAJIB</span>
                            </label>
                            <select class="form-select" id="jenis_pembayaran_id" name="jenis_pembayaran_id" required>
                                <option value="">Pilih Jenis...</option>
                                @foreach($jenisPembayaran as $jp)
                                    <option value="{{ $jp->id }}" data-nominal="{{ $jp->nominal }}" data-kategori="{{ $jp->kategori }}">
                                        {{ $jp->nama }} ({{ ucfirst($jp->kategori) }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div>
                            <label class="form-label">
                                Tahun Ajaran
                                <span class="badge badge-optional">Opsional</span>
                            </label>
                            <select class="form-select" id="tahun_ajaran_id" name="tahun_ajaran_id">
                                <option value="">Pilih Tahun Ajaran...</option>
                                @foreach($tahunAjaran as $ta)
                                    <option value="{{ $ta->id }}" {{ $ta->is_active ? 'selected' : '' }}>
                                        {{ $ta->nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="form-label">
                                Tanggal Pembayaran
                                <span class="badge badge-required">WAJIB</span>
                            </label>
                            <input type="date" class="form-control" id="tanggal_pembayaran" name="tanggal_pembayaran" required>
                        </div>
                    </div>

                    <div class="form-row" id="bulanTahunRow" style="display: none;">
                        <div>
                            <label class="form-label">
                                Bulan
                                <span class="badge badge-optional">Untuk Pembayaran Bulanan</span>
                            </label>
                            <select class="form-select" id="bulan" name="bulan">
                                <option value="">Pilih Bulan...</option>
                                <option value="1">Januari</option>
                                <option value="2">Februari</option>
                                <option value="3">Maret</option>
                                <option value="4">April</option>
                                <option value="5">Mei</option>
                                <option value="6">Juni</option>
                                <option value="7">Juli</option>
                                <option value="8">Agustus</option>
                                <option value="9">September</option>
                                <option value="10">Oktober</option>
                                <option value="11">November</option>
                                <option value="12">Desember</option>
                            </select>
                        </div>

                        <div>
                            <label class="form-label">
                                Tahun
                                <span class="badge badge-optional">Untuk Pembayaran Bulanan</span>
                            </label>
                            <input type="number" class="form-control" id="tahun" name="tahun" min="2000" max="2100">
                        </div>
                    </div>

                    <div class="form-row">
                        <div>
                            <label class="form-label">
                                Nominal
                                <span class="badge badge-required">WAJIB</span>
                            </label>
                            <input type="number" class="form-control" id="nominal" name="nominal" step="0.01" min="0" required>
                            <div class="form-text">Nominal pembayaran dalam Rupiah</div>
                        </div>

                        <div>
                            <label class="form-label">
                                Potongan/Diskon
                                <span class="badge badge-optional">Opsional</span>
                            </label>
                            <input type="number" class="form-control" id="potongan" name="potongan" value="0" step="0.01" min="0">
                        </div>
                    </div>

                    <div class="form-row">
                        <div>
                            <label class="form-label">
                                Denda
                                <span class="badge badge-optional">Opsional</span>
                            </label>
                            <input type="number" class="form-control" id="denda" name="denda" value="0" step="0.01" min="0">
                        </div>

                        <div>
                            <label class="form-label">
                                Metode Pembayaran
                                <span class="badge badge-required">WAJIB</span>
                            </label>
                            <select class="form-select" id="metode_pembayaran" name="metode_pembayaran" required>
                                <option value="tunai">Tunai</option>
                                <option value="transfer">Transfer Bank</option>
                                <option value="qris">QRIS</option>
                                <option value="lainnya">Lainnya</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div>
                            <label class="form-label">
                                Nomor Referensi
                                <span class="badge badge-optional">No. Transfer/Bukti</span>
                            </label>
                            <input type="text" class="form-control" id="nomor_referensi" name="nomor_referensi" maxlength="100">
                            <div class="form-text">Nomor referensi transfer atau bukti pembayaran</div>
                        </div>

                        <div>
                            <label class="form-label">
                                Status
                                <span class="badge badge-required">WAJIB</span>
                            </label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="lunas">Lunas</option>
                                <option value="belum_lunas">Belum Lunas</option>
                                <option value="cicilan">Cicilan</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row full">
                        <div>
                            <label class="form-label">
                                Keterangan
                                <span class="badge badge-optional">Opsional</span>
                            </label>
                            <textarea class="form-control" id="keterangan" name="keterangan" rows="3"></textarea>
                        </div>
                    </div>

                    <!-- Calculation Summary -->
                    <div class="calculation-summary">
                        <h6 style="margin: 0 0 1rem 0; font-weight: 700;">Rincian Pembayaran</h6>
                        <div class="calculation-row">
                            <span>Nominal:</span>
                            <span id="displayNominal">Rp 0</span>
                        </div>
                        <div class="calculation-row">
                            <span>Potongan:</span>
                            <span id="displayPotongan">Rp 0</span>
                        </div>
                        <div class="calculation-row">
                            <span>Denda:</span>
                            <span id="displayDenda">Rp 0</span>
                        </div>
                        <div class="calculation-row total">
                            <span>Total Bayar:</span>
                            <span id="displayTotal">Rp 0</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btnSubmit">
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