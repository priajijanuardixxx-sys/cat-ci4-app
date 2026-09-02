<?= $this->extend('layout/admin_layout') ?>

<?= $this->section('content') ?>

<!-- Kontainer Konten Dinamis -->
<!-- PENTING: Gunakan padding dan main-content-desktop untuk mengatasi tumpang tindih -->
<div id="main-content" class="px-4 py-4 main-content-desktop">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><?= $title ?></h1>
        <a href="<?= base_url('superadmin/users/create') ?>" class="btn btn-primary">
            <i class="fas fa-plus"></i> Tambah Akun Baru
        </a>
    </div>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>

    <div class="card shadow p-4">
        <div class="row mb-3">
            <div class="col-md-6">
                <input type="text" id="live-search" class="form-control" placeholder="Cari berdasarkan Nama, Username, atau Event...">
            </div>
        </div>

        <!-- Kontainer Tabel Dinamis -->
        <div id="user-table-content">
            <?= view('superadmin/user/table_content', ['users' => $users, 'pager' => $pager]) ?>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script> 
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

<script>
$(document).ready(function() {
    
    // Fungsi umum untuk memuat data tabel melalui AJAX (untuk Search dan Paginasi)
    function loadTableData(searchQuery, pageUrl) {
        const url = pageUrl || '<?= base_url('superadmin/users/search_ajax') ?>';
        const pageParam = pageUrl ? url.split('page_users=')[1] : '';

        $.ajax({
            url: url,
            type: 'GET',
            data: { 
                search: searchQuery,
                page_users: pageParam
            }, 
            dataType: 'html',
            beforeSend: function() {
                $('#user-table-content').html('<div class="text-center p-5"><div class="spinner-border text-secondary" role="status"></div><p class="mt-2">Memuat Data...</p></div>');
            },
            success: function(response) {
                $('#user-table-content').html(response);
            },
            error: function() {
                $('#user-table-content').html('<div class="alert alert-danger">Gagal memuat data.</div>');
            }
        });
    }

    // ===================================
    // 1. LIVE SEARCH IMPLEMENTATION (on keyup)
    // ===================================
    $('#live-search').on('keyup', function() {
        const searchText = $(this).val();
        loadTableData(searchText, null); 
    });

    // ===================================
    // 2. PAGINATION IMPLEMENTATION (on click)
    // ===================================
    // Menggunakan event delegation untuk tautan paginasi yang dimuat dinamis
    $(document).on('click', '#user-table-content .pagination a', function(e) {
        e.preventDefault();
        const pageUrl = $(this).attr('href'); 
        const currentSearch = $('#live-search').val(); 

        loadTableData(currentSearch, pageUrl); 
    });
    
    // ===================================
    // 3. DELETE AJAX IMPLEMENTATION
    // ===================================
    $(document).on('click', '.btn-delete-user', function() {
        const userId = $(this).data('id');
        const userName = $(this).data('name');

        Swal.fire({
            title: 'Hapus Akun?',
            html: `Yakin ingin menghapus akun **${userName}**?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= base_url('superadmin/users/delete_ajax/') ?>' + userId,
                    type: 'GET', 
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            Swal.fire({
                                title: 'Terhapus!',
                                text: response.message,
                                icon: 'success'
                            }).then(() => {
                                window.location.href = response.redirect;
                            });
                        } else {
                            Swal.fire('Gagal!', response.message, 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error Jaringan!', 'Gagal menghubungi server.', 'error');
                    }
                });
            }
        });
    });
});
</script>

<?= $this->endSection() ?>
