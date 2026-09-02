<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ExamSessionModel;
use App\Models\QuestionModel;
use App\Models\ExamSettingModel;

class CorrectionController extends BaseController
{
    protected $sessionModel;
    protected $questionModel;
    protected $settingModel;

    public function __construct()
    {
        $this->sessionModel = new ExamSessionModel();
        $this->questionModel = new QuestionModel();
        $this->settingModel = new ExamSettingModel();
    }

    /**
     * Halaman daftar sesi ujian untuk dikoreksi (Admin Dashboard)
     */
    public function index($settingId)
    {
        $setting = $this->settingModel
            ->select('exam_settings.*, et.name AS exam_name') // Pilih semua kolom setting dan et.name sebagai exam_name
            ->join('exam_types et', 'et.id = exam_settings.exam_type_id', 'inner')
            ->where('exam_settings.id', $settingId)
            ->first();
            if (!$setting) {
                throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
            }

        // Ambil semua sesi untuk setting ujian ini, join dengan tabel user (asumsi UserModel ada)
            $sessions = $this->sessionModel
            ->select('exam_sessions.*, users.full_name as participant_name')
            ->join('users', 'users.id = exam_sessions.user_id', 'inner')
            ->where('exam_setting_id', $settingId)
            ->findAll();

            return view('admin/exams/correction_dashboard', [
                'title' => 'Koreksi Hasil Ujian: ' . $setting['exam_name'],
                'sessions' => $sessions,
                'setting' => $setting
            ]);
        }

    /**
     * Menghitung dan menyimpan skor ujian (AJAX/Single Call)
     */
    public function calculate_score($sessionId)
    {
        $session = $this->sessionModel->find($sessionId);

        if (!$session) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Sesi ujian tidak ditemukan']);
        }

        // Jika sesi sudah selesai atau sudah dikoreksi, skip
        if ($session['status'] !== 'finished' && $session['score'] !== null) {
            // Bisa jadi sesi masih 'started' tapi kita koreksi paksa
            // Atau sudah dikoreksi sebelumnya
        }

        $participantAnswers = json_decode($session['answers'], true) ?? [];
        $questionOrder = json_decode($session['question_order'], true);
        
        // Filter marker judul dari questionOrder untuk mendapatkan ID soal saja
        $realQuestionIds = array_filter($questionOrder, function($id) {
            return !str_contains($id, 'TITLE_');
        });

        // Ambil semua data soal, termasuk kunci jawaban
        $questions = $this->questionModel->getQuestionsByIds($realQuestionIds);
        $questionsLookup = [];
        foreach ($questions as $q) {
            $questionsLookup[$q['id']] = $q;
        }

        $correctCount = 0;
        $incorrectCount = 0;
        $unansweredCount = 0;
        $totalScore = 0;
        $totalQuestions = count($realQuestionIds);
        
        // Bobot nilai (asumsi 1 poin per soal benar)
        $scorePerQuestion = 100 / max(1, $totalQuestions); 

        foreach ($realQuestionIds as $qId) {
            $kunciJawaban = $questionsLookup[$qId]['correct_answer'] ?? null;
            $jawabanPeserta = $participantAnswers[$qId] ?? null;

            if ($jawabanPeserta === null || $jawabanPeserta === '') {
                $unansweredCount++;
            } elseif ($jawabanPeserta == $kunciJawaban) {
                $correctCount++;
                $totalScore += $scorePerQuestion;
            } else {
                $incorrectCount++;
            }
        }
        
        // Simpan hasil koreksi ke database
        $this->sessionModel->update($sessionId, [
            'score' => round($totalScore, 2),
            'correct_count' => $correctCount,
            'incorrect_count' => $incorrectCount,
            'unanswered_count' => $unansweredCount,
            'status' => 'finished' // Pastikan status menjadi finished
        ]);

        return $this->response->setJSON([
            'total_questions' => $totalQuestions,
            'correct_count' => $correctCount,
            'status' => 'success',
            'score' => round($totalScore, 2),
            'message' => 'Skor berhasil dihitung dan disimpan.'
        ]);
    }


    public function get_correction_details($sessionId)
    {
        $session = $this->sessionModel->find($sessionId);

        if (!$session) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Sesi ujian tidak ditemukan']);
        }

        $participantAnswers = json_decode($session['answers'], true) ?? [];
        $questionOrder = json_decode($session['question_order'], true);
        
        // Filter marker judul dari questionOrder
        $realQuestionIds = array_filter($questionOrder, function($id) {
            return !str_contains($id, 'TITLE_');
        });

        // Ambil semua data soal, termasuk kunci jawaban
        $questions = $this->questionModel->getQuestionsByIds($realQuestionIds);
        $questionsLookup = [];
        foreach ($questions as $q) {
            $questionsLookup[$q['id']] = $q;
        }
        
        $details = [];
        $correctCount = 0;
        $incorrectCount = 0;
        $unansweredCount = 0;
        $totalQuestions = count($realQuestionIds); // Total soal sebenarnya

        foreach ($questionOrder as $item) {
            if (str_contains($item, 'TITLE_')) {
                // Ini adalah penanda kategori (judul)
                $details[] = [
                    'is_title' => true,
                    'id' => $item
                ];
                continue;
            }

            $question = $questionsLookup[$item] ?? null;
            if (!$question) continue;

            $qId = $question['id'];
            $correctAnswer = $question['correct_answer'] ?? null;
            $participantAnswer = $participantAnswers[$qId] ?? null;

            // Logika Perhitungan Summary (diulang di sini untuk memastikan akurasi data modal)
            $isCorrect = ($participantAnswer === $correctAnswer && $participantAnswer !== null);

            if ($participantAnswer === null || $participantAnswer === '') {
                $unansweredCount++;
            } elseif ($isCorrect) {
                $correctCount++;
            } else {
                $incorrectCount++;
            }
            
            // Kumpulkan semua opsi yang tersedia
            $options = [];
            foreach (['A', 'B', 'C', 'D', 'E'] as $opt) {
                if (!empty($question['option_' . strtolower($opt)])) {
                    $options[$opt] = $question['option_' . strtolower($opt)];
                }
            }

            $details[] = [
                'is_title' => false,
                'id' => $qId,
                'question_text' => $question['question_text'],
                'options' => $options,
                'correct_answer' => $correctAnswer,
                'participant_answer' => $participantAnswer,
                'is_correct' => $isCorrect
            ];
        }

        return $this->response->setJSON([
            'status' => 'success',
            'details' => $details,
            'session_id' => $sessionId,
            'session_status' => $session['status'],
            // *** DATA SUMMARY BARU ***
            'summary' => [
                'total_questions' => $totalQuestions,
                'correct_count' => $correctCount,
                'incorrect_count' => $incorrectCount,
                'unanswered_count' => $unansweredCount,
            ]
        ]);
    }
}