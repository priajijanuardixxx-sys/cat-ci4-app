<?= view('layout/header', ['title' => $title]) ?>
<?= view('dashboard/admin_sidebar', ['role' => $role]) ?> 

<!-- HEADER BARU: Navigasi di atas untuk Desktop -->
<header class="navbar navbar-expand-lg navbar-light bg-light sticky-top header-desktop" style="border-bottom: 1px solid #ddd; background-color: #FFFFFF !important;">
    <div class="container-fluid">
        <!-- Brand Name di Kiri -->
        <a class="navbar-brand me-auto fw-bold text-dark" href="<?= base_url('dashboard') ?>">
             Sistem CAT
        </a>
        
        <!-- Tombol Toggler (Mobile Only) -->
        <button class="navbar-toggler d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navigasi Desktop (Hanya terlihat di Desktop) -->
        <div class="collapse navbar-collapse d-none d-lg-flex me-auto" id="topNavDesktop">
            <ul class="navbar-nav mx-auto mb-2 mb-lg-0 fw-bold">
                
                <!-- 1. DASHBOARD -->
                <li class="nav-item">
                    <a href="<?= base_url('dashboard') ?>" class="nav-link text-dark" data-nav-id="dashboard">Dashboard</a>
                </li>

                <!-- 2. MANAJEMEN (DROPDOWN) -->
                <?php if ($role == 'Super Admin' || $role == 'Panitia'): ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-dark" href="#" id="navbarDropdownManagement" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Manajemen
                    </a>
                    <ul class="dropdown-menu shadow-sm" aria-labelledby="navbarDropdownManagement">
                        <?php if ($role == 'Super Admin'): ?>
                        <li><a class="dropdown-item" href="<?= base_url('superadmin/events') ?>" data-nav-id="events">Kelola Event</a></li>
                        <li><a class="dropdown-item" href="<?= base_url('superadmin/users') ?>" data-nav-id="users">Kelola Akun</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <?php endif; ?>
                        
                        <?php if ($role == 'Panitia' || $role == 'Super Admin'): ?>
                        <li><a class="dropdown-item" href="<?= base_url('admin/peserta') ?>" data-nav-id="peserta">Kelola Peserta</a></li>
                        <?php endif; ?>
                    </ul>
                </li>
                <?php endif; ?>

                <!-- 3. DATA UJIAN (DROPDOWN) -->
                <?php if ($role === 'Panitia' || $role === 'Super Admin'): ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-dark" href="#" id="navbarDropdownExamData" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Data Ujian
                    </a>
                    <ul class="dropdown-menu shadow-sm" aria-labelledby="navbarDropdownExamData">
                        <li>
                            <a href="#" class="dropdown-item ajax-link" data-url="<?= base_url('admin/ajax/questions/index') ?>" data-nav-id="questions">Bank Soal</a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="#" data-nav-id="settings">Pengaturan Ujian</a></li>
                    </ul>
                </li>
                <?php endif; ?>

                <!-- 4. LAPORAN & KOREKSI (DROPDOWN) -->
                <?php if ($role === 'Panitia' || $role === 'Korektor' || $role === 'Super Admin'): ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-dark" href="#" id="navbarDropdownReports" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Laporan & Koreksi
                    </a>
                    <ul class="dropdown-menu shadow-sm" aria-labelledby="navbarDropdownReports">
                        <li><a class="dropdown-item" href="#">Rekap Nilai Tertulis</a></li>
                        <li><a class="dropdown-item" href="#">Input Nilai Kemampuan</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="#">Analisis & Grafik</a></li>
                    </ul>
                </li>
                <?php endif; ?>
                
            </ul>
        </div>
        
        <!-- User Info dan Logout di Kanan -->
        <div class="d-none d-lg-flex align-items-center">
            <span class="navbar-text me-3 text-dark">
                Halo, <?= session()->get('username') ?>
            </span>
            <a href="<?= base_url('logout') ?>" class="btn btn-sm btn-outline-danger fw-bold">Logout</a>
        </div>

    </div>
</header>

<!-- Kontainer Konten Dinamis -->
<div id="main-content" class="px-4 py-4 main-content-desktop"> 
    <!-- Konten awal (Welcome Cards) dimuat di sini -->
    <?= view('dashboard/home_content', ['role' => $role]) ?>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script> 
<?= view('layout/footer') ?> 
