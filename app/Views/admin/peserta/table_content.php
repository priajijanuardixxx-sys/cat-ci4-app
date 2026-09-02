<div class="table-responsive">
    <table class="table table-striped table-hover align-middle">
        <thead class="bg-dark text-white">
            <tr>
                <th>ID</th>
                <th>Nama Peserta</th>
                <th>Username</th>
                <th>Event</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($users)): ?>
                <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= $user['id'] ?></td>
                    <td><?= $user['full_name'] ?></td>
                    <td><?= $user['username'] ?></td>
                    <td>
                        <span class="badge bg-primary fw-medium">Event: <?= session()->get('event_name') ?></span>
                    </td>
                    <td>
                        <a href="<?= base_url('admin/peserta/edit/' . $user['id']) ?>" class="btn btn-sm btn-warning fw-medium">Edit</a>
                        <button type="button" class="btn btn-sm btn-danger fw-medium btn-delete-peserta" data-id="<?= $user['id'] ?>" data-name="<?= $user['full_name'] ?>">Hapus</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="text-center text-secondary">Tidak ada peserta terdaftar untuk Event ini.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Tautan Paginasi -->
<div class="d-flex justify-content-center">
    <?= $pager->links('peserta', 'default_full') ?> 
</div>
