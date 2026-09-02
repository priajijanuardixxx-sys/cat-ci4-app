<?= $this->extend('layout/participant_layout') ?>
<?= $this->section('content') ?>

<!-- Hero Section -->
<section class="hero position-relative overflow-hidden" style="min-height: 80vh;">
    <div class="hero-blur position-absolute top-0 start-0 w-100 h-100"></div>
    <div class="container h-100 d-flex flex-column justify-content-center position-relative z-2">
        <h1 class="fw-bold display-5 text-dark mb-3 animate__animated animate__fadeInDown">
            Selamat Datang, <?= esc($full_name) ?> 👋
        </h1>
        <p class="lead text-secondary animate__animated animate__fadeInUp">
            Anda terdaftar di event <strong><?= esc($event_name) ?></strong>
        </p>

        <div class="row mt-5 g-4">
            <div class="col-lg-4 col-md-6">
                <div class="card shadow-lg border-0 rounded-3 animate__animated animate__fadeInLeft" style="background: linear-gradient(135deg,#9b5de5,#f15bb5); color:white;">
                    <div class="card-body">
                        <h6>Jadwal Ujian</h6>
                        <h3><i class="bi bi-calendar"></i></h3>
                    </div>
                    <div class="card-footer bg-transparent border-0">
                         <a href="<?= base_url('participant/schedule') ?>" class="text-white text-decoration-none">Lihat Jadwal →</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="card shadow-lg border-0 rounded-3 animate__animated animate__fadeInUp" style="background: linear-gradient(135deg,#f9c74f,#f8961e); color:white;">
                     <div class="card-body">
                        <h6>Ujian Aktif</h6>
                        <h3><?= esc($event_name) ?></h3>
                    </div>
                    <div class="card-footer bg-transparent border-0">
                        <a href="<?= base_url('participant/exams') ?>" class="text-white text-decoration-none">Masuk Ujian →</a>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <canvas id="heroParticles" class="position-absolute top-0 start-0 w-100 h-100"></canvas>
</section>



<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
<link href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" rel="stylesheet">

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
<script src="https://cdn.jsdelivr.net/npm/tsparticles@2.12.1/tsparticles.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
    // Background Particles
    tsParticles.load("heroParticles", {
        fpsLimit: 60,
        particles: {
            number: { value: 40 },
            color: { value: ["#9b5de5","#f15bb5","#48cae4","#f9c74f"] },
            opacity: { value: 0.4 },
            size: { value: { min: 2, max: 6 } },
            move: { enable: true, speed: 1, outModes: "bounce" }
        },
        detectRetina: true
    });

    // AOS Animations
    AOS.init({
        duration: 1000,
        once: true,
        easing: 'ease-in-out'
    });

    // Chart: Distribusi Nilai
    const ctxScores = document.getElementById('chartScores').getContext('2d');
    new Chart(ctxScores, {
        type: 'bar',
        data: {
            labels: ['Ujian 1', 'Ujian 2', 'Ujian 3'],
            datasets: [{
                label: 'Nilai',
                data: [78, 85, 92],
                backgroundColor: ['#9b5de5','#f15bb5','#48cae4']
            }]
        },
        options: {
            responsive: true,
            scales: { y: { beginAtZero: true, max: 100 } },
            plugins: { legend: { display: false } }
        }
    });

    // Chart: Kemajuan Ujian
    const ctxProgress = document.getElementById('chartProgress').getContext('2d');
    new Chart(ctxProgress, {
        type: 'doughnut',
        data: {
            labels: ['Selesai', 'Belum'],
            datasets: [{
                data: [3, 2],
                backgroundColor: ['#00b4d8', '#f8961e']
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { position: 'bottom' } }
        }
    });
});
</script>
<?= $this->endSection() ?>
