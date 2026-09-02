
<?= view('layout/header', ['title' => $title]) ?>

<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card shadow-lg border-0 rounded-lg mt-5">
            <div class="card-header text-center bg-primary text-white">
                <h3 class="fw-light my-4">Login Aplikasi CAT</h3>
            </div>
            <div class="card-body">
                
                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger" role="alert">
                        <?= session()->getFlashdata('error') ?>
                    </div>
                <?php endif; ?>

                <?= form_open('auth/attemptLogin') ?>
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input class="form-control" id="username" name="username" type="text" 
                               placeholder="Masukkan Username" value="<?= old('username') ?>" required />
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input class="form-control" id="password" name="password" type="password" 
                               placeholder="Masukkan Password" required />
                    </div>
                    <div class="d-grid mt-4 mb-0">
                        <button class="btn btn-primary btn-block" type="submit">Login</button>
                    </div>
                <?= form_close() ?>
            </div>
        </div>
    </div>
</div>

<?= view('layout/footer') ?>