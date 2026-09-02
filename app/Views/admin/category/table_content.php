<div class="table-responsive">
    <table class="table table-striped table-hover align-middle">
        <thead class="bg-dark text-white">
            <tr>
                <th>ID</th>
                <th>Nama Kategori</th>
                <th>Jumlah Soal</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($categories)): ?>
                <?php foreach ($categories as $cat): ?>
                    <tr>
                        <td><?= $cat['id'] ?></td>
                        <td><?= esc($cat['name']) ?></td>
                        <td>
                            <?php if ($cat['has_questions']): ?>
                                <button type="button"
                                class="btn btn-sm btn-info btn-preview-questions"
                                data-id="<?= $cat['id'] ?>">
                                <?= $cat['question_count'] ?> Soal
                            </button>
                            <?php else: ?>
                                <span class="badge bg-secondary">0 Soal</span>
                            <?php endif; ?>
                        </td>
                        <td>
                         <button type="button"
                         class="btn btn-sm btn-warning btn-edit-category"
                         data-bs-toggle="modal" data-bs-target="#categoryModal"
                         data-id="<?= $cat['id'] ?>"
                         data-name="<?= esc($cat['name']) ?>"
                         data-count="<?= $cat['required_count'] ?>">
                         Edit
                     </button>

                     <button type="button"
                     class="btn btn-sm btn-danger btn-delete-category"
                     data-id="<?= $cat['id'] ?>"
                     data-name="<?= esc($cat['name']) ?>">
                     Hapus
                 </button>
             </td>
         </tr>
     <?php endforeach; ?>
     <?php else: ?>
        <tr>
            <td colspan="4" class="text-center text-secondary">
                Belum ada kategori terdaftar.
            </td>
        </tr>
    <?php endif; ?>
</tbody>
</table>
</div>

<!-- Modal Preview Soal -->
<div class="modal fade" id="previewQuestionsModal" tabindex="-1" aria-labelledby="previewQuestionsLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title" id="previewQuestionsLabel">Daftar Soal</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="previewQuestionsContent">
                <div class="text-center text-muted">Memuat...</div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {

    // 🔹 Preview soal dalam modal
    $(document).on('click', '.btn-preview-questions', function() {
        const categoryId = $(this).data('id');
        const modal = $('#previewQuestionsModal');
        const content = $('#previewQuestionsContent');

        modal.modal('show');
        content.html('<div class="text-center text-muted p-3">Memuat...</div>');

        $.ajax({
            url: '<?= base_url('admin/categories/preview_questions/') ?>' + categoryId,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    content.html(response.html);
                } else {
                    content.html('<div class="text-danger p-3">' + response.message + '</div>');
                }
            },
            error: function() {
                content.html('<div class="text-danger p-3">Gagal memuat data soal.</div>');
            }
        });
    });


    // 🔹 Hapus kategori
    $(document).on('click', '.btn-delete-category', function() {
        const categoryId = $(this).data('id');
        const categoryName = $(this).data('name');

        Swal.fire({
            title: 'Hapus Kategori?',
            html: `Yakin ingin menghapus kategori <strong>${categoryName}</strong>? <br><span class="text-danger fw-bold">Semua soal yang terikat pada kategori ini juga akan terhapus.</span>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= base_url('admin/categories/delete_ajax/') ?>' + categoryId,
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            Swal.fire('Terhapus!', response.message, 'success');
                            window.loadCategoryIndex(); // Refresh tabel
                        } else {
                            Swal.fire('Gagal!', response.message, 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error!', 'Gagal menghubungi server.', 'error');
                    }
                });
            }
        });
    });

});
</script>
