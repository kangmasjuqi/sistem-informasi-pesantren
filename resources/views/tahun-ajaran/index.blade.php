@extends('layouts.dashboard')

@section('page-title', 'Tahun Ajaran & Semester')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>&nbsp;</div>
                <button class="btn btn-primary btn-lg shadow-sm" onclick="openCreateModal()">
                    <i class="fas fa-plus me-2"></i>Tambah Tahun Ajaran
                </button>
            </div>
        </div>
    </div>

    <!-- Alert Container -->
    <div id="alertContainer"></div>

    <!-- Search & Filter Card -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input type="text" 
                               class="form-control border-start-0 ps-0" 
                               id="searchInput" 
                               placeholder="Cari tahun ajaran, periode, atau keterangan..."
                               onkeyup="searchData()">
                    </div>
                </div>
                <div class="col-md-3">
                    <select class="form-select" id="perPageSelect" onchange="changePerPage()">
                        <option value="10" selected>10 per halaman</option>
                        <option value="25">25 per halaman</option>
                        <option value="50">50 per halaman</option>
                        <option value="100">100 per halaman</option>
                    </select>
                </div>
                <div class="col-md-3 text-end">
                    <button class="btn btn-outline-secondary" onclick="resetFilters()">
                        <i class="fas fa-redo me-2"></i>Reset
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="card shadow-sm">
        <div class="card-body">
            <div id="loadingSpinner" class="text-center py-5" style="display: none;">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="text-muted mt-2">Memuat data...</p>
            </div>

            <div id="dataTableContainer">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
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
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="text-muted">
                        <small id="paginationInfo">Menampilkan data...</small>
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
                <i class="fas fa-calendar-times fa-4x text-muted mb-3"></i>
                <h5 class="text-muted">Tidak ada data tahun ajaran</h5>
                <p class="text-muted">Mulai dengan menambahkan tahun ajaran baru</p>
                <button class="btn btn-primary mt-2" onclick="openCreateModal()">
                    <i class="fas fa-plus me-2"></i>Tambah Tahun Ajaran
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Create/Edit Modal -->
<div class="modal fade" id="formModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalTitle">
                    <i class="fas fa-plus-circle me-2"></i>Tambah Tahun Ajaran & Semester
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="tahunAjaranForm">
                <div class="modal-body">
                    <input type="hidden" id="tahunAjaranId">
                    
                    <!-- Alert in Modal -->
                    <div id="modalAlertContainer"></div>

                    <!-- Tahun Ajaran Section -->
                    <div class="card border-primary mb-4">
                        <div class="card-header bg-primary text-white">
                            <h6 class="mb-0"><i class="fas fa-calendar-alt me-2"></i>Informasi Tahun Ajaran</h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">
                                        Nama Tahun Ajaran 
                                        <span class="badge bg-danger ms-1">Required</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="nama" 
                                           placeholder="Contoh: 2024/2025"
                                           required>
                                    <div class="form-text">Format: YYYY/YYYY (contoh: 2024/2025)</div>
                                    <div class="invalid-feedback" id="error-nama"></div>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">
                                        Tahun Mulai 
                                        <span class="badge bg-danger ms-1">Required</span>
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

                                <div class="col-md-3">
                                    <label class="form-label">
                                        Tahun Selesai 
                                        <span class="badge bg-danger ms-1">Required</span>
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

                                <div class="col-md-6">
                                    <label class="form-label">
                                        Tanggal Mulai 
                                        <span class="badge bg-danger ms-1">Required</span>
                                    </label>
                                    <input type="date" class="form-control" id="tanggal_mulai" required>
                                    <div class="invalid-feedback" id="error-tanggal_mulai"></div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">
                                        Tanggal Selesai 
                                        <span class="badge bg-danger ms-1">Required</span>
                                    </label>
                                    <input type="date" class="form-control" id="tanggal_selesai" required>
                                    <div class="invalid-feedback" id="error-tanggal_selesai"></div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">
                                        Status Tahun Ajaran 
                                        <span class="badge bg-danger ms-1">Required</span>
                                    </label>
                                    <select class="form-select" id="is_active" required>
                                        <option value="1">Aktif</option>
                                        <option value="0" selected>Tidak Aktif</option>
                                    </select>
                                    <div class="form-text">
                                        <i class="fas fa-info-circle text-primary"></i> 
                                        Hanya 1 tahun ajaran yang bisa aktif
                                    </div>
                                    <div class="invalid-feedback" id="error-is_active"></div>
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label">
                                        Keterangan 
                                        <span class="badge bg-secondary ms-1">Optional</span>
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
                            <div class="card border-success h-100">
                                <div class="card-header bg-success text-white">
                                    <h6 class="mb-0">
                                        <i class="fas fa-calendar-week me-2"></i>Semester Ganjil
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <label class="form-label">
                                                Tanggal Mulai 
                                                <span class="badge bg-danger ms-1">Required</span>
                                            </label>
                                            <input type="date" 
                                                   class="form-control" 
                                                   id="semester_ganjil_tanggal_mulai" 
                                                   required>
                                            <div class="invalid-feedback" id="error-semester_ganjil_tanggal_mulai"></div>
                                        </div>

                                        <div class="col-12">
                                            <label class="form-label">
                                                Tanggal Selesai 
                                                <span class="badge bg-danger ms-1">Required</span>
                                            </label>
                                            <input type="date" 
                                                   class="form-control" 
                                                   id="semester_ganjil_tanggal_selesai" 
                                                   required>
                                            <div class="invalid-feedback" id="error-semester_ganjil_tanggal_selesai"></div>
                                        </div>

                                        <div class="col-12">
                                            <label class="form-label">
                                                Status Semester 
                                                <span class="badge bg-danger ms-1">Required</span>
                                            </label>
                                            <select class="form-select" id="semester_ganjil_is_active" required>
                                                <option value="1">Aktif</option>
                                                <option value="0" selected>Tidak Aktif</option>
                                            </select>
                                            <div class="form-text">
                                                <i class="fas fa-info-circle text-success"></i> 
                                                Hanya 1 semester yang bisa aktif
                                            </div>
                                            <div class="invalid-feedback" id="error-semester_ganjil_is_active"></div>
                                        </div>

                                        <div class="col-12">
                                            <label class="form-label">
                                                Keterangan 
                                                <span class="badge bg-secondary ms-1">Optional</span>
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
                        </div>

                        <!-- Semester Genap -->
                        <div class="col-md-6">
                            <div class="card border-info h-100">
                                <div class="card-header bg-info text-white">
                                    <h6 class="mb-0">
                                        <i class="fas fa-calendar-week me-2"></i>Semester Genap
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <label class="form-label">
                                                Tanggal Mulai 
                                                <span class="badge bg-danger ms-1">Required</span>
                                            </label>
                                            <input type="date" 
                                                   class="form-control" 
                                                   id="semester_genap_tanggal_mulai" 
                                                   required>
                                            <div class="form-text">
                                                <i class="fas fa-exclamation-triangle text-warning"></i> 
                                                Harus setelah semester ganjil selesai
                                            </div>
                                            <div class="invalid-feedback" id="error-semester_genap_tanggal_mulai"></div>
                                        </div>

                                        <div class="col-12">
                                            <label class="form-label">
                                                Tanggal Selesai 
                                                <span class="badge bg-danger ms-1">Required</span>
                                            </label>
                                            <input type="date" 
                                                   class="form-control" 
                                                   id="semester_genap_tanggal_selesai" 
                                                   required>
                                            <div class="invalid-feedback" id="error-semester_genap_tanggal_selesai"></div>
                                        </div>

                                        <div class="col-12">
                                            <label class="form-label">
                                                Status Semester 
                                                <span class="badge bg-danger ms-1">Required</span>
                                            </label>
                                            <select class="form-select" id="semester_genap_is_active" required>
                                                <option value="1">Aktif</option>
                                                <option value="0" selected>Tidak Aktif</option>
                                            </select>
                                            <div class="form-text">
                                                <i class="fas fa-info-circle text-info"></i> 
                                                Hanya 1 semester yang bisa aktif
                                            </div>
                                            <div class="invalid-feedback" id="error-semester_genap_is_active"></div>
                                        </div>

                                        <div class="col-12">
                                            <label class="form-label">
                                                Keterangan 
                                                <span class="badge bg-secondary ms-1">Optional</span>
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
                </div>

                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Batal
                    </button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <i class="fas fa-save me-2"></i>Simpan
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
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle me-2"></i>Konfirmasi Hapus
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center py-4">
                <i class="fas fa-trash-alt fa-4x text-danger mb-3"></i>
                <h5>Yakin ingin menghapus?</h5>
                <p class="text-muted mb-0">
                    Tahun ajaran "<strong id="deleteItemName"></strong>" beserta semesternya akan dihapus.
                    <br>Tindakan ini tidak dapat dibatalkan.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Batal
                </button>
                <button type="button" class="btn btn-danger" onclick="confirmDelete()">
                    <i class="fas fa-trash me-2"></i>Ya, Hapus
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    .table thead th {
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
    }

    .badge {
        font-weight: 500;
        padding: 0.35em 0.65em;
    }

    .form-label {
        font-weight: 600;
        margin-bottom: 0.5rem;
        color: #2c3e50;
    }

    .card-header {
        font-weight: 600;
    }

    .btn {
        font-weight: 500;
    }

    .semester-info {
        font-size: 0.85rem;
        line-height: 1.6;
    }

    .semester-dates {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 0.25rem;
    }

    .action-buttons .btn {
        padding: 0.25rem 0.75rem;
        font-size: 0.875rem;
    }

    .pagination .page-link {
        font-weight: 500;
    }

    .form-text {
        font-size: 0.8rem;
    }

    .invalid-feedback {
        font-size: 0.85rem;
    }
</style>
@endpush

@push('scripts')
<script>
let currentPage = 1;
let perPage = 10;
let searchQuery = '';
let deleteId = null;

const formModal = new bootstrap.Modal(document.getElementById('formModal'));
const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));

// Load data on page load
document.addEventListener('DOMContentLoaded', function() {
    loadData();
});

// Load data function
function loadData(page = 1) {
    currentPage = page;
    
    document.getElementById('loadingSpinner').style.display = 'block';
    document.getElementById('dataTableContainer').style.display = 'none';
    document.getElementById('emptyState').style.display = 'none';

    fetch(`{{ route('tahun-ajaran.data') }}?page=${page}&per_page=${perPage}&search=${searchQuery}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('loadingSpinner').style.display = 'none';
            
            if (data.success && data.data.data.length > 0) {
                renderTable(data.data);
                document.getElementById('dataTableContainer').style.display = 'block';
            } else {
                document.getElementById('emptyState').style.display = 'block';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('loadingSpinner').style.display = 'none';
            showAlert('danger', 'Gagal memuat data. Silakan coba lagi.');
        });
}

// Render table
function renderTable(paginationData) {
    const tbody = document.getElementById('dataTableBody');
    tbody.innerHTML = '';

    paginationData.data.forEach((item, index) => {
        const semesterGanjil = item.semesters.find(s => s.jenis_semester === 'ganjil');
        const semesterGenap = item.semesters.find(s => s.jenis_semester === 'genap');
        
        const row = `
            <tr>
                <td class="text-muted">${paginationData.from + index}</td>
                <td>
                    <strong>${item.nama}</strong>
                    ${item.keterangan ? `<br><small class="text-muted">${item.keterangan}</small>` : ''}
                </td>
                <td>
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-calendar text-primary"></i>
                        <div>
                            <small class="text-muted d-block">${formatDate(item.tanggal_mulai)}</small>
                            <small class="text-muted">s/d ${formatDate(item.tanggal_selesai)}</small>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="semester-info">
                        ${semesterGanjil ? `
                            <div class="semester-dates">
                                <i class="fas fa-calendar-check text-success"></i>
                                <span>${formatDate(semesterGanjil.tanggal_mulai)} - ${formatDate(semesterGanjil.tanggal_selesai)}</span>
                            </div>
                            <div>
                                ${semesterGanjil.is_active 
                                    ? '<span class="badge bg-success">Aktif</span>' 
                                    : '<span class="badge bg-secondary">Tidak Aktif</span>'}
                            </div>
                            ${semesterGanjil.keterangan ? `<small class="text-muted">${semesterGanjil.keterangan}</small>` : ''}
                        ` : '<span class="text-muted">-</span>'}
                    </div>
                </td>
                <td>
                    <div class="semester-info">
                        ${semesterGenap ? `
                            <div class="semester-dates">
                                <i class="fas fa-calendar-check text-info"></i>
                                <span>${formatDate(semesterGenap.tanggal_mulai)} - ${formatDate(semesterGenap.tanggal_selesai)}</span>
                            </div>
                            <div>
                                ${semesterGenap.is_active 
                                    ? '<span class="badge bg-success">Aktif</span>' 
                                    : '<span class="badge bg-secondary">Tidak Aktif</span>'}
                            </div>
                            ${semesterGenap.keterangan ? `<small class="text-muted">${semesterGenap.keterangan}</small>` : ''}
                        ` : '<span class="text-muted">-</span>'}
                    </div>
                </td>
                <td class="text-center">
                    ${item.is_active 
                        ? '<span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>Aktif</span>' 
                        : '<span class="badge bg-secondary">Tidak Aktif</span>'}
                </td>
                <td class="text-center">
                    <div class="btn-group action-buttons" role="group">
                        <button class="btn btn-sm btn-outline-primary" onclick="openEditModal(${item.id})" title="Edit">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger" onclick="openDeleteModal(${item.id}, '${item.nama}')" title="Hapus" ${item.is_active ? 'disabled' : ''}>
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
        tbody.innerHTML += row;
    });

    renderPagination(paginationData);
}

// Render pagination
function renderPagination(data) {
    const info = document.getElementById('paginationInfo');
    info.textContent = `Menampilkan ${data.from} - ${data.to} dari ${data.total} data`;

    const container = document.getElementById('paginationContainer');
    container.innerHTML = '';

    // Previous button
    const prevClass = data.current_page === 1 ? 'disabled' : '';
    container.innerHTML += `
        <li class="page-item ${prevClass}">
            <a class="page-link" href="#" onclick="loadData(${data.current_page - 1}); return false;">
                <i class="fas fa-chevron-left"></i>
            </a>
        </li>
    `;

    // Page numbers
    const startPage = Math.max(1, data.current_page - 2);
    const endPage = Math.min(data.last_page, data.current_page + 2);

    if (startPage > 1) {
        container.innerHTML += `<li class="page-item"><a class="page-link" href="#" onclick="loadData(1); return false;">1</a></li>`;
        if (startPage > 2) {
            container.innerHTML += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
        }
    }

    for (let i = startPage; i <= endPage; i++) {
        const activeClass = i === data.current_page ? 'active' : '';
        container.innerHTML += `
            <li class="page-item ${activeClass}">
                <a class="page-link" href="#" onclick="loadData(${i}); return false;">${i}</a>
            </li>
        `;
    }

    if (endPage < data.last_page) {
        if (endPage < data.last_page - 1) {
            container.innerHTML += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
        }
        container.innerHTML += `<li class="page-item"><a class="page-link" href="#" onclick="loadData(${data.last_page}); return false;">${data.last_page}</a></li>`;
    }

    // Next button
    const nextClass = data.current_page === data.last_page ? 'disabled' : '';
    container.innerHTML += `
        <li class="page-item ${nextClass}">
            <a class="page-link" href="#" onclick="loadData(${data.current_page + 1}); return false;">
                <i class="fas fa-chevron-right"></i>
            </a>
        </li>
    `;
}

// Search function
function searchData() {
    searchQuery = document.getElementById('searchInput').value;
    loadData(1);
}

// Change per page
function changePerPage() {
    perPage = document.getElementById('perPageSelect').value;
    loadData(1);
}

// Reset filters
function resetFilters() {
    document.getElementById('searchInput').value = '';
    document.getElementById('perPageSelect').value = '10';
    searchQuery = '';
    perPage = 10;
    loadData(1);
}

// Open create modal
function openCreateModal() {
    document.getElementById('modalTitle').innerHTML = '<i class="fas fa-plus-circle me-2"></i>Tambah Tahun Ajaran & Semester';
    document.getElementById('tahunAjaranForm').reset();
    document.getElementById('tahunAjaranId').value = '';
    clearValidationErrors();
    document.getElementById('modalAlertContainer').innerHTML = '';
    formModal.show();
}

// Open edit modal
function openEditModal(id) {
    document.getElementById('modalTitle').innerHTML = '<i class="fas fa-edit me-2"></i>Edit Tahun Ajaran & Semester';
    clearValidationErrors();
    document.getElementById('modalAlertContainer').innerHTML = '';

    fetch(`{{ url('tahun-ajaran') }}/${id}`)
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                const data = result.data;
                document.getElementById('tahunAjaranId').value = data.id;
                document.getElementById('nama').value = data.nama;
                document.getElementById('tahun_mulai').value = data.tahun_mulai;
                document.getElementById('tahun_selesai').value = data.tahun_selesai;
                document.getElementById('tanggal_mulai').value = data.tanggal_mulai;
                document.getElementById('tanggal_selesai').value = data.tanggal_selesai;
                document.getElementById('is_active').value = data.is_active ? '1' : '0';
                document.getElementById('keterangan').value = data.keterangan || '';

                // Semester Ganjil
                document.getElementById('semester_ganjil_tanggal_mulai').value = data.semester_ganjil_tanggal_mulai;
                document.getElementById('semester_ganjil_tanggal_selesai').value = data.semester_ganjil_tanggal_selesai;
                document.getElementById('semester_ganjil_is_active').value = data.semester_ganjil_is_active ? '1' : '0';
                document.getElementById('semester_ganjil_keterangan').value = data.semester_ganjil_keterangan || '';

                // Semester Genap
                document.getElementById('semester_genap_tanggal_mulai').value = data.semester_genap_tanggal_mulai;
                document.getElementById('semester_genap_tanggal_selesai').value = data.semester_genap_tanggal_selesai;
                document.getElementById('semester_genap_is_active').value = data.semester_genap_is_active ? '1' : '0';
                document.getElementById('semester_genap_keterangan').value = data.semester_genap_keterangan || '';

                formModal.show();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('danger', 'Gagal memuat data. Silakan coba lagi.');
        });
}

// Submit form
document.getElementById('tahunAjaranForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const id = document.getElementById('tahunAjaranId').value;
    const url = id ? `{{ url('tahun-ajaran') }}/${id}` : '{{ route('tahun-ajaran.store') }}';
    const method = id ? 'PUT' : 'POST';

    const formData = {
        nama: document.getElementById('nama').value,
        tahun_mulai: document.getElementById('tahun_mulai').value,
        tahun_selesai: document.getElementById('tahun_selesai').value,
        tanggal_mulai: document.getElementById('tanggal_mulai').value,
        tanggal_selesai: document.getElementById('tanggal_selesai').value,
        is_active: document.getElementById('is_active').value,
        keterangan: document.getElementById('keterangan').value,
        
        semester_ganjil_tanggal_mulai: document.getElementById('semester_ganjil_tanggal_mulai').value,
        semester_ganjil_tanggal_selesai: document.getElementById('semester_ganjil_tanggal_selesai').value,
        semester_ganjil_is_active: document.getElementById('semester_ganjil_is_active').value,
        semester_ganjil_keterangan: document.getElementById('semester_ganjil_keterangan').value,
        
        semester_genap_tanggal_mulai: document.getElementById('semester_genap_tanggal_mulai').value,
        semester_genap_tanggal_selesai: document.getElementById('semester_genap_tanggal_selesai').value,
        semester_genap_is_active: document.getElementById('semester_genap_is_active').value,
        semester_genap_keterangan: document.getElementById('semester_genap_keterangan').value,
    };

    const submitBtn = document.getElementById('submitBtn');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Menyimpan...';

    clearValidationErrors();

    fetch(url, {
        method: method,
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify(formData)
    })
    .then(response => response.json())
    .then(data => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-save me-2"></i>Simpan';

        if (data.success) {
            formModal.hide();
            showAlert('success', data.message);
            loadData(currentPage);
        } else {
            if (data.errors) {
                displayValidationErrors(data.errors);
            }
            showModalAlert('danger', data.message || 'Terjadi kesalahan saat menyimpan data');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-save me-2"></i>Simpan';
        showModalAlert('danger', 'Terjadi kesalahan sistem. Silakan coba lagi.');
    });
});

// Open delete modal
function openDeleteModal(id, name) {
    deleteId = id;
    document.getElementById('deleteItemName').textContent = name;
    deleteModal.show();
}

// Confirm delete
function confirmDelete() {
    if (!deleteId) return;

    fetch(`{{ url('tahun-ajaran') }}/${deleteId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        deleteModal.hide();
        if (data.success) {
            showAlert('success', data.message);
            loadData(currentPage);
        } else {
            showAlert('danger', data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        deleteModal.hide();
        showAlert('danger', 'Terjadi kesalahan saat menghapus data');
    });
}

// Show alert
function showAlert(type, message) {
    const alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>
            <strong>${type === 'success' ? 'Berhasil!' : 'Gagal!'}</strong> ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    const container = document.getElementById('alertContainer');
    container.innerHTML = alertHtml;
    
    // Auto dismiss after 5 seconds
    setTimeout(() => {
        const alert = container.querySelector('.alert');
        if (alert) {
            bootstrap.Alert.getOrCreateInstance(alert).close();
        }
    }, 5000);

    // Scroll to top
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

// Show modal alert
function showModalAlert(type, message) {
    const alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    document.getElementById('modalAlertContainer').innerHTML = alertHtml;
}

// Display validation errors
function displayValidationErrors(errors) {
    for (const [field, messages] of Object.entries(errors)) {
        const input = document.getElementById(field);
        const errorDiv = document.getElementById(`error-${field}`);
        
        if (input && errorDiv) {
            input.classList.add('is-invalid');
            errorDiv.textContent = messages[0];
        }
    }
}

// Clear validation errors
function clearValidationErrors() {
    document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
    document.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');
}

// Format date helper
function formatDate(dateString) {
    if (!dateString) return '-';
    const date = new Date(dateString);
    const options = { day: '2-digit', month: 'short', year: 'numeric' };
    return date.toLocaleDateString('id-ID', options);
}
</script>
@endpush