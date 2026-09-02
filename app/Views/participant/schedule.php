<?= $this->extend('layout/participant_layout') ?>
<?= $this->section('content') ?>

<div class="container py-5">
    <div class="mb-3">
        <a href="<?= base_url('participant/dashboard') ?>" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali ke Dashboard
        </a>
    </div>
    <h3 class="mb-4 text-center">Timeline Ujian Anda</h3>
    <div class="timeline position-relative">
        <?php foreach($schedules as $i => $exam): ?>
        <?php
            $status = $exam['status'] ?? 'scheduled';
            $class = 'bg-secondary';
            $statusText = 'Belum dijadwalkan';
            $dateText = '-';
            $durationText = '-';

            if($exam['name'] ?? false) {
                $statusText = match($status) {
                    'scheduled' => 'Belum Mulai',
                    'started' => 'Berlangsung',
                    'paused' => 'Dijeda',
                    'finished' => 'Selesai',
                    default => 'Belum dijadwalkan'
                };
                $class = match($status) {
                    'scheduled' => 'bg-secondary',
                    'started' => 'bg-primary',
                    'paused' => 'bg-warning text-dark',
                    'finished' => 'bg-success',
                    default => 'bg-secondary'
                };
                $dateText = $exam['start_time'] ?? '-';
                $durationText = isset($exam['duration']) ? $exam['duration'].' menit' : '-';
            }
        ?>
        <div class="timeline-step d-flex position-relative">
            <!-- Bulatan -->
            <div class="step-marker <?= $class ?>"><?= $i+1 ?></div>
            <!-- Garis vertikal -->
            <?php if($i < count($schedules)-1): ?>
            <div class="step-line"></div>
            <?php endif; ?>
            <!-- Keterangan -->
            <div class="step-content ms-3">
                <h6 class="mb-1"><?= esc($exam['name'] ?? 'Belum dijadwalkan') ?></h6>
                <small class="text-muted">Tanggal: <?= esc($dateText) ?></small><br>
                <small class="text-muted">Durasi: <?= esc($durationText) ?></small><br>
                <small class="text-muted">Status: <?= esc($statusText) ?></small>
                <?php if($status === 'started' && isset($exam['remaining_seconds'])): ?>
                <div class="text-danger small mt-1" id="countdown-<?= $exam['id'] ?>">Loading...</div>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 50px;
}

.timeline-step {
    display: flex;
    align-items: flex-start;
    position: relative;
    margin-bottom: 40px;
}

.step-marker {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    color: white;
    text-align: center;
    line-height: 30px;
    font-weight: bold;
    flex-shrink: 0;
}

.step-line {
    position: absolute;
    top: 30px;
    left: 15px;
    width: 4px;
    height: calc(100% - 30px);
    background: #ddd;
    z-index: -1;
}

.step-content {
    margin-left: 20px;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    <?php foreach($schedules as $exam): ?>
    <?php if(($exam['status'] ?? '') === 'started' && isset($exam['remaining_seconds'])): ?>
    // Countdown timer
    let remaining_<?= $exam['id'] ?> = <?= $exam['remaining_seconds'] ?>;
    let el_<?= $exam['id'] ?> = document.getElementById('countdown-<?= $exam['id'] ?>');
    setInterval(() => {
        if(remaining_<?= $exam['id'] ?> > 0){
            let mins = Math.floor(remaining_<?= $exam['id'] ?> / 60);
            let secs = remaining_<?= $exam['id'] ?> % 60;
            el_<?= $exam['id'] ?>.textContent = `Sisa waktu: ${mins}m ${secs}s`;
            remaining_<?= $exam['id'] ?>--;
        } else {
            el_<?= $exam['id'] ?>.textContent = 'Selesai';
        }
    }, 1000);
    <?php endif; ?>
    <?php endforeach; ?>
});
</script>

<?= $this->endSection() ?>
