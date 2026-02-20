@extends('layouts.crud')

@section('page-title', 'Tahun Ajaran & Semester')

@section('header-actions')
<div class="action-buttons d-flex gap-2">
    <button class="btn btn-primary" id="btnCreate">
        <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
            <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
        </svg>
        Tambah Tahun Ajaran
    </button>
    <button class="btn btn-outline-primary" id="btnRefresh">
        <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
            <path fill-rule="evenodd" d="M8 3a5 5 0 1 0 4.546 2.914.5.5 0 0 1 .908-.417A6 6 0 1 1 8 2v1z"/>
            <path d="M8 4.466V.534a.25.25 0 0 1 .41-.192l2.36 1.966c.12.1.12.284 0 .384L8.41 4.658A.25.25 0 0 1 8 4.466z"/>
        </svg>
        Refresh
    </button>
</div>
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <!-- Alert Container -->
        <div id="alertContainer" style="margin-bottom: 1.5rem;"></div>

        <!-- Filters -->
        <div class="filters-section">
            <h6 style="margin: 0 0 1rem 0; font-weight: 700; color: #374151;">
                <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16" style="vertical-align: -2px; margin-right: 0.5rem;">
                    <path d="M6 10.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5zm-2-3a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5zm-2-3a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5z"/>
                </svg>
                Filter Pencarian
            </h6>
            <div class="filters-grid">
                <div>
                    <label class="form-label">Tahun Ajaran</label>
                    <input type="text" class="form-control" id="searchInput" placeholder="Cari tahun ajaran...">
                </div>
                <div>
                    <label class="form-label">Status</label>
                    <select class="form-select" id="filterStatus">
                        <option value="">Semua Status</option>
                        <option value="1">Aktif</option>
                        <option value="0">Tidak Aktif</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Data per Halaman</label>
                    <select class="form-select" id="perPageSelect">
                        <option value="10" selected>10 per halaman</option>
                        <option value="25">25 per halaman</option>
                        <option value="50">50 per halaman</option>
                        <option value="100">100 per halaman</option>
                    </select>
                </div>
                <div style="display: flex; align-items: end;">
                    <button class="btn btn-primary" id="btnApplyFilters" style="width: 100%;">
                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
                        </svg>
                        Terapkan Filter
                    </button>
                </div>
            </div>
        </div>

        <!-- Loading Spinner -->
        <div id="loadingSpinner" class="text-center py-5" style="display: none;">
            <div class="spinner"></div>
            <p class="text-muted mt-3" style="font-weight: 500;">Memuat data...</p>
        </div>

        <!-- Data Table -->
        <div id="dataTableContainer">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th width="5%">#</th>
                            <th width="15%">Tahun Ajaran</th>
                            <th width="12%">Periode</th>
                            <th width="25%">Semester Ganjil</th>
                            <th width="25%">Semester Genap</th>
                            <th width="8%" class="text-center">Status</th>
                            <th width="10%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="dataTableBody">
                        <!-- Data will be loaded here -->
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="text-muted">
                    <small id="paginationInfo" style="font-weight: 500;">Menampilkan data...</small>
                </div>
                <nav>
                    <ul class="pagination mb-0" id="paginationContainer">
                        <!-- Pagination will be loaded here -->
                    </ul>
                </nav>
            </div>
        </div>

        <!-- Empty State -->
        <div id="emptyState" class="text-center py-5" style="display: none;">
            <svg width="80" height="80" fill="currentColor" viewBox="0 0 16 16" style="color: #d1d5db; margin-bottom: 1rem;">
                <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4H1z"/>
            </svg>
            <h5 class="text-muted" style="font-weight: 600;">Tidak ada data tahun ajaran</h5>
            <p class="text-muted" style="font-size: 0.875rem;">Mulai dengan menambahkan tahun ajaran baru</p>
            <button class="btn btn-primary mt-2" id="btnCreateEmpty">
                <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
                </svg>
                Tambah Tahun Ajaran
            </button>
        </div>
    </div>
</div>

<!-- Create/Edit Modal -->
<div class="modal fade" id="formModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">
                    <svg width="20" height="20" fill="currentColor" viewBox="0 0 16 16" style="vertical-align: -3px; margin-right: 0.5rem;">
                        <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
                    </svg>
                    Tambah Tahun Ajaran & Semester
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="tahunAjaranForm">
                <div class="modal-body">
                    <input type="hidden" id="tahunAjaranId">
                    
                    <!-- Alert in Modal -->
                    <div id="modalAlertContainer"></div>

                    <!-- Tahun Ajaran Section -->
                    <div class="section-card" style="border: 2px solid var(--primary-color);">
                        <div class="section-header" style="background: var(--primary-color); color: white;">
                            <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4H1z"/>
                            </svg>
                            Informasi Tahun Ajaran
                        </div>
                        <div class="section-body">
                            <div class="form-row">
                                <div>
                                    <label class="form-label">
                                        Nama Tahun Ajaran 
                                        <span class="badge badge-required">Required</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="nama" 
                                           placeholder="Contoh: 2024/2025"
                                           required>
                                    <div class="form-text">Format: YYYY/YYYY (contoh: 2024/2025)</div>
                                    <div class="invalid-feedback" id="error-nama"></div>
                                </div>

                                <div>
                                    <label class="form-label">
                                        Status Tahun Ajaran 
                                        <span class="badge badge-required">Required</span>
                                    </label>
                                    <select class="form-select" id="is_active" required>
                                        <option value="1">Aktif</option>
                                        <option value="0" selected>Tidak Aktif</option>
                                    </select>
                                    <div class="form-text">
                                        <svg width="12" height="12" fill="currentColor" viewBox="0 0 16 16">
                                            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                                            <path d="m8.93 6.588-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533L8.93 6.588zM9 4.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0z"/>
                                        </svg>
                                        Hanya 1 tahun ajaran yang bisa aktif
                                    </div>
                                    <div class="invalid-feedback" id="error-is_active"></div>
                                </div>
                            </div>

                            <div class="form-row">
                                <div>
                                    <label class="form-label">
                                        Tahun Mulai 
                                        <span class="badge badge-required">Required</span>
                                    </label>
                                    <input type="number" 
                                           class="form-control" 
                                           id="tahun_mulai" 
                                           min="2000" 
                                           max="2100"
                                           placeholder="2024"
                                           required>
                                    <div class="invalid-feedback" id="error-tahun_mulai"></div>
                                </div>

                                <div>
                                    <label class="form-label">
                                        Tahun Selesai 
                                        <span class="badge badge-required">Required</span>
                                    </label>
                                    <input type="number" 
                                           class="form-control" 
                                           id="tahun_selesai" 
                                           min="2000" 
                                           max="2100"
                                           placeholder="2025"
                                           required>
                                    <div class="invalid-feedback" id="error-tahun_selesai"></div>
                                </div>
                            </div>

                            <div class="form-row">
                                <div>
                                    <label class="form-label">
                                        Tanggal Mulai 
                                        <span class="badge badge-required">Required</span>
                                    </label>
                                    <input type="date" class="form-control" id="tanggal_mulai" required>
                                    <div class="invalid-feedback" id="error-tanggal_mulai"></div>
                                </div>

                                <div>
                                    <label class="form-label">
                                        Tanggal Selesai 
                                        <span class="badge badge-required">Required</span>
                                    </label>
                                    <input type="date" class="form-control" id="tanggal_selesai" required>
                                    <div class="invalid-feedback" id="error-tanggal_selesai"></div>
                                </div>
                            </div>

                            <div class="form-row full">
                                <div>
                                    <label class="form-label">
                                        Keterangan 
                                        <span class="badge badge-optional">Optional</span>
                                    </label>
                                    <textarea class="form-control" 
                                              id="keterangan" 
                                              rows="2" 
                                              placeholder="Catatan atau informasi tambahan..."></textarea>
                                    <div class="invalid-feedback" id="error-keterangan"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Semester Section -->
                    <div class="row g-4">
                        <!-- Semester Ganjil -->
                        <div class="col-md-6">
                            <div class="section-card" style="border: 2px solid var(--success-color);">
                                <div class="section-header" style="background: var(--success-color); color: white;">
                                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                        <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4H1z"/>
                                    </svg>
                                    Semester Ganjil
                                </div>
                                <div class="section-body">
                                    <div style="margin-bottom: 1.5rem;">
                                        <label class="form-label">
                                            Tanggal Mulai 
                                            <span class="badge badge-required">Required</span>
                                        </label>
                                        <input type="date" 
                                               class="form-control" 
                                               id="semester_ganjil_tanggal_mulai" 
                                               required>
                                        <div class="invalid-feedback" id="error-semester_ganjil_tanggal_mulai"></div>
                                    </div>

                                    <div style="margin-bottom: 1.5rem;">
                                        <label class="form-label">
                                            Tanggal Selesai 
                                            <span class="badge badge-required">Required</span>
                                        </label>
                                        <input type="date" 
                                               class="form-control" 
                                               id="semester_ganjil_tanggal_selesai" 
                                               required>
                                        <div class="invalid-feedback" id="error-semester_ganjil_tanggal_selesai"></div>
                                    </div>

                                    <div style="margin-bottom: 1.5rem;">
                                        <label class="form-label">
                                            Status Semester 
                                            <span class="badge badge-required">Required</span>
                                        </label>
                                        <select class="form-select" id="semester_ganjil_is_active" required>
                                            <option value="1">Aktif</option>
                                            <option value="0" selected>Tidak Aktif</option>
                                        </select>
                                        <div class="form-text">
                                            <svg width="12" height="12" fill="currentColor" viewBox="0 0 16 16">
                                                <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                                                <path d="m8.93 6.588-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533L8.93 6.588zM9 4.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0z"/>
                                            </svg>
                                            Hanya 1 semester yang bisa aktif
                                        </div>
                                        <div class="invalid-feedback" id="error-semester_ganjil_is_active"></div>
                                    </div>

                                    <div>
                                        <label class="form-label">
                                            Keterangan 
                                            <span class="badge badge-optional">Optional</span>
                                        </label>
                                        <textarea class="form-control" 
                                                  id="semester_ganjil_keterangan" 
                                                  rows="3" 
                                                  placeholder="Catatan semester ganjil..."></textarea>
                                        <div class="invalid-feedback" id="error-semester_ganjil_keterangan"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Semester Genap -->
                        <div class="col-md-6">
                            <div class="section-card" style="border: 2px solid var(--info-color);">
                                <div class="section-header" style="background: var(--info-color); color: white;">
                                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                        <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4H1z"/>
                                    </svg>
                                    Semester Genap
                                </div>
                                <div class="section-body">
                                    <div style="margin-bottom: 1.5rem;">
                                        <label class="form-label">
                                            Tanggal Mulai 
                                            <span class="badge badge-required">Required</span>
                                        </label>
                                        <input type="date" 
                                               class="form-control" 
                                               id="semester_genap_tanggal_mulai" 
                                               required>
                                        <div class="form-text">
                                            <svg width="12" height="12" fill="var(--warning-color)" viewBox="0 0 16 16">
                                                <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
                                            </svg>
                                            Harus setelah semester ganjil selesai
                                        </div>
                                        <div class="invalid-feedback" id="error-semester_genap_tanggal_mulai"></div>
                                    </div>

                                    <div style="margin-bottom: 1.5rem;">
                                        <label class="form-label">
                                            Tanggal Selesai 
                                            <span class="badge badge-required">Required</span>
                                        </label>
                                        <input type="date" 
                                               class="form-control" 
                                               id="semester_genap_tanggal_selesai" 
                                               required>
                                        <div class="invalid-feedback" id="error-semester_genap_tanggal_selesai"></div>
                                    </div>

                                    <div style="margin-bottom: 1.5rem;">
                                        <label class="form-label">
                                            Status Semester 
                                            <span class="badge badge-required">Required</span>
                                        </label>
                                        <select class="form-select" id="semester_genap_is_active" required>
                                            <option value="1">Aktif</option>
                                            <option value="0" selected>Tidak Aktif</option>
                                        </select>
                                        <div class="form-text">
                                            <svg width="12" height="12" fill="currentColor" viewBox="0 0 16 16">
                                                <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                                                <path d="m8.93 6.588-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533L8.93 6.588zM9 4.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0z"/>
                                            </svg>
                                            Hanya 1 semester yang bisa aktif
                                        </div>
                                        <div class="invalid-feedback" id="error-semester_genap_is_active"></div>
                                    </div>

                                    <div>
                                        <label class="form-label">
                                            Keterangan 
                                            <span class="badge badge-optional">Optional</span>
                                        </label>
                                        <textarea class="form-control" 
                                                  id="semester_genap_keterangan" 
                                                  rows="3" 
                                                  placeholder="Catatan semester genap..."></textarea>
                                        <div class="invalid-feedback" id="error-semester_genap_keterangan"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">
                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8 2.146 2.854Z"/>
                        </svg>
                        Batal
                    </button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M2 1a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H9.5a1 1 0 0 0-1 1v7.293l2.646-2.647a.5.5 0 0 1 .708.708l-3.5 3.5a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L7.5 9.293V2a2 2 0 0 1 2-2H14a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h2.5a.5.5 0 0 1 0 1H2z"/>
                        </svg>
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, var(--danger-color) 0%, #dc2626 100%);">
                <h5 class="modal-title">
                    <svg width="20" height="20" fill="currentColor" viewBox="0 0 16 16" style="vertical-align: -3px; margin-right: 0.5rem;">
                        <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
                    </svg>
                    Konfirmasi Hapus
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center py-4">
                <svg width="80" height="80" fill="var(--danger-color)" viewBox="0 0 16 16" style="margin-bottom: 1.5rem;">
                    <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/>
                    <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/>
                </svg>
                <h5 style="font-weight: 700; margin-bottom: 0.75rem;">Yakin ingin menghapus?</h5>
                <p class="text-muted mb-0" style="font-size: 0.9rem;">
                    Tahun ajaran "<strong id="deleteItemName"></strong>" beserta semesternya akan dihapus.
                    <br><span style="color: var(--danger-color); font-weight: 600;">Tindakan ini tidak dapat dibatalkan.</span>
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">
                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8 2.146 2.854Z"/>
                    </svg>
                    Batal
                </button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/>
                        <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/>
                    </svg>
                    Ya, Hapus
                </button>
            </div>
        </div>
    </div>
</div>

<div class="loading-overlay" id="loadingOverlay">
    <div class="spinner"></div>
</div>
@endsection

@section('extra-js')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    let currentPage = 1;
    let perPage = 10;
    let searchQuery = '';
    let filterStatus = '';
    let deleteId = null;
    const csrfToken = $('meta[name="csrf-token"]').attr('content');

    const formModal = new bootstrap.Modal(document.getElementById('formModal'));
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));

    // Load data on page load
    loadData();

    // Buttons
    $('#btnCreate, #btnCreateEmpty').on('click', function() {
        openCreateModal();
    });

    $('#btnRefresh').on('click', function() {
        loadData(currentPage);
        showNotification('success', 'Data berhasil direfresh');
    });

    $('#btnApplyFilters').on('click', function() {
        searchQuery = $('#searchInput').val();
        filterStatus = $('#filterStatus').val();
        perPage = $('#perPageSelect').val();
        loadData(1);
    });

    // Form Submit
    $('#tahunAjaranForm').on('submit', function(e) {
        e.preventDefault();
        
        const id = $('#tahunAjaranId').val();
        const url = id ? `/tahun-ajaran/${id}` : '/tahun-ajaran';
        const method = id ? 'PUT' : 'POST';
        
        const formData = {
            _token: csrfToken,
            _method: method,
            nama: $('#nama').val(),
            tahun_mulai: $('#tahun_mulai').val(),
            tahun_selesai: $('#tahun_selesai').val(),
            tanggal_mulai: $('#tanggal_mulai').val(),
            tanggal_selesai: $('#tanggal_selesai').val(),
            is_active: $('#is_active').val(),
            keterangan: $('#keterangan').val(),
            
            semester_ganjil_tanggal_mulai: $('#semester_ganjil_tanggal_mulai').val(),
            semester_ganjil_tanggal_selesai: $('#semester_ganjil_tanggal_selesai').val(),
            semester_ganjil_is_active: $('#semester_ganjil_is_active').val(),
            semester_ganjil_keterangan: $('#semester_ganjil_keterangan').val(),
            
            semester_genap_tanggal_mulai: $('#semester_genap_tanggal_mulai').val(),
            semester_genap_tanggal_selesai: $('#semester_genap_tanggal_selesai').val(),
            semester_genap_is_active: $('#semester_genap_is_active').val(),
            semester_genap_keterangan: $('#semester_genap_keterangan').val(),
        };

        const submitBtn = $('#submitBtn');
        submitBtn.prop('disabled', true);
        submitBtn.html(`
            <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16" class="spinner" style="animation: spin 1s linear infinite;">
                <path d="M8 0a8 8 0 1 0 0 16A8 8 0 0 0 8 0zM7 8a1 1 0 1 1 2 0 1 1 0 0 1-2 0z"/>
            </svg>
            Menyimpan...
        `);

        clearValidationErrors();
        showLoading();

        $.ajax({
            url: url,
            method: 'POST',
            data: formData,
            success: function(response) {
                hideLoading();
                submitBtn.prop('disabled', false);
                submitBtn.html(`
                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M2 1a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H9.5a1 1 0 0 0-1 1v7.293l2.646-2.647a.5.5 0 0 1 .708.708l-3.5 3.5a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L7.5 9.293V2a2 2 0 0 1 2-2H14a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h2.5a.5.5 0 0 1 0 1H2z"/>
                    </svg>
                    Simpan
                `);
                
                formModal.hide();
                loadData(currentPage);
                showNotification('success', response.message);
            },
            error: function(xhr) {
                hideLoading();
                submitBtn.prop('disabled', false);
                submitBtn.html(`
                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M2 1a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H9.5a1 1 0 0 0-1 1v7.293l2.646-2.647a.5.5 0 0 1 .708.708l-3.5 3.5a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L7.5 9.293V2a2 2 0 0 1 2-2H14a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h2.5a.5.5 0 0 1 0 1H2z"/>
                    </svg>
                    Simpan
                `);
                
                handleAjaxError(xhr);
            }
        });
    });

    // Edit button
    $(document).on('click', '.btn-edit', function() {
        const id = $(this).data('id');
        openEditModal(id);
    });

    // Delete button
    $(document).on('click', '.btn-delete', function() {
        const id = $(this).data('id');
        const name = $(this).data('name');
        openDeleteModal(id, name);
    });

    // Confirm delete
    $('#confirmDeleteBtn').on('click', function() {
        confirmDelete();
    });

    // Functions
    function loadData(page = 1) {
        currentPage = page;
        
        $('#loadingSpinner').show();
        $('#dataTableContainer').hide();
        $('#emptyState').hide();

        const params = {
            page: page,
            per_page: perPage,
            search: searchQuery
        };

        if (filterStatus !== '') {
            params.is_active = filterStatus;
        }

        $.ajax({
            url: '/tahun-ajaran/data',
            method: 'GET',
            data: params,
            success: function(response) {
                $('#loadingSpinner').hide();
                
                if (response.success && response.data.data.length > 0) {
                    renderTable(response.data);
                    $('#dataTableContainer').show();
                } else {
                    $('#emptyState').show();
                }
            },
            error: function(xhr) {
                $('#loadingSpinner').hide();
                handleAjaxError(xhr);
            }
        });
    }

    function renderTable(paginationData) {
        const tbody = $('#dataTableBody');
        tbody.empty();

        $.each(paginationData.data, function(index, item) {
            const semesterGanjil = item.semesters.find(s => s.jenis_semester === 'ganjil');
            const semesterGenap = item.semesters.find(s => s.jenis_semester === 'genap');
            
            const row = `
                <tr>
                    <td class="text-muted" style="font-weight: 600;">${paginationData.from + index}</td>
                    <td>
                        <strong style="font-size: 0.95rem;">${item.nama}</strong>
                        ${item.keterangan ? `<br><small class="text-muted">${item.keterangan}</small>` : ''}
                    </td>
                    <td>
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <svg width="14" height="14" fill="var(--primary-color)" viewBox="0 0 16 16">
                                <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4H1z"/>
                            </svg>
                            <div style="font-size: 0.85rem;">
                                <small class="text-muted d-block">${formatDate(item.tanggal_mulai)}</small>
                                <small class="text-muted">s/d ${formatDate(item.tanggal_selesai)}</small>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="semester-info">
                            ${semesterGanjil ? `
                                <div class="semester-dates">
                                    <svg width="14" height="14" fill="var(--success-color)" viewBox="0 0 16 16">
                                        <path fill-rule="evenodd" d="M10.854 7.146a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 1 1 .708-.708L7.5 9.793l2.646-2.647a.5.5 0 0 1 .708 0z"/>
                                        <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4H1z"/>
                                    </svg>
                                    <span>${formatDate(semesterGanjil.tanggal_mulai)} - ${formatDate(semesterGanjil.tanggal_selesai)}</span>
                                </div>
                                <div>
                                    ${semesterGanjil.is_active 
                                        ? '<span class="semester-badge semester-ganjil">AKTIF</span>' 
                                        : '<span class="semester-badge" style="background: #f3f4f6; color: #6b7280;">Tidak Aktif</span>'}
                                </div>
                                ${semesterGanjil.keterangan ? `<small class="text-muted" style="display: block; margin-top: 0.25rem;">${semesterGanjil.keterangan}</small>` : ''}
                            ` : '<span class="text-muted">-</span>'}
                        </div>
                    </td>
                    <td>
                        <div class="semester-info">
                            ${semesterGenap ? `
                                <div class="semester-dates">
                                    <svg width="14" height="14" fill="var(--info-color)" viewBox="0 0 16 16">
                                        <path fill-rule="evenodd" d="M10.854 7.146a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 1 1 .708-.708L7.5 9.793l2.646-2.647a.5.5 0 0 1 .708 0z"/>
                                        <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4H1z"/>
                                    </svg>
                                    <span>${formatDate(semesterGenap.tanggal_mulai)} - ${formatDate(semesterGenap.tanggal_selesai)}</span>
                                </div>
                                <div>
                                    ${semesterGenap.is_active 
                                        ? '<span class="semester-badge semester-genap">AKTIF</span>' 
                                        : '<span class="semester-badge" style="background: #f3f4f6; color: #6b7280;">Tidak Aktif</span>'}
                                </div>
                                ${semesterGenap.keterangan ? `<small class="text-muted" style="display: block; margin-top: 0.25rem;">${semesterGenap.keterangan}</small>` : ''}
                            ` : '<span class="text-muted">-</span>'}
                        </div>
                    </td>
                    <td class="text-center">
                        ${item.is_active 
                            ? '<span class="status-badge status-active">AKTIF</span>' 
                            : '<span class="status-badge status-inactive">TIDAK AKTIF</span>'}
                    </td>
                    <td class="text-center">
                        <div class="action-buttons">
                            <button class="btn btn-sm btn-primary btn-edit" data-id="${item.id}" title="Edit">
                                <svg width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                                    <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5v11z"/>
                                </svg>
                            </button>
                            <button class="btn btn-sm btn-danger btn-delete" data-id="${item.id}" data-name="${item.nama}" title="Hapus" ${item.is_active ? 'disabled' : ''}>
                                <svg width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/>
                                    <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/>
                                </svg>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
            tbody.append(row);
        });

        renderPagination(paginationData);
    }

    function renderPagination(data) {
        $('#paginationInfo').text(`Menampilkan ${data.from} - ${data.to} dari ${data.total} data`);

        const container = $('#paginationContainer');
        container.empty();

        // Previous button
        const prevClass = data.current_page === 1 ? 'disabled' : '';
        container.append(`
            <li class="page-item ${prevClass}">
                <a class="page-link" href="#" data-page="${data.current_page - 1}">
                    <svg width="12" height="12" fill="currentColor" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0z"/>
                    </svg>
                </a>
            </li>
        `);

        // Page numbers
        const startPage = Math.max(1, data.current_page - 2);
        const endPage = Math.min(data.last_page, data.current_page + 2);

        if (startPage > 1) {
            container.append(`<li class="page-item"><a class="page-link" href="#" data-page="1">1</a></li>`);
            if (startPage > 2) {
                container.append(`<li class="page-item disabled"><span class="page-link">...</span></li>`);
            }
        }

        for (let i = startPage; i <= endPage; i++) {
            const activeClass = i === data.current_page ? 'active' : '';
            container.append(`
                <li class="page-item ${activeClass}">
                    <a class="page-link" href="#" data-page="${i}">${i}</a>
                </li>
            `);
        }

        if (endPage < data.last_page) {
            if (endPage < data.last_page - 1) {
                container.append(`<li class="page-item disabled"><span class="page-link">...</span></li>`);
            }
            container.append(`<li class="page-item"><a class="page-link" href="#" data-page="${data.last_page}">${data.last_page}</a></li>`);
        }

        // Next button
        const nextClass = data.current_page === data.last_page ? 'disabled' : '';
        container.append(`
            <li class="page-item ${nextClass}">
                <a class="page-link" href="#" data-page="${data.current_page + 1}">
                    <svg width="12" height="12" fill="currentColor" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708z"/>
                    </svg>
                </a>
            </li>
        `);

        // Pagination click event
        container.find('a.page-link').on('click', function(e) {
            e.preventDefault();
            const page = $(this).data('page');
            if (page && !$(this).parent().hasClass('disabled') && !$(this).parent().hasClass('active')) {
                loadData(page);
            }
        });
    }

    function openCreateModal() {
        $('#modalTitle').html(`
            <svg width="20" height="20" fill="currentColor" viewBox="0 0 16 16" style="vertical-align: -3px; margin-right: 0.5rem;">
                <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
            </svg>
            Tambah Tahun Ajaran & Semester
        `);
        $('#tahunAjaranForm')[0].reset();
        $('#tahunAjaranId').val('');
        clearValidationErrors();
        $('#modalAlertContainer').empty();
        formModal.show();
    }

    function openEditModal(id) {
        $('#modalTitle').html(`
            <svg width="20" height="20" fill="currentColor" viewBox="0 0 16 16" style="vertical-align: -3px; margin-right: 0.5rem;">
                <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
            </svg>
            Edit Tahun Ajaran & Semester
        `);
        clearValidationErrors();
        $('#modalAlertContainer').empty();
        showLoading();

        $.ajax({
            url: `/tahun-ajaran/${id}`,
            method: 'GET',
            success: function(response) {
                hideLoading();
                if (response.success) {
                    const data = response.data;
                    $('#tahunAjaranId').val(data.id);
                    $('#nama').val(data.nama);
                    $('#tahun_mulai').val(data.tahun_mulai);
                    $('#tahun_selesai').val(data.tahun_selesai);
                    $('#tanggal_mulai').val(data.tanggal_mulai);
                    $('#tanggal_selesai').val(data.tanggal_selesai);
                    $('#is_active').val(data.is_active ? '1' : '0');
                    $('#keterangan').val(data.keterangan || '');

                    $('#semester_ganjil_tanggal_mulai').val(data.semester_ganjil_tanggal_mulai);
                    $('#semester_ganjil_tanggal_selesai').val(data.semester_ganjil_tanggal_selesai);
                    $('#semester_ganjil_is_active').val(data.semester_ganjil_is_active ? '1' : '0');
                    $('#semester_ganjil_keterangan').val(data.semester_ganjil_keterangan || '');

                    $('#semester_genap_tanggal_mulai').val(data.semester_genap_tanggal_mulai);
                    $('#semester_genap_tanggal_selesai').val(data.semester_genap_tanggal_selesai);
                    $('#semester_genap_is_active').val(data.semester_genap_is_active ? '1' : '0');
                    $('#semester_genap_keterangan').val(data.semester_genap_keterangan || '');

                    formModal.show();
                }
            },
            error: function(xhr) {
                hideLoading();
                handleAjaxError(xhr);
            }
        });
    }

    function openDeleteModal(id, name) {
        deleteId = id;
        $('#deleteItemName').text(name);
        deleteModal.show();
    }

    function confirmDelete() {
        if (!deleteId) return;

        showLoading();

        $.ajax({
            url: `/tahun-ajaran/${deleteId}`,
            method: 'DELETE',
            data: { _token: csrfToken },
            success: function(response) {
                hideLoading();
                deleteModal.hide();
                if (response.success) {
                    showNotification('success', response.message);
                    loadData(currentPage);
                } else {
                    showNotification('error', response.message);
                }
            },
            error: function(xhr) {
                hideLoading();
                deleteModal.hide();
                handleAjaxError(xhr);
            }
        });
    }

    function clearValidationErrors() {
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').text('');
    }

    function displayValidationErrors(errors) {
        for (const [field, messages] of Object.entries(errors)) {
            const input = $(`#${field}`);
            const errorDiv = $(`#error-${field}`);
            
            if (input.length && errorDiv.length) {
                input.addClass('is-invalid');
                errorDiv.text(messages[0]);
            }
        }
    }

    function showLoading() {
        $('#loadingOverlay').addClass('show');
    }

    function hideLoading() {
        $('#loadingOverlay').removeClass('show');
    }

    function showNotification(type, message) {
        const icons = {
            success: 'success',
            error: 'error',
            warning: 'warning',
            info: 'info'
        };

        Swal.fire({
            icon: icons[type],
            title: type === 'success' ? 'Berhasil!' : type === 'error' ? 'Gagal!' : 'Perhatian!',
            text: message,
            timer: 3000,
            timerProgressBar: true,
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
        });
    }

    function handleAjaxError(xhr) {
        let message = 'Terjadi kesalahan pada server';
        
        if (xhr.responseJSON) {
            if (xhr.responseJSON.message) {
                message = xhr.responseJSON.message;
            }
            
            if (xhr.responseJSON.errors) {
                const errors = xhr.responseJSON.errors;
                displayValidationErrors(errors);
                message = Object.values(errors).flat().join('<br>');
            }
        }
        
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            html: message,
            confirmButtonColor: '#ef4444'
        });
    }

    function formatDate(dateString) {
        if (!dateString) return '-';
        const date = new Date(dateString);
        const options = { day: '2-digit', month: 'short', year: 'numeric' };
        return date.toLocaleDateString('id-ID', options);
    }
});
</script>
@endsection