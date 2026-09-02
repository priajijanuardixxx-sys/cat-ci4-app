<?php if (empty($examTypes)): ?>
  <div class="alert alert-info">Belum ada jenis ujian untuk event ini.</div>
  <?php else: ?>
    <table class="table table-striped table-bordered align-middle">
      <thead class="table-light">
        <tr>
          <th width="5%">#</th>
          <th>Nama Ujian</th>
          <th>Deskripsi</th>
          <th width="25%">Status Ujian</th>
          <th width="20%" class="text-center">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($examTypes)): ?>
          <?php foreach ($examTypes as $i => $exam): ?>
            <?php
            $settingId = $exam['setting_id'] ?? null;
            $status = $exam['status'] ?? null;

            $badge = '<span class="badge bg-secondary">Belum Mulai</span>';
            if ($status === 'started') $badge = '<span class="badge bg-success">Berlangsung</span>';
            elseif ($status === 'paused') $badge = '<span class="badge bg-warning text-dark">Dijeda</span>';
            elseif ($status === 'finished') $badge = '<span class="badge bg-dark">Selesai</span>';
            ?>
            <tr>
              <td><?= $i + 1 ?></td>
              <td><?= esc($exam['name']) ?></td>
              <td><?= esc($exam['description']) ?></td>
              <td id="status-<?= $settingId ?? 0 ?>">
                <?= $badge ?>
                <?php if ($settingId && in_array($status, ['started','paused'])): ?>
                  <div class="small text-muted mt-1">
                    Durasi: <?= $exam['duration'] ?> menit
                  </div>
                <?php endif; ?>
              </td>
              <td class="text-center">
                <?php if ($settingId): ?>
                  <?php if ($status === 'scheduled'): ?>
                    <button class="btn btn-success btn-sm btnStart" data-setting-id="<?= $settingId ?>">
                      <i class="bi bi-play-circle"></i> 
                    </button>
                    <?php elseif ($status === 'started'): ?>
                      <button class="btn btn-warning btn-sm btnPause" data-setting-id="<?= $settingId ?>">
                        <i class="bi bi-pause-circle"></i> 
                      </button>
                      <?php elseif ($status === 'paused'): ?>
                        <button class="btn btn-info btn-sm btnResume" data-setting-id="<?= $settingId ?>">
                          <i class="bi bi-play-btn"></i> 
                        </button>
                        <button class="btn btn-danger btn-sm btnReset" data-setting-id="<?= $settingId ?>">
                          <i class="bi bi-arrow-clockwise"></i> 
                        </button>
                      <?php endif; ?>
                    <?php endif; ?>

                    <button class="btn btn-primary btn-sm btn-setting" data-type-id="<?= $exam['id'] ?>">
                      <i class="bi bi-gear"></i>
                    </button>
                    <button class="btn btn-info btn-sm btn-participants" data-type-id="<?= $exam['id'] ?>">
                      <i class="bi bi-people"></i> 
                    </button>

                    <button class="btn btn-danger btn-sm btn-delete" data-type-id="<?= $exam['id'] ?>">
                      <i class="bi bi-trash"></i>
                    </button>
                  </td>
                </tr>
              <?php endforeach; ?>
              <?php else: ?>
                <tr><td colspan="5" class="text-center text-muted">Belum ada jenis ujian.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>

        <?php endif; ?>
