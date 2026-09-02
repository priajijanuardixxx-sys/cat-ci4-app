<?= $this->extend('layout/admin_layout') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><?= $title ?></h1>
    <a href="<?= base_url('admin/questions') ?>" class="btn btn-secondary fw-medium">
        <i class="fas fa-arrow-left"></i> Kembali ke Bank Soal
    </a>
</div>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
<?php endif; ?>

<div class="card shadow p-4">
    <h5 class="fw-bold mb-3 text-primary">Informasi Soal</h5>
    <form id="formQuestion" action="<?= base_url('admin/questions/save') ?>" method="post">
        <?= csrf_field() ?>
        
        <!-- Field Kunci: Diisi otomatis oleh Controller -->
        <input type="hidden" name="id" value="<?= $question['id'] ?? '' ?>"> 

        <div class="row">
            <div class="col-md-12 mb-3">
                <label for="category_id" class="form-label fw-medium">Grup Soal (Kategori)</label>
                <select class="form-select" id="category_id" name="category_id" required>
                    <option value="">-- Pilih Kategori --</option>
                    <?php if (!empty($categories)): ?>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= old('category_id', $question['category_id'] ?? '') == $cat['id'] ? 'selected' : '' ?>>
                                <?= $cat['name'] ?> (<?= $cat['required_count'] ?> Soal Wajib)
                            </option>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <option disabled>Mohon buat Kategori Soal terlebih dahulu.</option>
                    <?php endif; ?>
                </select>
            </div>
        </div>

        <div class="mb-4">
            <label for="question_text" class="form-label fw-medium">Teks Soal</label>
            <textarea class="form-control" id="question_text" name="question_text" rows="4" required><?= old('question_text', $question['question_text'] ?? '') ?></textarea>
        </div>

        <h5 class="fw-bold mb-3 text-secondary">Pilihan Jawaban (Options)</h5>

        <div class="row">
            <?php 
            $options = ['a', 'b', 'c', 'd', 'e']; 
            $i = 0;
            foreach ($options as $opt): 
                $i++;
            ?>
            <div class="col-md-6 mb-3">
                <label for="option_<?= $opt ?>" class="form-label">Pilihan <?= strtoupper($opt) ?></label>
                <input type="text" class="form-control" id="option_<?= $opt ?>" name="option_<?= $opt ?>" 
                       value="<?= old('option_'.$opt, $question['option_'.$opt] ?? '') ?>" 
                       <?= $i < 4 ? 'required' : '' /* A, B, C, D wajib */ ?>>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="row mt-3">
            <div class="col-md-6 mb-3">
                <label for="correct_answer" class="form-label fw-medium text-success">Kunci Jawaban Benar (A/B/C/D/E)</label>
                <input type="text" class="form-control text-uppercase" id="correct_answer" name="correct_answer" maxlength="1" 
                       value="<?= old('correct_answer', $question['correct_answer'] ?? '') ?>" required style="width: 80px;">
            </div>
        </div>

        <div class="d-flex justify-content-end mt-4">
            <button type="submit" class="btn btn-success fw-medium">Simpan Soal</button>
        </div>

    </form>
</div>

<?= $this->endSection() ?>
