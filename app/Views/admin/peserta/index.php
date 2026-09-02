<?= $this->extend('layout/admin_layout') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><?= $title ?></h1>
    <a href="<?= base_url('admin/peserta/create') ?>" class="btn btn-primary fw-medium">
        <i class="fas fa-plus"></i> Daftarkan Peserta Baru
    </a>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
<?php endif; ?>

<div class="card shadow p-4">
    <div class="row mb-3">
        <div class="col-md-6">
            <input type="text" id="live-search" class="form-control" placeholder="Cari berdasarkan Nama atau Username Peserta...">
        </div>
    </div>
    
    <!-- Kontainer Tabel Dinamis untuk Live Search + Paginasi -->
    <div id="peserta-table-content">
        <?= view('admin/peserta/table_content', ['users' => $users, 'pager' => $pager]) ?>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script> 
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- SCRIPT AJAX UNTUK SEARCH, PAGINASI, DAN DELETE PESERTA -->
<script>
$(document).ready(function() {
    
    // Fungsi umum untuk memuat data tabel melalui AJAX (untuk Search dan Paginasi)
    function loadTableData(searchQuery, pageUrl) {
        // Menggunakan 'page_peserta' sebagai parameter paging
        const url = pageUrl || '<?= base_url('admin/peserta/search_ajax') ?>';
        const pageParam = pageUrl ? url.split('page_peserta=')[1] : '';

        $.ajax({
            url: url,
            type: 'GET',
            data: { 
                search: searchQuery,
                page_peserta: pageParam
            }, 
            dataType: 'html',
            beforeSend: function() {
                $('#peserta-table-content').html('<div class="text-center p-5"><div class="spinner-border text-secondary" role="status"></div><p class="mt-2">Memuat Data...</p></div>');
            },
            success: function(response) {
                $('#peserta-table-content').html(response);
            },
            error: function() {
                $('#peserta-table-content').html('<div class="alert alert-danger">Gagal memuat data.</div>');
            }
        });
    }

    // 1. LIVE SEARCH IMPLEMENTATION (on keyup)
    $('#live-search').on('keyup', function() {
        const searchText = $(this).val();
        loadTableData(searchText, null); // Muat data dengan query baru, halaman 1
    });

    // 2. PAGINATION IMPLEMENTATION (on click)
    $(document).on('click', '#peserta-table-content .pagination a', function(e) {
        e.preventDefault();
        const pageUrl = $(this).attr('href'); 
        const currentSearch = $('#live-search').val(); 

        loadTableData(currentSearch, pageUrl); 
    });
    
    // 3. DELETE AJAX IMPLEMENTATION
    $(document).on('click', '.btn-delete-peserta', function() {
        const userId = $(this).data('id');
        const userName = $(this).data('name');

        Swal.fire({
            title: 'Hapus Peserta?',
            html: `Yakin ingin menghapus peserta <strong>${userName}</strong>?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Catatan: Belum buat method delete_ajax di Controller, akan menggunakan rute admin/peserta/delete/id
                // Kita harus membuat DELETE route untuk Peserta di langkah selanjutnya.
                $.ajax({
                    url: '<?= base_url('admin/peserta/delete/') ?>' + userId,
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
                            Swal.fire('Gagal Hapus!', response.message, 'error');
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
