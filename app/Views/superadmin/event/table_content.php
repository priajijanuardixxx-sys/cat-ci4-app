<div class="table-responsive">
    <table class="table table-striped table-hover align-middle">
        <thead class="bg-dark text-white">
            <tr>
                <th>ID</th>
                <th>Nama Event</th>
                <th>Penyelenggara</th>
                <th>Panitia Utama</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($events)): ?>
                <?php foreach ($events as $event): ?>
                <tr>
                    <td><?= $event['id'] ?></td>
                    <td><?= $event['name'] ?></td>
                    <td><?= $event['organizer'] ?></td>
                    <td><?= $event['panitia_name'] ?? 'Belum Ditunjuk' ?></td>
                    <td>
                        <span class="badge bg-<?= $event['is_active'] ? 'success' : 'danger' ?> fw-medium">
                            <?= $event['is_active'] ? 'Aktif' : 'Nonaktif' ?>
                        </span>
                    </td>
                    <td>
                        <a href="<?= base_url('superadmin/events/edit/' . $event['id']) ?>" class="btn btn-sm btn-warning fw-medium">Edit</a>
                        <button type="button" class="btn btn-sm btn-danger fw-medium btn-delete-event" data-id="<?= $event['id'] ?>" data-name="<?= $event['name'] ?>">Hapus</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" class="text-center text-secondary">Tidak ada Event yang ditemukan.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Tautan Paginasi -->
<div class="d-flex justify-content-center">
    <!-- Kunci: Menggunakan links() dengan group 'events' -->
    <?= $pager->links('events', 'default_full') ?> 
</div>
