<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold">Total Soal: <?= esc($total) ?></h5>
</div>

<div class="table-responsive">
    <table class="table table-striped table-hover align-middle">
        <thead class="bg-dark text-white">
            <tr>
                <th>No.</th>
                <th>Kategori (Grade)</th>
                <th>Teks Soal</th>
                <th>Jawaban Benar</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if (!empty($questions)):
                $i = 1 + (($page - 1) * $perPage);
                foreach ($questions as $q):
            ?>
                <tr>
                    <td><?= $i++ ?></td>
                    <td><span class="badge bg-info fw-medium"><?= esc($q['category_name'] ?? 'Tidak Terikat') ?></span></td>
                    <td><?= esc(substr($q['question_text'], 0, 100)) ?><?= strlen($q['question_text']) > 100 ? '...' : '' ?></td>
                    <td><span class="badge bg-success fw-medium"><?= esc($q['correct_answer']) ?></span></td>
                    <td>
                        <a href="<?= base_url('admin/questions/edit/' . $q['id']) ?>" class="btn btn-sm btn-warning fw-medium">Edit</a>
                        <button type="button" class="btn btn-sm btn-danger fw-medium btn-delete-question" data-id="<?= $q['id'] ?>">Hapus</button>
                    </td>
                </tr>
            <?php
                endforeach;
            else:
            ?>
                <tr>
                    <td colspan="5" class="text-center text-secondary">
                        Belum ada soal terdaftar untuk Event ini.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Paginasi -->
<div class="d-flex justify-content-center">
    <?= $pager ?> 
</div>


