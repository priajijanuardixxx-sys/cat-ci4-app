<?= $this->extend('layout/participant_layout') ?>
<?= $this->section('content') ?>

<section class="exam py-5">
    <div class="container">
        <h2 class="mb-4"><?= esc($title) ?></h2>

        <div class="alert alert-info" id="timer">⏳ Waktu tersisa: <span id="remaining-time"></span></div>

        <div class="row">
            <div class="col-md-9">
                <div id="question-container">
                    <?php foreach ($questions as $index => $q): ?>
                        
                        <?php if (isset($q['is_title']) && $q['is_title']): // TAMPILKAN HALAMAN JUDUL KATEGORI ?>
                            <div class="card mb-3 question-card question-title-card"
                                 data-index="<?= $index ?>"
                                 data-id="<?= $q['id'] ?>"
                                 style="<?= $index != $current_index ? 'display:none;' : '' ?>">
                                <div class="card-body text-center py-5">
                                    <h1 class="card-title text-primary fw-bold">Selamat Datang di Sesi</h1>
                                    <h2 class="card-text mb-4"><?= esc($q['title']) ?></h2>
                                    <p class="text-muted">Klik "Berikutnya ➡️" untuk memulai soal pertama.</p>
                                </div>
                            </div>
                        <?php else: // TAMPILKAN SOAL BIASA ?>
                            <div class="card mb-3 question-card"
                                 data-index="<?= $index ?>"
                                 data-id="<?= $q['id'] ?>"
                                 style="<?= $index != $current_index ? 'display:none;' : '' ?>">
                                <div class="card-body" id="question-body-<?= $index ?>"> 
                                    <h5 class="card-title">Soal <?= $index + 1 ?></h5> 
                                    <p><?= esc($q['question_text']) ?></p>

                                    <?php foreach (['A','B','C','D','E'] as $opt): ?>
                                        <?php if(!empty($q['option_'.strtolower($opt)])): ?>
                                            <div class="form-check">
                                                <input class="form-check-input answer-radio" type="radio" 
                                                    name="answer_<?= $q['id'] ?>" 
                                                    value="<?= $opt ?>"
                                                    <?= isset($session['answers'][$q['id']]) && $session['answers'][$q['id']] == $opt ? 'checked' : '' ?>>
                                                <label class="form-check-label"> <?= esc($q['option_'.strtolower($opt)]) ?></label>
                                            </div>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                    <?php endforeach; ?>

                    <div class="d-flex justify-content-between mt-3">
                        <button class="btn btn-secondary" id="prev-btn" <?= $current_index == 0 ? 'disabled' : '' ?>>⬅️ Sebelumnya</button>
                        <button class="btn btn-secondary" id="next-btn" <?= $current_index == count($questions)-1 ? 'disabled' : '' ?>>Berikutnya ➡️</button>
                        <button class="btn btn-success" id="finish-btn">✅ Selesai</button>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card sticky-top" style="top: 80px;">
                    <div class="card-header text-center fw-bold">📋 Nomor Soal</div>
                    <div class="card-body d-flex flex-wrap justify-content-center" id="question-nav">
                        <?php $soalCounter = 1; ?>
                        <?php foreach ($questions as $index => $q): ?>
                            
                            <?php if (isset($q['is_title']) && $q['is_title']): // JANGAN TAMPILKAN JUDUL DI PANEL NAV ?>
                                <?php continue; ?>
                            <?php endif; ?>
                            
                            <?php
                                $answered = isset($session['answers'][$q['id']]) && $session['answers'][$q['id']] !== '';
                                $color = $answered ? 'btn-success' : 'btn-danger';
                            ?>
                            <button class="btn <?= $color ?> m-1 question-nav-btn"
                                    data-index="<?= $index ?>"
                                    data-id="<?= $q['id'] ?>"
                                    style="width:45px;">
                                <?= $soalCounter++ ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    let currentIndex = <?= $current_index ?>;
    const totalQuestions = <?= count($questions) ?>;
    const sessionId = <?= $session['id'] ?>;
    const settingId = <?= $setting['id'] ?>;
    let remainingSeconds = <?= $remaining_seconds ?>;

    // Fungsi helper untuk mengacak urutan elemen anak (jawaban)
    function shuffleChildren(containerId) {
        const parent = document.getElementById(containerId);
        if (!parent) return;

        const children = Array.from(parent.children);

        // Filter hanya elemen div.form-check (pilihan jawaban)
        const answerOptions = children.filter(el => el.classList.contains('form-check'));
        
        // Ambil elemen yang BUKAN form-check (judul soal, teks soal, dll.)
        const staticElements = children.filter(el => !el.classList.contains('form-check'));
        
        // Algoritma Fisher-Yates shuffle
        for (let i = answerOptions.length - 1; i > 0; i--) {
            const j = Math.floor(Math.random() * (i + 1));
            [answerOptions[i], answerOptions[j]] = [answerOptions[j], answerOptions[i]];
        }

        // Hapus semua elemen lama
        parent.innerHTML = '';

        // Tambahkan kembali elemen statis dan jawaban yang sudah diacak
        staticElements.forEach(child => parent.appendChild(child));
        answerOptions.forEach(child => parent.appendChild(child));

        // Setelah di-shuffle, pastikan event change dipasang lagi pada radio
        $('.answer-radio').change(saveAnswer); 
    }

    // tampilkan soal
    function showQuestion(index) {
        $('.question-card').hide();
        const currentCard = $('.question-card[data-index="' + index + '"]');
        currentCard.fadeIn(200);

        $('#prev-btn').prop('disabled', index === 0);
        $('#next-btn').prop('disabled', index === totalQuestions - 1);

        // ACak Jawaban HANYA JIKA BUKAN HALAMAN JUDUL
        if (!currentCard.hasClass('question-title-card')) {
            const containerId = 'question-body-' + index;
            shuffleChildren(containerId);
        }
    }

    // highlight di daftar nomor soal
    function updateNavColor(questionId, answered) {
        const btn = $('.question-nav-btn[data-id="' + questionId + '"]');
        btn.removeClass('btn-success btn-danger');
        btn.addClass(answered ? 'btn-success' : 'btn-danger');
    }

    // highlight border soal
    function highlightAnswered() {
        $('.question-card').each(function() {
            const checked = $(this).find('.answer-radio:checked').length > 0;
            // Hanya highlight soal, bukan kartu judul
            if (!$(this).hasClass('question-title-card')) {
                if (checked) $(this).addClass('border-success shadow-sm');
                else $(this).removeClass('border-success shadow-sm');
            }
        });
    }

    // simpan jawaban via AJAX
    function saveAnswer() {
        const card = $('.question-card[data-index="' + currentIndex + '"]');
        
        // HENTIKAN jika halaman yang sedang dilihat adalah halaman JUDUL
        if (card.hasClass('question-title-card')) {
             return; 
        }

        const questionId = card.data('id');
        const answer = card.find('.answer-radio:checked').val() || null;

        // POST data
        $.post('<?= base_url("participant/exams/save_answer") ?>', {
            session_id: sessionId,
            question_id: questionId,
            answer: answer,
            current_index: currentIndex
        }, function(res){
            if(res.status === 'success') {
                highlightAnswered();
                updateNavColor(questionId, !!answer); // Update warna tombol di grid soal
            }
        }, 'json');
    }

    // navigasi tombol
    $('#prev-btn').click(function() {
        saveAnswer();
        if (currentIndex > 0) {
            currentIndex--;
            showQuestion(currentIndex);
        }
    });

    $('#next-btn').click(function() {
        saveAnswer();
        if (currentIndex < totalQuestions - 1) {
            currentIndex++;
            showQuestion(currentIndex);
        }
    });

    // *** SIMPAN OTOMATIS SAAT JAWABAN BERUBAH ***
    // NOTE: Event ini akan dipasang ulang di fungsi shuffleChildren
    $('.answer-radio').change(saveAnswer);

    // klik nomor soal di panel kanan
    $('.question-nav-btn').click(function() {
        saveAnswer();
        const targetIndex = $(this).data('index');
        currentIndex = targetIndex;
        showQuestion(currentIndex);
    });

    // tombol selesai
    $('#finish-btn').click(function() {
        // Pastikan jawaban terakhir tersimpan sebelum konfirmasi
        saveAnswer(); 
        
        Swal.fire({
            title: 'Akhiri ujian?',
            text: 'Pastikan semua jawaban sudah benar.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, kirim!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Asumsi: Anda akan membuat endpoint finish di controller
                // $.post('<?= base_url("participant/exams/finish") ?>', { session_id: sessionId }, function(res){ ... });
                
                Swal.fire('Selesai!', 'Ujian telah diselesaikan.', 'success').then(() => {
                    window.location.href = '<?= base_url("participant/exams") ?>';
                });
            }
        });
    });

    // polling status ujian
    setInterval(function(){
        $.get('<?= base_url("participant/exams/status") ?>/' + settingId, function(res){
            if(res.status === 'success' && res.exam_status !== 'started'){
                Swal.fire({
                    icon: 'info',
                    title: 'Ujian dijeda!',
                    text: 'Panitia telah menjeda ujian. Anda akan dikembalikan ke dashboard.',
                    confirmButtonText: 'OK'
                }).then(() => {
                    window.location.href = '<?= base_url("participant/exams") ?>';
                });
            }
        }, 'json');
    }, 5000);

    // timer
    function updateTimer(){
        let minutes = Math.floor(remainingSeconds / 60);
        let seconds = remainingSeconds % 60;
        $('#remaining-time').text(minutes + 'm ' + seconds + 's');
        if(remainingSeconds > 0) remainingSeconds--;
        else {
            Swal.fire('Waktu habis!', 'Ujian akan dikirim otomatis.', 'info').then(() => {
                // Panggil proses selesai otomatis
                $('#finish-btn').click(); 
            });
        }
    }
    setInterval(updateTimer, 1000);

    // Inisialisasi awal
    showQuestion(currentIndex);
    highlightAnswered();
});
</script>

<?= $this->endSection() ?>