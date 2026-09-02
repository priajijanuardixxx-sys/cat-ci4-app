<?= $this->extend('layout/admin_layout') ?>

<?= $this->section('content') ?>

<div id="main-content" class="px-4 py-4 main-content-desktop">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><?= $title ?></h1>
        <a href="<?= base_url('superadmin/users') ?>" class="btn btn-secondary">Kembali ke Daftar</a>
    </div>

    <div class="card shadow p-4">
        <!-- Kontainer untuk menampilkan error validasi AJAX -->
        <div id="validation-errors" class="alert alert-danger d-none"></div>

        <!-- Form submission menggunakan AJAX POST ke users/save -->
        <form id="formEditUser" action="<?= base_url('superadmin/users/save') ?>" method="post">
            <?= csrf_field() ?>
            <!-- Hidden input untuk ID (menandakan ini adalah operasi UPDATE) -->
            <input type="hidden" name="id" value="<?= $user['id'] ?>">
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="full_name" class="form-label">Nama Lengkap</label>
                    <input type="text" class="form-control" id="full_name" name="full_name" required value="<?= old('full_name', $user['full_name']) ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="username" class="form-label">Username (Login)</label>
                    <!-- Username diisi dari data yang sudah ada -->
                    <input type="text" class="form-control" id="username" name="username" required value="<?= old('username', $user['username']) ?>">
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="password" class="form-label">Password <small class="text-muted">(Kosongkan jika tidak ingin diubah)</small></label>
                    <!-- Password dikosongkan secara default untuk edit -->
                    <input type="password" class="form-control" id="password" name="password">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="role_id" class="form-label">Peran (Role)</label>
                    <select class="form-select" id="role_id" name="role_id" required>
                        <option value="">-- Pilih Peran --</option>
                        <?php foreach ($roles as $role): ?>
                            <option value="<?= $role['id'] ?>" <?= old('role_id', $user['role_id']) == $role['id'] ? 'selected' : '' ?>>
                                <?= $role['name'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Event Assignment -->
            <div class="mb-3" id="event-assignment-field">
                <label for="event_id" class="form-label">Tugaskan ke Event</label>
                <select class="form-select" id="event_id" name="event_id">
                    <option value="">-- Tidak Terikat Event (Opsional) --</option>
                    <?php foreach ($events as $event): ?>
                        <option value="<?= $event['id'] ?>" <?= old('event_id', $user['event_id']) == $event['id'] ? 'selected' : '' ?>>
                            <?= $event['name'] ?> (<?= $event['organizer'] ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
                <small class="form-text text-muted">Panitia dan Korektor harus ditugaskan ke sebuah Event untuk dapat mengelola data.</small>
            </div>

            <div class="d-flex justify-content-end mt-4">
                <a href="<?= base_url('superadmin/users') ?>" class="btn btn-secondary me-2">Batal</a>
                <button type="submit" class="btn btn-success">Perbarui Akun</button>
            </div>

        </form>
    </div>
</div>

<!-- SCRIPT AJAX SUBMISSION -->
<script>
$(document).ready(function() {
    // Handler untuk submission AJAX
    $('#formEditUser').submit(function(e) {
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
                    // Tampilkan pesan error validasi
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
