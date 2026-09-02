<?= $this->extend('layout/admin_layout') ?>

<?= $this->section('content') ?>

<!-- Kontainer Konten Dinamis -->
<div id="main-content" class="px-4 py-4 main-content-desktop"> 
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><?= $title ?></h1>
    </div>

    <div class="card shadow p-4">
        <div id="validation-errors" class="alert alert-danger d-none"></div>

        <!-- Form submission menggunakan AJAX POST ke events/save -->
        <form id="formCreateEvent" action="<?= base_url('superadmin/events/save') ?>" method="post">
            <?= csrf_field() ?>
            
            <div class="mb-3">
                <label for="name" class="form-label">Nama Event Ujian</label>
                <input type="text" class="form-control" id="name" name="name" value="<?= old('name') ?>" required>
            </div>

            <div class="mb-3">
                <label for="organizer" class="form-label">Penyelenggara (Institusi)</label>
                <input type="text" class="form-control" id="organizer" name="organizer" value="<?= old('organizer') ?>" required>
            </div>
            
            <div class="mb-3">
                <label for="location" class="form-label">Lokasi Ujian</label>
                <input type="text" class="form-control" id="location" name="location" value="<?= old('location') ?>" required>
            </div>

            <div class="mb-3">
                <label for="panitia_user_id" class="form-label">Panitia Utama Bertanggung Jawab</label>
                <select class="form-select" id="panitia_user_id" name="panitia_user_id">
                    <option value="">-- Pilih Panitia (Opsional) --</option>
                    <?php foreach ($panitia_users as $user): ?>
                        <option value="<?= $user['id'] ?>" <?= old('panitia_user_id') == $user['id'] ? 'selected' : '' ?>>
                            <?= $user['full_name'] ?> (<?= $user['username'] ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
                <small class="form-text text-muted">Hanya menampilkan user dengan role "Panitia". Panitia Utama akan mendapatkan hak akses tertinggi di Event ini.</small>
            </div>

            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" value="1" id="is_active" name="is_active" checked>
                <label class="form-check-label" for="is_active">
                    Aktifkan Event ini
                </label>
            </div>

            <div class="d-flex justify-content-end">
                <a href="<?= base_url('superadmin/events') ?>" class="btn btn-secondary me-2">Batal</a>
                <button type="submit" class="btn btn-success">Simpan Event</button>
            </div>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script> 
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Script AJAX Submission Event Create -->
<script>
$(document).ready(function() {
    $('#formCreateEvent').submit(function(e) {
        e.preventDefault(); 
        $('#validation-errors').addClass('d-none').html(''); 

        $.ajax({
            type: 'POST',
            url: $(this).attr('action'),
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    Swal.fire({
                        title: 'Berhasil!',
                        html: response.message,
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        window.location.href = response.redirect;
                    });
                } else if (response.status === 'error') {
                    let errorHtml = '<h6>Gagal: Periksa Input</h6><ul>';
                    if (response.errors) {
                        $.each(response.errors, function(key, value) {
                            errorHtml += '<li>' + value + '</li>';
                        });
                        errorHtml += '</ul>';
                        
                        $('#validation-errors').html(errorHtml).removeClass('d-none');
                    } else {
                         Swal.fire('Gagal!', response.message, 'error');
                    }
                }
            },
            error: function(xhr) {
                Swal.fire('Error Jaringan!', 'Terjadi kesalahan saat menghubungi server.', 'error');
            }
        });
    });
});
</script>

<?= $this->endSection() ?>
