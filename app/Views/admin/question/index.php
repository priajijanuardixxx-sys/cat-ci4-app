<?= $this->extend('layout/admin_layout') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Kelola Bank Soal</h1>
    <!-- Tombol Import Excel dan Tambah Soal Manual -->
    <div class="d-flex">
        <button class="btn btn-info fw-medium me-2" data-bs-toggle="modal" data-bs-target="#importModal">
            <i class="fas fa-file-excel me-1"></i> Import Excel
        </button>
        <a href="<?= base_url('admin/questions/create') ?>" class="btn btn-primary fw-medium">
            <i class="fas fa-plus"></i> Tambah Soal Baru
        </a>
    </div>
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
            <input type="text" id="live-search" class="form-control" placeholder="Cari Soal, Kategori, atau Jawaban...">
        </div>
    </div>
    
    <!-- Kontainer utama untuk dimuat oleh AJAX -->
    <div id="question-content-area">
       <!-- Konten akan dimuat di sini oleh AJAX loadQuestionsIndex() -->
    </div>
</div>

<!-- Modal Import Excel -->
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title fw-bold" id="importModalLabel">Import Bank Soal dari Excel</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <!-- PENTING: enctype="multipart/form-data" harus ada untuk upload file -->
            <form action="<?= base_url('admin/questions/import') ?>" method="post" enctype="multipart/form-data">
                <div class="modal-body">
                    <?= csrf_field() ?>
                    <p class="text-secondary small">Semua soal di file Excel akan dimasukkan ke kategori yang Anda pilih di bawah ini.</p>
                    <hr>
                    
                    <div class="mb-3">
                        <label for="import_category_id" class="form-label fw-medium">Pilih Kategori Tujuan</label>
                        <select class="form-select" id="import_category_id" name="category_id" required>
                            <option value="">-- Pilih Kategori --</option>
                            <?php if (!empty($categories)): ?>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>">
                                        <?= $cat['name'] ?> (<?= $cat['required_count'] ?> Soal Wajib)
                                    </option>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <option disabled>Mohon buat Kategori Soal terlebih dahulu.</option>
                            <?php endif; ?>
                        </select>
                        <div class="text-danger small mt-2">Kolom Kategori di Excel akan diabaikan.</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="excel_file" class="form-label fw-medium">Pilih File (.xls, .xlsx, .csv)</label>
                        <input class="form-control" type="file" id="excel_file" name="excel_file" required accept=".xls, .xlsx, .csv">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary fw-medium" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-info fw-medium">Upload & Import</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    
    // Fungsi umum untuk memuat daftar soal via AJAX (Live Search & Paginasi)
    function loadQuestionIndex(searchQuery = '', pageUrl = null) {
        const URL_AJAX = '<?= base_url('admin/ajax/questions/index') ?>';
        const pageParam = pageUrl ? pageUrl.split('page_questions=')[1] : '';

        $('#question-content-area').html('<div class="text-center p-5"><div class="spinner-border text-primary" role="status"></div><p class="mt-2">Memuat Bank Soal...</p></div>');
        
        $.ajax({
            url: URL_AJAX,
            type: 'GET',
            data: { 
                search: searchQuery,
                page_questions: pageParam
            },
            dataType: 'html',
            success: function(response) {
                $('#question-content-area').html(response);
            },
            error: function() {
                $('#question-content-area').html('<div class="alert alert-danger">Gagal memuat daftar soal.</div>');
            }
        });
    }

    // Panggil saat View Index dimuat
    loadQuestionIndex(); 
    
    // 1. LIVE SEARCH IMPLEMENTATION (on keyup)
    $('#live-search').on('keyup', function() {
        const searchText = $(this).val();
        loadQuestionIndex(searchText, null); // Muat data dengan query baru, halaman 1
    });
    
    // 2. PAGINATION IMPLEMENTATION (on click) - Gunakan event delegation
    $(document).on('click', '#question-content-area .pagination a', function(e) {
        e.preventDefault();
        const pageUrl = $(this).attr('href'); 
        const currentSearch = $('#live-search').val(); 

        loadQuestionIndex(currentSearch, pageUrl); 
    });


    // 3. DELETE AJAX IMPLEMENTATION (Menggunakan event delegation)
    $(document).on('click', '.btn-delete-question', function() {
        const questionId = $(this).data('id');

        Swal.fire({
            title: 'Hapus Soal?',
            text: "Soal ini akan dihapus permanen dari Bank Soal.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!'
        }).then((result) => {
            if (result.isConfirmed) {
                // Lakukan request AJAX ke Controller DELETE (full refresh)
                window.location.href = '<?= base_url('admin/questions/delete/') ?>' + questionId;
            }
        });
    });
});
</script>

<?= $this->endSection() ?>
