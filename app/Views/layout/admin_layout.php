<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Sistem CAT' ?></title>
    <!-- Memuat Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Memuat Font Awesome untuk Ikon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <!-- FONT INTER (Modern Look) -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    
    <!-- STYLING FINAL -->
    <style>
        /* FONT MODERN */
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f7f7f9;
        }
        
        /* GAYA MODERN SIDEBAR */
        .offcanvas-body .sidebar-link {
            padding: 10px 15px;
            border-radius: 12px; 
            transition: background-color 0.2s;
            margin-bottom: 5px;
            color: #E0E0F0 !important;
        }

        .offcanvas-body .sidebar-link:hover {
            background-color: #2c2c45;
        }

        .offcanvas-body .sidebar-link.active {
            background-color: #8E6CFA !important; /* Warna ungu cerah */
            color: white !important;
            font-weight: 600;
            box-shadow: 0 4px 10px rgba(142, 108, 250, 0.4);
        }
        
        /* Styling Responsif */
        .main-content-desktop, .header-desktop {
            margin-left: 0 !important;
            width: 100% !important;
        }
        
        @media (min-width: 992px) { 
            /* DI DESKTOP: OFF CANVAS HILANG */
            .offcanvas-start {
                visibility: hidden !important; 
                transform: translate3d(-100%, 0, 0) !important;
            }
            /* KONTEN UTAMA: TIDAK ADA MARGIN, LEBAR 100% */
            .main-content-desktop, .header-desktop {
                margin-left: 0 !important; 
                width: 100% !important;
            }
        }
    </style>
</head>
<body>

    <?php
    $role = $role ?? session()->get('role_name');
    ?>

    <!-- SIDEBAR (OFFCANVAS UNTUK MOBILE) -->
    <?= view('dashboard/admin_sidebar', ['role' => $role]) ?>


    <!-- HEADER UTAMA (TOP NAV UNTUK DESKTOP) -->
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

        <!-- Navigasi Desktop -->
        <!-- MENGHILANGKAN FW-BOLD DARI UL -->
        <div class="collapse navbar-collapse d-none d-lg-flex me-auto" id="topNavDesktop">
            <ul class="navbar-nav mx-auto mb-2 mb-lg-0 fw-medium"> 
                
                <!-- 1. DASHBOARD -->
                <li class="nav-item">
                    <a href="<?= base_url('dashboard') ?>" class="nav-link text-dark fw-medium" data-nav-id="dashboard">Dashboard</a>
                </li>

                <!-- 2. MANAJEMEN (DROPDOWN) -->
                <?php if ($role == 'Super Admin' || $role == 'Panitia'): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-dark fw-medium" href="#" id="navbarDropdownManagement" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Manajemen
                        </a>
                        <ul class="dropdown-menu shadow-sm" aria-labelledby="navbarDropdownManagement">
                            <?php if ($role == 'Super Admin'): ?>
                                <li><a class="dropdown-item fw-normal" href="<?= base_url('superadmin/events') ?>" data-nav-id="events">Kelola Event</a></li>
                                <li><a class="dropdown-item fw-normal" href="<?= base_url('superadmin/users') ?>" data-nav-id="users">Kelola Akun</a></li>
                                <li><hr class="dropdown-divider"></li>
                            <?php endif; ?>
                            
                            <?php if ($role == 'Panitia' || $role == 'Super Admin'): ?>
                                <li><a class="dropdown-item fw-normal" href="<?= base_url('admin/peserta') ?>" data-nav-id="peserta">Kelola Peserta</a></li>
                            <?php endif; ?>
                        </ul>
                    </li>
                <?php endif; ?>

                <!-- 3. DATA UJIAN (DROPDOWN) -->
                <?php if ($role === 'Panitia' || $role === 'Super Admin'): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-dark fw-medium" href="#" id="navbarDropdownExamData" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Data Ujian
                        </a>
                        <ul class="dropdown-menu shadow-sm" aria-labelledby="navbarDropdownExamData">
                            <li>
                                <a href="<?= base_url('admin/categories') ?>" class="dropdown-item fw-normal" data-nav-id="questions">Kelola Kategori</a>
                            </li>
                            <li>
                                <a href="<?= base_url('admin/questions') ?>" class="dropdown-item fw-normal" data-nav-id="questions">Bank Soal</a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item fw-normal" href="<?= base_url('admin/exams') ?>" data-nav-id="settings">Pengaturan Ujian</a></li>
                        </ul>
                    </li>
                <?php endif; ?>

                <!-- 4. LAPORAN & KOREKSI (DROPDOWN) -->
                <?php if ($role === 'Panitia' || $role === 'Korektor' || $role === 'Super Admin'): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-dark fw-medium" href="#" id="navbarDropdownReports" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Laporan & Koreksi
                        </a>
                        <ul class="dropdown-menu shadow-sm" aria-labelledby="navbarDropdownReports">
                            <li><a class="dropdown-item fw-normal" href="#">Rekap Nilai Tertulis</a></li>
                            <li><a class="dropdown-item fw-normal" href="#">Input Nilai Kemampuan</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item fw-normal" href="#">Analisis & Grafik</a></li>
                        </ul>
                    </li>
                <?php endif; ?>
                
            </ul>
        </div>
        
        <!-- User Info dan Logout di Kanan -->
        <div class="d-none d-lg-flex align-items-center">
            <span class="navbar-text me-3 text-dark fw-medium">
                Halo, <?= session()->get('username') ?>
            </span>
            <a href="<?= base_url('logout') ?>" class="btn btn-sm btn-outline-danger fw-medium">Logout</a>
        </div>

    </div>
</header>

<!-- KONTEN UTAMA DARI CONTROLLER AKAN DIMUAT DI SINI -->
<main id="main-content" class="px-4 py-4 main-content-desktop"> 
    <?= $this->renderSection('content') ?>
</main>

<!-- MEMUAT JQUERY DAN BOOTSTRAP JS -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script> 
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?= $this->renderSection('scripts') ?>
<script>
    // SCRIPT GLOBAL UNTUK MENGATUR STATUS ACTIVE PADA NAVIGASI
    $(document).ready(function() {
        const path = window.location.pathname;
        const links = $('#topNavDesktop a, #sidebarNavMobile a');
        const baseUrl = '<?= base_url() ?>';
        
        links.removeClass('active');
        
        links.each(function() {
            const linkHref = $(this).attr('href');
            const relativePath = linkHref.replace(baseUrl, ''); 

            if (path.includes(relativePath) && relativePath.length > 1) { 
                $(this).addClass('active');
                
                if($(this).hasClass('dropdown-item')) {
                    $(this).closest('.dropdown').find('.dropdown-toggle').addClass('active');
                }
            }
        });

        if (path === baseUrl || path === '/' || path.includes('/dashboard')) {
           $('[data-nav-id="dashboard"]').addClass('active');
       }
       
       $('.ajax-link').on('click', function() {
           links.removeClass('active');
           $(this).addClass('active');
       });
   });
</script>

</body>

</html>
