<?= $this->extend('layout/admin_layout') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Kelola Event Ujian</h1>
    <a href="<?= base_url('superadmin/events/create') ?>" class="btn btn-primary fw-medium">
        <i class="fas fa-plus"></i> Tambah Event Baru
    </a>
</div>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
<?php endif; ?>
<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
<?php endif; ?>

<div class="card shadow p-4">
    <div class="row mb-3">
        <div class="col-md-6">
            <input type="text" id="live-search" class="form-control" placeholder="Cari berdasarkan Nama Event, Penyelenggara, atau Panitia...">
        </div>
    </div>
    
    <!-- Kontainer Tabel Dinamis untuk Live Search + Paginasi -->
    <div id="event-table-content">
        <?= view('superadmin/event/table_content', ['events' => $events, 'pager' => $pager]) ?>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script> 
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- SCRIPT AJAX UNTUK SEARCH, PAGINASI, DAN DELETE EVENT -->
<script>
$(document).ready(function() {
    
    // Fungsi umum untuk memuat data tabel melalui AJAX (untuk Search dan Paginasi)
    function loadTableData(searchQuery, pageUrl) {
        // Menggunakan 'page_events' sebagai parameter paging
        const url = pageUrl || '<?= base_url('superadmin/events/search_ajax') ?>';
        const pageParam = pageUrl ? url.split('page_events=')[1] : '';

        $.ajax({
            url: url,
            type: 'GET',
            data: { 
                search: searchQuery,
                page_events: pageParam
            }, 
            dataType: 'html',
            beforeSend: function() {
                $('#event-table-content').html('<div class="text-center p-5"><div class="spinner-border text-secondary" role="status"></div><p class="mt-2">Memuat Data...</p></div>');
            },
            success: function(response) {
                $('#event-table-content').html(response);
            },
            error: function() {
                $('#event-table-content').html('<div class="alert alert-danger">Gagal memuat data.</div>');
            }
        });
    }

    // 1. LIVE SEARCH IMPLEMENTATION (on keyup)
    $('#live-search').on('keyup', function() {
        const searchText = $(this).val();
        loadTableData(searchText, null); // Muat data dengan query baru, halaman 1
    });

    // 2. PAGINATION IMPLEMENTATION (on click)
    // Menggunakan event delegation karena tautan paginasi dimuat dinamis oleh AJAX
    $(document).on('click', '#event-table-content .pagination a', function(e) {
        e.preventDefault();
        const pageUrl = $(this).attr('href'); 
        const currentSearch = $('#live-search').val(); 

        loadTableData(currentSearch, pageUrl); 
    });
    
    // 3. DELETE AJAX IMPLEMENTATION
    $(document).on('click', '.btn-delete-event', function() {
        const eventId = $(this).data('id');
        const eventName = $(this).data('name');

        Swal.fire({
            title: 'Hapus Event?',
            html: `Yakin ingin menghapus Event <strong>${eventName}</strong>? <br><span class="text-danger fw-bold">SEMUA data terkait (soal, peserta) akan ikut terhapus.</span>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus Saja!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= base_url('superadmin/events/delete_ajax/') ?>' + eventId,
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
