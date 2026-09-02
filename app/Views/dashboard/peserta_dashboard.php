<?= view('layout/header', ['title' => $title]) ?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-lg mt-5 p-4">
            <h1 class="card-title text-primary">Selamat Datang, <?= session()->get('full_name') ?>!</h1>
            <p class="lead">Siap untuk memulai Ujian Penyaringan Perangkat Desa Binangun?</p>
            <hr>
            
            <div class="alert alert-warning" role="alert">
                <h4 class="alert-heading">Ujian Tertulis CAT</h4>
                <p>Dilaksanakan pada: **Minggu, 09 November 2025**</p>
                [cite_start]<p>Alokasi Waktu: **120 menit** (08.00 s.d 10.00 WIB) </p>
                <p class="mb-0">Pastikan perangkat Anda stabil sebelum memulai!</p>
            </div>
            
            <div class="d-grid gap-2">
                <a href="<?= base_url('exam/start') ?>" class="btn btn-success btn-lg">
                    Mulai Ujian Sekarang
                </a>
            </div>
            
            <p class="mt-3 text-muted text-center">
                *Hanya dapat diakses pada waktu yang ditentukan.
            </p>
        </div>
    </div>
</div>

<?= view('layout/footer') ?>