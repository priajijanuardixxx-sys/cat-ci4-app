<?= $this->extend('layout/admin_layout') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Kelola Kategori Soal (Grade)</h1>
    <button class="btn btn-primary fw-medium" data-bs-toggle="modal" data-bs-target="#categoryModal">
        <i class="fas fa-plus"></i> Tambah Kategori
    </button>
</div>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
<?php endif; ?>

<div class="card shadow p-4">
    <!-- Kontainer Tabel Dinamis -->
    <div id="category-table-content">
        <!-- Konten dimuat via AJAX loadCategoryIndex() -->
    </div>
</div>

<!-- Modal Tambah/Edit Kategori -->
<div class="modal fade" id="categoryModal" tabindex="-1" aria-labelledby="categoryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="categoryModalLabel">Tambah Kategori Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formCategory" action="<?= base_url('admin/categories/save') ?>" method="post">
                <div class="modal-body">
                    <?= csrf_field() ?>
                    <input type="hidden" name="id" id="category-id">
                    
                    <div class="mb-3">
                        <label for="category-name" class="form-label">Nama Kategori/Mata Uji</label>
                        <input type="text" class="form-control" id="category-name" name="name" required>
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="mb-3">
                        <label for="required-count" class="form-label">Jumlah Soal Wajib Diambil</label>
                        <input type="number" class="form-control" id="required-count" name="required_count" min="1" required>
                        <small class="form-text text-muted">Contoh: 20 (untuk UUD 1945).</small>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary fw-medium" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary fw-medium">Simpan Kategori</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    
    // URL AJAX untuk memuat daftar kategori
    const URL_INDEX = '<?= base_url('admin/ajax/categories/index') ?>';
    
    // Fungsi untuk memuat daftar kategori via AJAX
    window.loadCategoryIndex = function() {
        $('#category-table-content').html('<div class="text-center p-5"><div class="spinner-border text-primary" role="status"></div><p class="mt-2">Memuat Kategori...</p></div>');
        
        $.ajax({
            url: URL_INDEX,
            type: 'GET',
            dataType: 'html',
            success: function(response) {
                $('#category-table-content').html(response);
            },
            error: function() {
                $('#category-table-content').html('<div class="alert alert-danger">Gagal memuat daftar kategori.</div>');
            }
        });
    }

    // Panggil saat View Index dimuat
    loadCategoryIndex();
    
    // Handler untuk menampilkan Modal Tambah
    $('#categoryModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var modal = $(this);
        
        if (button.hasClass('btn-edit-category')) {
            // Mode EDIT
            var id = button.data('id');
            var name = button.data('name');
            var count = button.data('count');
            
            modal.find('.modal-title').text('Edit Kategori: ' + name);
            modal.find('#category-id').val(id);
            modal.find('#category-name').val(name);
            modal.find('#required-count').val(count);
        } else {
            // Mode TAMBAH
            modal.find('.modal-title').text('Tambah Kategori Baru');
            modal.find('#formCategory')[0].reset();
            modal.find('#category-id').val('');
        }
        // Bersihkan validasi sebelumnya
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').text('');
    });


    // Handler AJAX untuk Simpan/Update Kategori
    $('#formCategory').submit(function(e) {
        e.preventDefault();
        
        $.ajax({
            type: 'POST',
            url: $(this).attr('action'),
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').text('');

                if (response.status === 'success') {
                    Swal.fire('Berhasil!', response.message, 'success');
                    $('#categoryModal').modal('hide');
                    loadCategoryIndex(); // Muat ulang tabel
                } else if (response.status === 'error' && response.errors) {
                    // Tampilkan error validasi di form
                    $.each(response.errors, function(key, value) {
                        $('#category-' + key).addClass('is-invalid').next('.invalid-feedback').text(value);
                        $('#required-' + key).addClass('is-invalid').next('.invalid-feedback').text(value);
                    });
                } else {
                    Swal.fire('Gagal!', response.message, 'error');
                }
            },
            error: function() {
                Swal.fire('Error Jaringan!', 'Gagal menghubungi server.', 'error');
            }
        });
    });
});
</script>

<?= $this->endSection() ?>
