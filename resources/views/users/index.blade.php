@extends('layouts.crud')

@section('page-title', 'Manajemen User')

@section('header-actions')
<div class="action-buttons">
    <button class="btn btn-primary" id="btnCreate">
        <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
            <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
        </svg>
        Tambah User
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
                    <label class="form-label">Nama</label>
                    <input type="text" class="form-control" id="filterName" placeholder="Cari nama...">
                </div>
                <div>
                    <label class="form-label">Email</label>
                    <input type="text" class="form-control" id="filterEmail" placeholder="Cari email...">
                </div>
                <div>
                    <label class="form-label">Role</label>
                    <select class="form-select" id="filterRole">
                        <option value="">Semua Role</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}">{{ $role->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">Status</label>
                    <select class="form-select" id="filterStatus">
                        <option value="">Semua Status</option>
                        <option value="aktif">Aktif</option>
                        <option value="tidak_aktif">Tidak Aktif</option>
                        <option value="banned">Banned</option>
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

        <!-- DataTable -->
        <div class="table-responsive">
            <table class="table table-hover" id="usersTable" style="width: 100%;">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Username</th>
                        <th>Telepon</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Last Login</th>
                        <th width="140">Aksi</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

@include('users/modal')

<!-- Loading Overlay -->
<div class="loading-overlay" id="loadingOverlay">
    <div class="spinner"></div>
</div>
@endsection

@section('extra-js')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('js/users.js') }}"></script>

<script>
$(document).ready(function() {
    let table;
    const csrfToken = $('meta[name="csrf-token"]').attr('content') || document.querySelector('meta[name="csrf-token"]')?.content;

    // Initialize DataTable
    initDataTable();

    // Button: Create
    $('#btnCreate').on('click', function() {
        resetForm();
        $('#modalTitle').text('Tambah User Baru');
        $('#password').attr('required', true);
        $('#password_confirmation').attr('required', true);
        $('#passwordBadge').text('WAJIB');
        $('#passwordConfirmBadge').text('WAJIB');
        $('#passwordHelp').text('Min. 8 karakter');
        $('#userModal').modal('show');
    });

    // Button: Refresh
    $('#btnRefresh').on('click', function() {
        table.ajax.reload();
        showNotification('success', 'Data berhasil direfresh');
    });

    // Button: Apply Filters
    $('#btnApplyFilters').on('click', function() {
        table.ajax.reload();
    });

    // Form Submit
    $('#userForm').on('submit', function(e) {
        e.preventDefault();
        
        const id = $('#userId').val();
        const url = id ? `/users/${id}` : '/users';
        const method = id ? 'PUT' : 'POST';
        
        // Get checked roles
        const roles = [];
        $('input[name="roles[]"]:checked').each(function() {
            roles.push($(this).val());
        });

        if (roles.length === 0) {
            showNotification('warning', 'Minimal pilih 1 role');
            return;
        }
        
        const formData = {
            _token: csrfToken,
            _method: method,
            name: $('#name').val(),
            nama_lengkap: $('#nama_lengkap').val(),
            email: $('#email').val(),
            username: $('#username').val(),
            password: $('#password').val(),
            password_confirmation: $('#password_confirmation').val(),
            telepon: $('#telepon').val(),
            alamat: $('#alamat').val(),
            status: $('#status').val(),
            roles: roles
        };

        showLoading();

        $.ajax({
            url: url,
            method: 'POST',
            data: formData,
            success: function(response) {
                hideLoading();
                $('#userModal').modal('hide');
                table.ajax.reload();
                showNotification('success', response.message);
            },
            error: function(xhr) {
                hideLoading();
                handleAjaxError(xhr);
            }
        });
    });

    // Edit Button
    $(document).on('click', '.btn-edit', function() {
        const id = $(this).data('id');
        editUser(id);
    });

    // Reset Password Button
    $(document).on('click', '.btn-reset-password', function() {
        const id = $(this).data('id');
        const name = $(this).data('name');
        resetPassword(id, name);
    });

    // Functions
    function initDataTable() {
        table = $('#usersTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '/users/data',
                data: function(d) {
                    d.name = $('#filterName').val();
                    d.email = $('#filterEmail').val();
                    d.role_id = $('#filterRole').val();
                    d.status = $('#filterStatus').val();
                }
            },
            columns: [
                {
                    data: null,
                    render: function(data) {
                        return `<div style="line-height: 1.4;">
                            <strong>${data.nama_lengkap}</strong><br>
                            <small class="text-muted">${data.name}</small>
                        </div>`;
                    }
                },
                { data: 'email' },
                { data: 'username' },
                { data: 'telepon' },
                {
                    data: 'roles',
                    orderable: false,
                    render: function(data) {
                        if (!data || data.length === 0) return '-';
                        return data.map(role => `<span class="role-badge">${role}</span>`).join(' ');
                    }
                },
                {
                    data: 'status',
                    render: function(data) {
                        const statusMap = {
                            'aktif': 'status-aktif',
                            'tidak_aktif': 'status-tidak_aktif',
                            'banned': 'status-banned'
                        };
                        const displayText = {
                            'aktif': 'Aktif',
                            'tidak_aktif': 'Tidak Aktif',
                            'banned': 'Banned'
                        };
                        return `<span class="status-badge ${statusMap[data]}">${displayText[data]}</span>`;
                    }
                },
                { 
                    data: 'last_login_at',
                    render: function(data) {
                        return `<small>${data}</small>`;
                    }
                },
                {
                    data: 'id',
                    orderable: false,
                    render: function(data) {
                        return `
                            <div style="display: flex; gap: 0.25rem; flex-wrap: wrap;">
                                <button class="btn btn-sm btn-primary btn-edit" data-id="${data}" title="Edit">
                                    <svg width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
                                        <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                                        <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5v11z"/>
                                    </svg>
                                </button>
                            </div>
                        `;
                    }
                }
            ],
            order: [[6, 'desc']],
            pageLength: 10,
            language: {
                processing: "Memuat data...",
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                infoFiltered: "(difilter dari _MAX_ total data)",
                zeroRecords: "Tidak ada data yang ditemukan",
                emptyTable: "Tidak ada data tersedia",
                paginate: {
                    first: "Pertama",
                    last: "Terakhir",
                    next: "Selanjutnya",
                    previous: "Sebelumnya"
                }
            }
        });
    }

    function editUser(id) {
        showLoading();
        
        $.ajax({
            url: `/users/${id}`,
            method: 'GET',
            success: function(response) {
                hideLoading();
                const data = response.data;
                
                $('#userId').val(data.id);
                $('#modalTitle').text('Edit User');
                $('#name').val(data.name);
                $('#nama_lengkap').val(data.nama_lengkap);
                $('#email').val(data.email);
                $('#username').val(data.username);
                $('#telepon').val(data.telepon);
                $('#alamat').val(data.alamat);
                $('#status').val(data.status);
                
                // Password not required for edit
                $('#password').attr('required', false);
                $('#password_confirmation').attr('required', false);
                $('#password').val('');
                $('#password_confirmation').val('');
                $('#passwordBadge').text('Opsional');
                $('#passwordBadge').removeClass('badge-required').addClass('badge-optional');
                $('#passwordConfirmBadge').text('Opsional');
                $('#passwordConfirmBadge').removeClass('badge-required').addClass('badge-optional');
                $('#passwordHelp').text('Min. 8 karakter. Kosongkan jika tidak ingin mengubah password.');
                
                // Check roles
                $('input[name="roles[]"]').prop('checked', false);
                if (data.roles && data.roles.length > 0) {
                    data.roles.forEach(function(roleId) {
                        $(`#role_${roleId}`).prop('checked', true);
                    });
                }
                
                $('#userModal').modal('show');
            },
            error: function(xhr) {
                hideLoading();
                handleAjaxError(xhr);
            }
        });
    }

    function resetPassword(id, name) {
        Swal.fire({
            title: 'Reset Password',
            html: `
                <p>Reset password untuk: <strong>${name}</strong></p>
                <input type="password" id="swal-password" class="swal2-input" placeholder="Password baru (min. 8 karakter)">
                <input type="password" id="swal-password-confirm" class="swal2-input" placeholder="Konfirmasi password">
            `,
            showCancelButton: true,
            confirmButtonText: 'Reset Password',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#1a3a2e',
            preConfirm: () => {
                const password = document.getElementById('swal-password').value;
                const passwordConfirm = document.getElementById('swal-password-confirm').value;
                
                if (!password || !passwordConfirm) {
                    Swal.showValidationMessage('Password dan konfirmasi harus diisi');
                    return false;
                }
                
                if (password.length < 8) {
                    Swal.showValidationMessage('Password minimal 8 karakter');
                    return false;
                }
                
                if (password !== passwordConfirm) {
                    Swal.showValidationMessage('Password dan konfirmasi tidak cocok');
                    return false;
                }
                
                return { password, password_confirmation: passwordConfirm };
            }
        }).then((result) => {
            if (result.isConfirmed && result.value) {
                showLoading();
                
                $.ajax({
                    url: `/users/${id}/reset-password`,
                    method: 'POST',
                    data: {
                        _token: csrfToken,
                        password: result.value.password,
                        password_confirmation: result.value.password_confirmation
                    },
                    success: function(response) {
                        hideLoading();
                        showNotification('success', response.message);
                    },
                    error: function(xhr) {
                        hideLoading();
                        handleAjaxError(xhr);
                    }
                });
            }
        });
    }

    function resetForm() {
        $('#userForm')[0].reset();
        $('#userId').val('');
        $('input[name="roles[]"]').prop('checked', false);
        $('#passwordBadge').text('WAJIB');
        $('#passwordBadge').removeClass('badge-optional').addClass('badge-required');
        $('#passwordConfirmBadge').text('WAJIB');
        $('#passwordConfirmBadge').removeClass('badge-optional').addClass('badge-required');
        $('#password').attr('required', true);
        $('#password_confirmation').attr('required', true);
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
});

// Toggle password visibility
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const type = field.type === 'password' ? 'text' : 'password';
    field.type = type;
}
</script>
@endsection