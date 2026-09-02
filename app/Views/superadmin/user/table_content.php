<div class="table-responsive">
    <table class="table table-striped table-hover align-middle">
        <thead class="bg-dark text-white">
            <tr>
                <th>ID</th>
                <th>Nama Lengkap</th>
                <th>Username</th>
                <th>Peran</th>
                <th>Event Terikat</th>
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
                        <span class="badge bg-<?= ($user['role_name'] == 'Panitia' ? 'warning' : 'info') ?>">
                            <?= $user['role_name'] ?>
                        </span>
                    </td>
                    <td>
                        <?= $user['event_name'] ?? '<span class="text-danger">TIDAK TERIKAT</span>' ?>
                    </td>
                    <td>
                        <a href="<?= base_url('superadmin/users/edit/' . $user['id']) ?>" class="btn btn-sm btn-warning">Edit</a>
                        <button type="button" class="btn btn-sm btn-danger btn-delete-user" data-id="<?= $user['id'] ?>" data-name="<?= $user['full_name'] ?>">Hapus</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" class="text-center">Tidak ada akun yang ditemukan.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div class="d-flex justify-content-center">
    <?= $pager->links('users', 'default_full') ?> 
</div>