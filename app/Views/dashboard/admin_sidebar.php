<div class="offcanvas offcanvas-start bg-dark text-white" tabindex="-1" id="sidebarMenu" aria-labelledby="sidebarMenuLabel">
    
    <div class="offcanvas-header" style="background-color: #1a1a2e;">
        <h5 class="offcanvas-title fw-bold" id="sidebarMenuLabel">Sistem CAT</h5>
        <button type="button" class="btn-close text-reset btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>

    <div class="offcanvas-body d-flex flex-column p-3" style="background-color: #1a1a2e; color: #E0E0F0;">
        <h5 class="text-secondary mt-3 border-bottom pb-2 fw-light" style="border-color: rgba(255,255,255,0.1) !important;">
            <?= $role ?> Panel
        </h5>
        <!-- NAVIGASI LENGKAP UNTUK MOBILE (Sekarang di dalam body Offcanvas) -->
        <ul class="nav nav-pills flex-column mb-auto" id="sidebarNavMobile">
            <li>
                <a href="<?= base_url('dashboard') ?>" class="nav-link text-white sidebar-link" data-nav-id="dashboard">
                    <i class="fas fa-home me-2"></i> Dashboard
                </a>
            </li>
            
            <?php if ($role == 'Super Admin'): ?>
            <li class="nav-item">
                <a href="<?= base_url('superadmin/events') ?>" class="nav-link text-white sidebar-link" data-nav-id="events">
                    <i class="fas fa-calendar-alt me-2"></i> Kelola Event Ujian
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= base_url('superadmin/users') ?>" class="nav-link text-white sidebar-link" data-nav-id="users">
                    <i class="fas fa-users-cog me-2"></i> Kelola Akun Panitia
                </a>
            </li>
            <?php endif; ?>

            <?php if ($role === 'Panitia' || $role === 'Super Admin'): ?>
            <li class="nav-header mt-3 border-top pt-2 text-secondary fw-light" style="border-color: rgba(255,255,255,0.1) !important;">
                EVENT: <?= $role === 'Panitia' ? session()->get('event_name') : 'GLOBAL' ?>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link text-white sidebar-link ajax-link" data-url="<?= base_url('admin/categories') ?>" data-nav-id="questions">
                    <i class="fas fa-list-alt me-2"></i> Kelola Kategori 
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link text-white sidebar-link ajax-link" data-url="<?= base_url('admin/ajax/questions/index') ?>" data-nav-id="questions">
                    <i class="fas fa-list-alt me-2"></i> Kelola Soal (Bank Soal)
                </a>
            </li>
            
            <li class="nav-item">
                <a href="<?= base_url('admin/peserta') ?>" class="nav-link text-white sidebar-link" data-nav-id="peserta">
                    <i class="fas fa-user-friends me-2"></i> Kelola Peserta
                </a>
            </li>
            <?php endif; ?>
        </ul>
        
        <div class="mt-auto border-top pt-2" style="border-color: rgba(255,255,255,0.1) !important;">
            <a href="<?= base_url('logout') ?>" class="btn btn-outline-light btn-sm w-100 fw-bold">Logout</a>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script> 
<script>
    // Fungsionalitas Active state tetap sama, tetapi hanya perlu menangani link Offcanvas
    $(document).ready(function() {
        const path = window.location.pathname;
        const links = $('#sidebarNavMobile a, #topNavDesktop a');
        const baseUrl = '<?= base_url() ?>';
        
        links.removeClass('active');
        
        links.each(function() {
            const linkHref = $(this).attr('href');
            const relativePath = linkHref.replace(baseUrl, ''); 

            if (path.includes(relativePath) && relativePath.length > 1) { 
                $(this).addClass('active');
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

<!-- STYLING AKHIR: HAPUS SEMUA MARGIN DESKTOP -->
<style>
/* 1. STYLING DEFAULT (MOBILE FIRST) */
.main-content-desktop, .header-desktop {
    margin-left: 0 !important;
    width: 100% !important;
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
    background-color: #8E6CFA !important; 
    color: white !important;
    font-weight: 600;
    box-shadow: 0 4px 10px rgba(142, 108, 250, 0.4);
}


/* 2. OVERRIDE UNTUK LAYAR BESAR (DESKTOP) */
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
