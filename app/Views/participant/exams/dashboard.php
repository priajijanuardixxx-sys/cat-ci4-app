<?= $this->extend('layout/participant_layout') ?>
<?= $this->section('content') ?>

<section class="exam-dashboard py-5">
    <div class="container">
        <h2 class="mb-4">Dashboard Ujian</h2>
        <div class="row g-4" id="exam-cards">
            <?php if (!empty($exams)): ?>
                <?php foreach ($exams as $i => $exam): ?>
                    <?php
                        $settingId = $exam['setting_id'] ?? null;
                        $status = $exam['status'] ?? null;
                        $badge = '<span class="badge bg-secondary">Belum Dijadwalkan</span>';
                        $btnDisabled = 'disabled';
                        $btnText = 'Masuk Ujian';

                        if ($settingId) {
                            if ($status === 'scheduled') {
                                $badge = '<span class="badge bg-secondary">Belum Mulai</span>';
                                $btnDisabled = 'disabled';
                            } elseif ($status === 'started') {
                                $badge = '<span class="badge bg-success">Berlangsung</span>';
                                $btnDisabled = '';
                            } elseif ($status === 'paused') {
                                $badge = '<span class="badge bg-warning text-dark">Dijeda</span>';
                                $btnDisabled = 'disabled';
                            } elseif ($status === 'finished') {
                                $badge = '<span class="badge bg-dark">Selesai</span>';
                                $btnText = 'Lihat Hasil';
                                $btnDisabled = '';
                            }
                        }
                    ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card shadow-sm h-100 exam-card" data-setting-id="<?= $settingId ?>">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h5 class="card-title"><?= esc($exam['name']) ?></h5>
                                    <span class="exam-badge"><?= $badge ?></span>
                                </div>
                                <p class="card-text"><?= esc($exam['description'] ?? '-') ?></p>
                                <?php if ($settingId): ?>
                                    <p class="small text-muted mb-1">
                                        Tanggal Mulai: <?= $exam['start_time'] ? date('d M Y H:i', strtotime($exam['start_time'])) : '-' ?>
                                    </p>
                                    <p class="small text-muted">
                                        Durasi: <?= $exam['duration'] ?? '-' ?> menit
                                    </p>
                                <?php endif; ?>
                            </div>
                            <div class="card-footer bg-transparent border-0 text-center">
                                <a href="<?= $settingId ? base_url('participant/exams/start/'.$settingId) : '#' ?>"
                                   class="btn btn-primary btn-sm exam-btn <?= $btnDisabled ?>"
                                ><?= $btnText ?></a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="alert alert-info text-center">Belum ada ujian untuk event ini.</div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.0/dist/jquery.min.js"></script>

<script>
$(document).ready(function() {
    console.log('aaa');

    // Fungsi update status tiap card via AJAX
    function updateExamStatus() {
        $('.exam-card').each(function() {
            const card = $(this);
            const settingId = card.data('setting-id');

            if (!settingId) return; // skip jika belum ada setting

            $.get(`<?= base_url('participant/exams/status') ?>/${settingId}`, function(res) {
                if (res.status === 'success') {
                    const badgeEl = card.find('.exam-badge');
                    const btnEl = card.find('.exam-btn');

                    let badgeHtml = '<span class="badge bg-secondary">Belum Mulai</span>';
                    let btnDisabled = true;
                    let btnText = 'Masuk Ujian';

                    if (res.exam_status === 'scheduled') {
                        badgeHtml = '<span class="badge bg-secondary">Belum Mulai</span>';
                        btnDisabled = true;
                    } else if (res.exam_status === 'started') {
                        badgeHtml = '<span class="badge bg-success">Berlangsung</span>';
                        btnDisabled = false;
                    } else if (res.exam_status === 'paused') {
                        badgeHtml = '<span class="badge bg-warning text-dark">Dijeda</span>';
                        btnDisabled = true;
                    } else if (res.exam_status === 'finished') {
                        badgeHtml = '<span class="badge bg-dark">Selesai</span>';
                        btnText = 'Lihat Hasil';
                        btnDisabled = false;
                    }

                    badgeEl.html(badgeHtml);
                    btnEl.text(btnText);

                    if (btnDisabled) {
                        btnEl.addClass('disabled').attr('href', '#');
                    } else {
                        btnEl.removeClass('disabled').attr('href', '<?= base_url("participant/exams/start/") ?>' + settingId);
                    }
                }
            }, 'json');
        });
    }

    // Polling tiap 5 detik untuk update status otomatis
    setInterval(updateExamStatus, 5000);

});
</script>
<?= $this->endSection() ?>
