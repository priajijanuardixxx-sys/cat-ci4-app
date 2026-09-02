<?= $this->extend('layout/admin_layout') ?>

<?= $this->section('content') ?>

<!-- Kontainer Konten Dinamis -->
<div id="main-content" class="px-4 py-4 main-content-desktop">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><?= $title ?></h1>
        <a href="<?= base_url('admin/peserta') ?>" class="btn btn-secondary">Kembali ke Daftar</a>
    </div>

    <div class="card shadow p-4">
        <!-- Kontainer untuk menampilkan error validasi AJAX -->
        <div id="validation-errors" class="alert alert-danger d-none"></div>

        <!-- Form submission menggunakan AJAX POST ke admin/peserta/save -->
        <form id="formCreatePeserta" action="<?= base_url('admin/peserta/save') ?>" method="post">
            <?= csrf_field() ?>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="full_name" class="form-label">Nama Lengkap Peserta</label>
                    <input type="text" class="form-control" id="full_name" name="full_name" required value="<?= old('full_name') ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="username" class="form-label">Username Peserta</label>
                    <input type="text" class="form-control" id="username" name="username" required value="<?= old('username') ?>">
                    <small class="form-text text-muted">Akan digunakan untuk login ujian.</small>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Event Ditugaskan</label>
                    <input type="text" class="form-control" disabled value="<?= session()->get('event_name') ?>">
                    <small class="form-text text-danger">Peserta ini otomatis terikat ke Event Anda.</small>
                </div>
            </div>

            <div class="d-flex justify-content-end mt-4">
                <button type="submit" class="btn btn-success">Daftarkan Peserta</button>
            </div>

        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script> 
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Script AJAX Submission (mirip SuperAdmin/User/create.php) -->
<script>
$(document).ready(function() {
    $('#formCreatePeserta').submit(function(e) {
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
                     // Tampilkan error di card
                     $('.card.shadow').prepend('<div class="alert alert-danger" id="temp-error-alert">'+errorHtml+'</div>');
                     setTimeout(() => $('#temp-error-alert').remove(), 5000);
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
