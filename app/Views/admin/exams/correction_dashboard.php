<?= $this->extend('layout/admin_layout') ?>
<?= $this->section('content') ?>

<section class="correction py-5">
    <div class="container">
        <h2 class="mb-4"><?= esc($title) ?></h2>

        <div class="card">
            <div class="card-body">
                <table class="table table-striped" id="correction-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Peserta</th>
                            <th>Status Ujian</th>
                            <th>Jawaban Benar</th>
                            <th>Skor</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($sessions as $index => $session): ?>
                            <tr data-session-id="<?= $session['id'] ?>">
                                <td><?= $index + 1 ?></td>
                                <td><?= esc($session['participant_name']) ?></td>
                                <td class="session-status">
                                    <?= ucfirst($session['status']) ?>
                                    <?= $session['status'] === 'started' ? '<span class="badge bg-warning">Berjalan</span>' : '' ?>
                                    <?= $session['status'] === 'finished' ? '<span class="badge bg-success">Selesai</span>' : '' ?>
                                </td>
                                <td class="session-correct-count">
                                    <?php 
                                    // Asumsi: total_questions sudah ada di sesi jika sudah pernah dikoreksi
                                    if (isset($session['correct_count']) && $session['correct_count'] !== null && isset($session['total_questions'])) {
                                        echo $session['correct_count'] . ' / ' . $session['total_questions'];
                                    } elseif (isset($session['correct_count']) && $session['correct_count'] !== null) {
                                        echo $session['correct_count'] . ' / -'; // Tampilkan hanya count jika total tidak ada
                                    } else {
                                        echo '-';
                                    }
                                    ?>
                                </td>
                                <td class="session-score">
                                    <strong><?= $session['score'] ?? '-' ?></strong>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-info detail-btn me-2" data-id="<?= $session['id'] ?>">
                                        Detail Jawaban
                                    </button>
                                    
                                    <?php if ($session['status'] === 'finished' || $session['score'] === null): ?>
                                        <button class="btn btn-sm btn-primary calculate-btn" data-id="<?= $session['id'] ?>">
                                            <?= $session['score'] !== null ? 'Hitung Ulang' : 'Koreksi' ?>
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="modal fade" id="correctionDetailModal" tabindex="-1" aria-labelledby="correctionDetailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="correctionDetailModalLabel">Detail Koreksi Ujian - Sesi ID: <span id="modal-session-id"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    
                    <div id="summary-section" class="mb-4">
                        </div>
                    
                    <div id="questions-list">
                        <p class="text-center text-muted">Memuat data...</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
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
        // Fungsi untuk menghitung skor via AJAX
        $(document).on('click', '.calculate-btn', function() {
            const sessionId = $(this).data('id');
            const $row = $('tr[data-session-id="' + sessionId + '"]');
            const $btn = $(this);

            $btn.attr('disabled', true).text('Menghitung...');

            $.post('<?= base_url("admin/correction/calculate_score") ?>/' + sessionId, {}, function(res) {
                if (res.status === 'success') {
                    Swal.fire('Berhasil!', 'Skor berhasil dihitung: ' + res.score, 'success');
                    
                    // *** PERBAIKAN: UPDATE JAWABAN BENAR DAN SKOR ***
                    // 1. Update kolom Skor
                    $row.find('.session-score strong').text(res.score);
                    
                    // 2. Update kolom Jawaban Benar (Format: Benar / Total)
                    const totalQuestions = res.total_questions || '-'; // Ambil total soal dari respons
                    const correctText = `${res.correct_count} / ${totalQuestions}`;
                    $row.find('.session-correct-count').text(correctText);
                    
                    // 3. Update Status
                    $row.find('.session-status').html('<span class="badge bg-success">Selesai</span>');
                    // ***********************************************

                } else {
                    Swal.fire('Gagal', res.message, 'error');
                }
                $btn.attr('disabled', false).text('Hitung Ulang');

            }, 'json');
        });

        // Fungsi untuk menampilkan modal detail koreksi
        $(document).on('click', '.detail-btn', function() {
            const sessionId = $(this).data('id');
            const $questionsList = $('#questions-list');
            const $summarySection = $('#summary-section');
            
            // Inisialisasi Modal
            const correctionDetailModal = new bootstrap.Modal(document.getElementById('correctionDetailModal'));

            // Reset modal dan tampilkan loading
            $('#modal-session-id').text(sessionId);
            $summarySection.empty(); // Kosongkan summary
            $questionsList.html('<p class="text-center text-muted">Memuat detail soal dan jawaban...</p>');
            
            correctionDetailModal.show();

            // Lakukan AJAX call ke Controller
            $.get('<?= base_url("admin/correction/details") ?>/' + sessionId, function(res) {
                if (res.status === 'success') {
                    // Panggil render dengan data summary
                    renderCorrectionDetails(res.details, $questionsList, res.summary); 
                } else {
                    $questionsList.html('<div class="alert alert-danger">' + res.message + '</div>');
                }
            }, 'json').fail(function() {
                $questionsList.html('<div class="alert alert-danger">Gagal memuat data dari server. Pastikan rute dan controller sudah benar.</div>');
            });
        });

        /**
         * Fungsi untuk merender detail soal dan jawaban ke dalam modal
         */
        function renderCorrectionDetails(details, $container, summary) {
            
            // --- RENDER SUMMARY ---
            let summaryHtml = `
            <div class="row text-center">
            <div class="col-md-3 mb-2">
            <div class="card bg-primary text-white">
            <div class="card-body py-2">
            <p class="mb-0 fw-bold">Skor Akhir</p>
            <h4 class="mb-0">${summary.final_score ?? summary.score ?? '-'}</h4>
            </div>
            </div>
            </div>
            <div class="col-md-3 mb-2">
            <div class="card bg-success text-white">
            <div class="card-body py-2">
            <p class="mb-0 fw-bold">Jawaban Benar</p>
            <h4 class="mb-0">${summary.correct_count}</h4>
            </div>
            </div>
            </div>
            <div class="col-md-3 mb-2">
            <div class="card bg-danger text-white">
            <div class="card-body py-2">
            <p class="mb-0 fw-bold">Jawaban Salah</p>
            <h4 class="mb-0">${summary.incorrect_count}</h4>
            </div>
            </div>
            </div>
            <div class="col-md-3 mb-2">
            <div class="card bg-secondary text-white">
            <div class="card-body py-2">
            <p class="mb-0 fw-bold">Tidak Dijawab</p>
            <h4 class="mb-0">${summary.unanswered_count}</h4>
            </div>
            </div>
            </div>
            </div>
            <p class="text-end text-muted mt-2">Total Soal: ${summary.total_questions}</p>
            `;
            $('#summary-section').html(summaryHtml);
            // ----------------------

            let html = '';
            let questionCounter = 0;

            details.forEach(q => {
                if (q.is_title) {
                    // Halaman Judul Kategori
                    const categoryName = q.id.replace('TITLE_', 'Kategori ID: ');
                    html += `<h4 class="mt-4 mb-3 text-primary border-bottom pb-2">Kategori: ${categoryName}</h4>`;
                } else {
                    questionCounter++;
                    
                    let resultBadge;
                    let resultColor;

                    if (q.participant_answer === null || q.participant_answer === '') {
                        resultBadge = 'Tidak Dijawab';
                        resultColor = 'secondary';
                    } else if (q.is_correct) {
                        resultBadge = 'BENAR';
                        resultColor = 'success';
                    } else {
                        resultBadge = 'SALAH';
                        resultColor = 'danger';
                    }

                    html += `
                        <div class="card mb-4 shadow-sm border-${resultColor}">
                        <div class="card-body">
                        <h6 class="card-title">Soal #${questionCounter} (ID: ${q.id}) 
                        <span class="badge bg-${resultColor}">${resultBadge}</span>
                        </h6>
                        <p class="question-text">${q.question_text}</p>

                        <ul class="list-group list-group-flush mt-3">
                        `;
                    
                    // Loop Opsi Jawaban (A, B, C...)
                    Object.keys(q.options).forEach(optKey => {
                        const optText = q.options[optKey];
                        
                        let liClass = 'list-group-item';
                        let icon = '';
                        
                        // 1. KUNCI JAWABAN (Selalu hijau)
                        if (optKey === q.correct_answer) {
                            liClass += ' list-group-item-success fw-bold';
                            icon = '✅ Kunci Jawaban';
                        }
                        
                        // 2. JAWABAN PESERTA (Overlay)
                        if (optKey === q.participant_answer) {
                            if (optKey === q.correct_answer) {
                                // Benar, sudah ditandai hijau di atas
                                icon = '✅ Jawaban Peserta & Kunci';
                            } else {
                                // Salah, tandai merah
                                liClass = 'list-group-item list-group-item-danger fw-bold'; // Timpa warna jika salah
                                icon = '❌ Jawaban Peserta';
                            }
                        } else if (optKey === q.correct_answer) {
                            // Kunci Jawaban yang tidak dipilih oleh peserta
                            icon = '✅ Kunci Jawaban';
                        }

                        html += `
                        <li class="${liClass}">
                        ${icon}
                        <span class="ms-2">${optKey}. ${optText}</span>
                        </li>
                        `;
                    });

                    html += `
                        </ul>
                        </div>
                        </div>
                        `;
                }
            });

            $container.html(html);

            // Jika menggunakan MathJax, panggil render di sini:
            // if (typeof MathJax !== 'undefined') {
            //     MathJax.typesetPromise([$container[0]]);
            // }
        }
    });
</script>

<?= $this->endSection() ?>