<?= $this->extend('layout/admin_layout') ?>

<?= $this->section('content') ?>


<!-- Kontainer Konten Dinamis -->
<div id="main-content" class="px-4 py-4 main-content-desktop">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><?= $title ?></h1>
        <a href="<?= base_url('superadmin/events') ?>" class="btn btn-secondary fw-medium">Kembali ke Daftar</a>
    </div>

    <div class="card shadow p-4">
        <div id="validation-errors" class="alert alert-danger d-none"></div>

        <!-- Form submission menggunakan AJAX POST ke events/save -->
        <form id="formEditEvent" action="<?= base_url('superadmin/events/save') ?>" method="post">
            <?= csrf_field() ?>
            <!-- Hidden input untuk ID (menandakan ini adalah operasi UPDATE) -->
            <input type="hidden" name="id" value="<?= $event['id'] ?>">
            
            <div class="mb-3">
                <label for="name" class="form-label">Nama Event Ujian</label>
                <!-- Menggunakan data Event yang sudah ada -->
                <input type="text" class="form-control" id="name" name="name" value="<?= old('name', $event['name']) ?>" required>
            </div>

            <div class="mb-3">
                <label for="organizer" class="form-label">Penyelenggara (Institusi)</label>
                <input type="text" class="form-control" id="organizer" name="organizer" value="<?= old('organizer', $event['organizer']) ?>" required>
            </div>
            
            <div class="mb-3">
                <label for="location" class="form-label">Lokasi Ujian</label>
                <input type="text" class="form-control" id="location" name="location" value="<?= old('location', $event['location']) ?>" required>
            </div>

            <div class="mb-3">
                <label for="panitia_user_id" class="form-label">Panitia Utama Bertanggung Jawab</label>
                <select class="form-select" id="panitia_user_id" name="panitia_user_id">
                    <option value="">-- Pilih Panitia (Opsional) --</option>
                    <?php foreach ($panitia_users as $user): ?>
                        <option value="<?= $user['id'] ?>" <?= old('panitia_user_id', $event['panitia_user_id']) == $user['id'] ? 'selected' : '' ?>>
                            <?= $user['full_name'] ?> (<?= $user['username'] ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" value="1" id="is_active" name="is_active" <?= old('is_active', $event['is_active']) ? 'checked' : '' ?>>
                <label class="form-check-label" for="is_active">
                    Aktifkan Event ini
                </label>
            </div>

            <div class="d-flex justify-content-end">
                <a href="<?= base_url('superadmin/events') ?>" class="btn btn-secondary me-2 fw-medium">Batal</a>
                <button type="submit" class="btn btn-success fw-medium">Simpan Perubahan</button>
            </div>

        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script> 
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- SCRIPT AJAX SUBMISSION (Mirip Create) -->
<script>
$(document).ready(function() {
    $('#formEditEvent').submit(function(e) {
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
