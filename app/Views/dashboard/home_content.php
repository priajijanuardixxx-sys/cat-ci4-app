<?= $this->extend('layout/admin_layout') ?>
<?= $this->section('content') ?>

<!-- Hero Section -->
<section class="hero position-relative overflow-hidden" style="min-height: 80vh;">
    <div class="hero-blur position-absolute top-0 start-0 w-100 h-100"></div>
    <div class="container h-100 d-flex flex-column justify-content-center position-relative z-2">
        <h1 class="fw-bold display-5 text-dark mb-3 animate__animated animate__fadeInDown">
            Selamat Datang, <?= session()->get('full_name') ?>!
        </h1>
        <p class="lead text-secondary animate__animated animate__fadeInUp">
            Anda masuk sebagai <strong><?= $role ?></strong> (<?= session()->get('username') ?>)
        </p>

        <?php if ($role !== 'Peserta'): ?>
        <div class="row mt-5 g-4">
            <div class="col-lg-4 col-md-6">
                <div class="card shadow-lg border-0 rounded-3 animate__animated animate__fadeInLeft" style="background: linear-gradient(135deg,#9b5de5,#f15bb5); color:white;">
                    <div class="card-body">
                        <h6>Total Peserta</h6>
                        <h3>12</h3>
                    </div>
                    <div class="card-footer bg-transparent border-0">
                        <a href="<?= base_url('admin/peserta') ?>" class="text-white text-decoration-none">Lihat Detail →</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="card shadow-lg border-0 rounded-3 animate__animated animate__fadeInUp" style="background: linear-gradient(135deg,#f9c74f,#f8961e); color:white;">
                    <div class="card-body">
                        <h6>Soal Tersedia</h6>
                        <h3>150</h3>
                    </div>
                    <div class="card-footer bg-transparent border-0">
                        <a href="<?= base_url('admin/questions') ?>" class="text-white text-decoration-none">Kelola Bank Soal →</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="card shadow-lg border-0 rounded-3 animate__animated animate__fadeInRight" style="background: linear-gradient(135deg,#00b4d8,#48cae4); color:white;">
                    <div class="card-body">
                        <h6>Event Aktif</h6>
                        <h3><?= session()->get('event_name') ?></h3>
                    </div>
                    <div class="card-footer bg-transparent border-0">
                        <a href="<?= base_url('superadmin/events') ?>" class="text-white text-decoration-none">Pengaturan Event →</a>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
    <canvas id="heroParticles" class="position-absolute top-0 start-0 w-100 h-100"></canvas>
</section>

<!-- Informasi Section -->
<section class="info-section py-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card shadow-lg border-0 rounded-3 flip-card" data-aos="flip-left">
                    <div class="card-body text-center">
                        <i class="fas fa-cogs fa-2x mb-2 text-primary"></i>
                        <h5 class="card-title">Manajemen Event</h5>
                        <p class="card-text">Kontrol penuh event, panitia, dan peserta.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-lg border-0 rounded-3 flip-card" data-aos="flip-up">
                    <div class="card-body text-center">
                        <i class="fas fa-file-alt fa-2x mb-2 text-success"></i>
                        <h5 class="card-title">Bank Soal</h5>
                        <p class="card-text">Atur kategori, komposisi, dan soal ujian.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-lg border-0 rounded-3 flip-card" data-aos="flip-right">
                    <div class="card-body text-center">
                        <i class="fas fa-chart-line fa-2x mb-2 text-warning"></i>
                        <h5 class="card-title">Laporan & Koreksi</h5>
                        <p class="card-text">Pantau hasil ujian peserta secara real-time.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Summary Section -->
<section class="summary-section py-5 bg-light">
    <div class="container">
        <h3 class="mb-4 text-center animate__animated animate__fadeInUp">Ringkasan Hasil Ujian</h3>
        <div class="row g-4">
            <!-- Chart 1: Total Peserta Lulus/Gagal -->
            <div class="col-md-6">
                <div class="card shadow-lg border-0 rounded-3 p-3" data-aos="fade-right">
                    <h5 class="card-title text-center">Peserta Lulus/Gagal</h5>
                    <canvas id="chartPassFail" height="200"></canvas>
                </div>
            </div>
            <!-- Chart 2: Nilai Rata-rata Tiap Jenis Ujian -->
            <div class="col-md-6">
                <div class="card shadow-lg border-0 rounded-3 p-3" data-aos="fade-left">
                    <h5 class="card-title text-center">Rata-rata Nilai Tiap Jenis Ujian</h5>
                    <canvas id="chartAvgScore" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>
</section>


<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
.hero {
    background: linear-gradient(135deg, #e0bbff 0%, #ffffff 100%);
    position: relative;
    overflow: hidden;
}
.hero-blur {
    position: absolute;
    top: 0;
    left: 0;
    width: 50%;
    height: 100%;
    background: linear-gradient(to right, rgba(255,255,255,0.7), rgba(255,255,255,0));
    backdrop-filter: blur(12px);
    z-index: 1;
    pointer-events: none;
}
.hero .container {
    position: relative;
    z-index: 2;
}
.flip-card {
    transition: transform 0.6s;
    transform-style: preserve-3d;
}
</style>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- Animate.css -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
<!-- AOS (Animate on Scroll) -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>

<script src="https://cdn.jsdelivr.net/npm/tsparticles@2.12.1/tsparticles.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // --- tsParticles Hero ---
    if (window.tsParticles) {
        tsParticles.load("heroParticles", {
            fpsLimit: 60,
            particles: {
                number: { value: 30 },
                color: { value: ["#9b5de5","#f15bb5","#f8961e","#48cae4"] },
                shape: { type: "circle" },
                opacity: { value: 0.5 },
                size: { value: { min: 2, max: 6 } },
                move: { enable: true, speed: 1, direction: "none", outModes: "bounce" }
            },
            detectRetina: true
        });
    }

    // --- AOS Animate on Scroll ---
    if (window.AOS) {
        AOS.init({
            duration: 1000,
            once: true,
            easing: 'ease-in-out'
        });
    }

    // --- Chart.js: Peserta Lulus/Gagal ---
    const ctxPassFail = document.getElementById('chartPassFail');
    if (ctxPassFail) {
        new Chart(ctxPassFail.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: ['Lulus', 'Gagal'],
                datasets: [{
                    data: [80, 20],
                    backgroundColor: ['#00b4d8', '#f8961e'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'bottom' },
                    tooltip: { enabled: true }
                },
                animation: {
                    animateScale: true,
                    animateRotate: true
                }
            }
        });
    }

    // --- Chart.js: Rata-rata Nilai ---
    const ctxAvgScore = document.getElementById('chartAvgScore');
    if (ctxAvgScore) {
        new Chart(ctxAvgScore.getContext('2d'), {
            type: 'bar',
            data: {
                labels: ['Tulis', 'Office', 'Praktek'],
                datasets: [{
                    label: 'Rata-rata Nilai',
                    data: [75, 85, 70],
                    backgroundColor: ['#9b5de5','#f15bb5','#48cae4']
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: { beginAtZero: true, max: 100 }
                },
                plugins: {
                    legend: { display: false }
                },
                animation: { duration: 1200, easing: 'easeOutQuart' }
            }
        });
    }

    // Refresh AOS setelah chart muncul
    if (window.AOS) { AOS.refresh(); }
});
</script>

<?= $this->endSection() ?>