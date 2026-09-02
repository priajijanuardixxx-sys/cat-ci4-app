<?= view('layout/header', ['title' => $title]) ?>
<?= view('dashboard/admin_sidebar', ['role' => session()->get('role_name'), 'event_name' => 'Belum Ditunjuk']) ?> 

<div id="main-content" class="content-area flex-grow-1">
    <div class="alert alert-danger mt-5">
        <h4 class="alert-heading">Akses Dibatasi!</h4>
        <p>Akun Anda teridentifikasi sebagai **Panitia**, namun belum terikat pada Event Ujian mana pun.</p>
        <p>Anda hanya dapat mengakses fitur administrasi setelah **Super Admin** menunjuk akun Anda sebagai Panitia Utama sebuah Event.</p>
    </div>
    <a href="<?= base_url('logout') ?>" class="btn btn-warning">Logout</a>
</div>

<?= view('layout/footer') ?>
