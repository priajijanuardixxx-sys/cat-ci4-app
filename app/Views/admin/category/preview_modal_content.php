<h5 class="fw-bold mb-3">Kategori: <?= esc($category['name']) ?></h5>

<?php if (empty($questions)): ?>
    <div class="text-center text-secondary">Belum ada soal di kategori ini.</div>
<?php else: ?>
    <div class="accordion" id="accordionQuestions">
        <?php foreach ($questions as $index => $q): ?>
            <?php 
                $qid = 'q' . $q['id'];
                $correct = strtoupper($q['correct_answer']);
            ?>
            <div class="accordion-item mb-2 shadow-sm">
                <h2 class="accordion-header" id="heading<?= $qid ?>">
                    <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?= $qid ?>" aria-expanded="false" aria-controls="collapse<?= $qid ?>">
                        <?= ($index + 1) . '. ' . esc($q['question_text']) ?>
                    </button>
                </h2>
                <div id="collapse<?= $qid ?>" class="accordion-collapse collapse" aria-labelledby="heading<?= $qid ?>" data-bs-parent="#accordionQuestions">
                    <div class="accordion-body">
                        <ul class="list-group">
                            <?php foreach (['A', 'B', 'C', 'D'] as $opt): ?>
                                <?php 
                                    $optionKey = 'option_' . strtolower($opt);
                                    $optionText = $q[$optionKey] ?? null;
                                ?>
                                <?php if ($optionText): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center 
                                        <?= $opt === $correct ? 'list-group-item-success fw-bold' : '' ?>">
                                        <span><?= $opt ?>. <?= esc($optionText) ?></span>
                                        <?php if ($opt === $correct): ?>
                                            <span class="badge bg-success">Benar</span>
                                        <?php endif; ?>
                                    </li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </ul>
                        <div class="mt-3 text-end">
                            <a href="<?= base_url('admin/questions/edit/' . $q['id']) ?>" class="btn btn-sm btn-warning">
                                <i class="bi bi-pencil-square"></i> Edit Soal
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
