<form id="formExamSetting">
    <?= csrf_field() ?>
    <input type="hidden" name="exam_type_id" value="<?= $setting['exam_type_id'] ?>">
    <input type="hidden" name="id" value="<?= $setting['id'] ?? '' ?>">
    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label class="form-label fw-medium">Durasi Ujian (menit)</label>
                <input type="number" name="duration" class="form-control"
                       value="<?= esc($setting['duration']) ?>" min="10" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-medium">Passing Grade</label>
                <input type="number" name="passing_grade" class="form-control"
                       value="<?= esc($setting['passing_grade']) ?>" min="0" max="100" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-medium">Mode Ujian</label>
                <select name="mode" class="form-select">
                    <option value="online" <?= $setting['mode'] == 'online' ? 'selected' : '' ?>>Online</option>
                    <option value="offline" <?= $setting['mode'] == 'offline' ? 'selected' : '' ?>>Offline</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label fw-medium">Waktu Pelaksanaan</label>
                <div class="d-flex gap-2">
                    <input type="datetime-local" name="start_time" class="form-control"
                           value="<?= $setting['start_time'] ? date('Y-m-d\TH:i', strtotime($setting['start_time'])) : '' ?>">
                    <input type="datetime-local" name="end_time" class="form-control"
                           value="<?= $setting['end_time'] ? date('Y-m-d\TH:i', strtotime($setting['end_time'])) : '' ?>">
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-check form-switch mb-3">
                <input class="form-check-input" type="checkbox" name="randomize_questions" id="randomize_questions"
                       value="1" <?= $setting['randomize_questions'] ? 'checked' : '' ?>>
                <label class="form-check-label fw-medium" for="randomize_questions">
                    Acak Soal
                </label>
            </div>

            <div class="form-check form-switch mb-3">
                <input class="form-check-input" type="checkbox" name="show_result" id="show_result"
                       value="1" <?= $setting['show_result'] ? 'checked' : '' ?>>
                <label class="form-check-label fw-medium" for="show_result">
                    Tampilkan Hasil Setelah Ujian
                </label>
            </div>

            <div class="mb-3">
                <label class="form-label fw-medium">Kategori Soal yang Dihubungkan</label>
                <div class="border rounded p-2" style="max-height: 180px; overflow-y: auto;">
                    <?php if (!empty($categories)): ?>
                        <?php foreach ($categories as $cat): ?>
                            <div class="form-check">
                                <input class="form-check-input"
                                       type="checkbox"
                                       name="categories[]"
                                       id="cat_<?= $cat['id'] ?>"
                                       value="<?= $cat['id'] ?>"
                                       <?= in_array($cat['id'], $linkedIds) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="cat_<?= $cat['id'] ?>">
                                    <?= esc($cat['name']) ?>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-muted fst-italic">Belum ada kategori tersedia.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="text-end mt-3">
        <button type="submit" class="btn btn-success px-4 fw-medium">
            <i class="bi bi-save"></i> Simpan Pengaturan
        </button>
    </div>
</form>

<script>
$(document).ready(function() {
    $('#formExamSetting').on('submit', function(e) {
        e.preventDefault();

        $.ajax({
            url: '<?= base_url('admin/exams/save_setting') ?>',
            method: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(res) {
                if (res.status === 'success') {
                    Swal.fire('Tersimpan!', res.message, 'success');
                    $('#examSettingModal').modal('hide');
                } else {
                    Swal.fire('Gagal!', res.message, 'error');
                }
            },
            error: function() {
                Swal.fire('Error', 'Terjadi kesalahan koneksi ke server.', 'error');
            }
        });
    });
});
</script>
