<?php

namespace App\Controllers\Participant;

use App\Controllers\BaseController;
use App\Models\ExamTypeModel;
use App\Models\ExamSettingModel;
use App\Models\ExamModel;
use App\Models\QuestionModel;
use App\Models\CategoryModel;

class ExamController extends BaseController
{
    protected $examTypeModel;
    protected $examSettingModel;
    protected $examModel;
    protected $settingModel;
    protected $questionModel;
    protected $categoryModel;

    public function __construct()
    {
        $this->examTypeModel = new ExamTypeModel();
        $this->examSettingModel = new ExamSettingModel();
        $this->examModel = new ExamModel();
        $this->settingModel = new ExamSettingModel();
        $this->questionModel = new QuestionModel();
        $this->categoryModel = new CategoryModel();
    }

    /**
     * Dashboard ujian peserta
     */
    public function index()
    {
        $eventId = session()->get('event_id');

        // Ambil semua ujian untuk event ini
        $exams = $this->examTypeModel
        ->select('exam_types.*, exam_settings.id AS setting_id, exam_settings.status, exam_settings.start_time, exam_settings.duration')
        ->join('exam_settings', 'exam_settings.exam_type_id = exam_types.id', 'left')
        ->where('exam_types.event_id', $eventId)
        ->orderBy('exam_types.id', 'ASC')
        ->findAll();

        return view('participant/exams/dashboard', [
            'title' => 'Dashboard Ujian',
            'exams' => $exams
        ]);
    }

    /**
     * Cek status ujian (AJAX)
     */
    public function status($settingId)
    {
        $setting = $this->settingModel->find($settingId);
        if (!$setting) return $this->response->setJSON(['status' => 'error', 'message' => 'Setting tidak ditemukan']);

        return $this->response->setJSON([
            'status' => 'success',
            'exam_status' => $setting['status'],
            'remaining_seconds' => $setting['remaining_seconds'] ?? null
        ]);
    }

    public function start($settingId)
    {
        $userId = session()->get('user_id');
        $setting = $this->settingModel->find($settingId);

        if (!$setting) {
            return redirect()->back()->with('error', 'Setting ujian tidak ditemukan.');
        }

    // Cek apakah ujian sudah selesai, jika iya, redirect
        if ($setting['status'] === 'finished') {
            return redirect()->to(base_url('participant/exams'))
            ->with('error', 'Ujian ini telah selesai.');
        }

    // Cek status running/pause
        if ($setting['status'] !== 'started' || $setting['is_paused']) {
            return redirect()->back()->with('error', 'Ujian belum dimulai atau sedang dijeda oleh panitia.');
        }

        $sessionModel = new \App\Models\ExamSessionModel();

    // Cek apakah peserta sudah punya sesi ujian
        $session = $sessionModel->where([
            'user_id' => $userId,
            'exam_setting_id' => $settingId
        ])->first();

    // Jika belum ada, buat baru
        if (!$session) {
            
            // 1. Ambil semua soal
            // PENTING: Model (QuestionModel) harus me-join dan memberikan kolom 'category_id'
            // Kita asumsikan 'category_id' di alias sebagai 'exam_type_id' di Model, 
            // ATAU kita gunakan 'category_id' secara langsung di Controller.
            // Kita akan gunakan 'category_id' yang merupakan kolom di tabel questions.
            $allQuestions = $this->questionModel->getQuestionsByExam($settingId);
            
            // 2. Kelompokkan soal berdasarkan category_id
            $questionsByExamType = [];
            $defaultTypeId = $setting['exam_type_id'] ?? null; // Fallback jika setting tidak punya exam_type_id
            
            foreach ($allQuestions as $q) {
                // Menggunakan 'category_id' dari Model (sesuai QuestionModel.php Anda)
                // Jika QuestionModel sudah meng-alias-kan, gunakan alias tersebut.
                // Disini kita asumsikan 'category_id' tersedia karena di-SELECT.
                $typeId = $q['category_id'] ?? $defaultTypeId; 

                if (!isset($questionsByExamType[$typeId])) {
                    $questionsByExamType[$typeId] = [];
                }
                $questionsByExamType[$typeId][] = $q['id'];
            }
            
            $finalQuestionOrder = [];

            // 3. Loop melalui setiap kategori dan lakukan pengacakan (suffle) di dalamnya
            foreach ($questionsByExamType as $typeId => $questionIds) {
                
                // --- Tambahkan Halaman Judul Kategori (Marker) ---
                $finalQuestionOrder[] = "TITLE_{$typeId}"; 
                // --------------------------------------------------

                if ($setting['randomize_questions']) {
                    shuffle($questionIds); // Acak soal di dalam kategori ini
                }
                
                // Tambahkan ID soal yang sudah diacak ke urutan final
                $finalQuestionOrder = array_merge($finalQuestionOrder, $questionIds);
            }


            $sessionId = $sessionModel->insert([
                'user_id' => $userId,
                'exam_setting_id' => $settingId,
                'question_order' => json_encode($finalQuestionOrder), 
                'answers' => json_encode([]),
                'current_index' => 0,
                'status' => 'started',
                'started_at' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ], true);

            $session = $sessionModel->find($sessionId);
        } else {
            // DECODE JAWABAN SAAT SESSION DITEMUKAN
            $session['answers'] = json_decode($session['answers'], true) ?? [];
        }

    // ========== LOGIKA PERHITUNGAN WAKTU SINKRON DENGAN ADMIN ==========
        $now = \CodeIgniter\I18n\Time::now('Asia/Jakarta');
        $status = $setting['status'];
        $remainingSeconds = 0;

        if ($status === 'started') {
            $endTime = $setting['end_time']
            ? new \CodeIgniter\I18n\Time($setting['end_time'])
            : (new \CodeIgniter\I18n\Time($setting['start_time']))->addMinutes($setting['duration']);

            $remainingSeconds = max(0, $endTime->getTimestamp() - $now->getTimestamp());

            if ($remainingSeconds <= 0) {
                $sessionModel->update($session['id'], ['status' => 'finished']);
                return redirect()->to(base_url('participant/exams'))
                ->with('error', 'Waktu ujian telah habis.');
            }
        } elseif ($status === 'paused') {
            $remainingSeconds = $setting['paused_seconds'] ?? ($setting['duration'] * 60);
        } else {
            $remainingSeconds = $setting['duration'] * 60;
        }
    // ====================================================================

        $questionOrderWithMarkers = json_decode($session['question_order'], true); 
        $currentIndex = $session['current_index'];

        // Ambil SEMUA ID SOAL (tanpa marker/judul)
        $realQuestionIds = array_filter($questionOrderWithMarkers, function($id) {
            return !str_contains($id, 'TITLE_'); 
        });
        
        // Ambil semua data soal
        $questionsData = $this->questionModel->getQuestionsByIds($realQuestionIds);
        
        // Konversi array $questionsData menjadi array asosiatif [id => data_soal]
        $questionsLookup = [];
        foreach ($questionsData as $q) {
            $questionsLookup[$q['id']] = $q;
        }

        // *** PERBAIKAN PENGAMBILAN NAMA KATEGORI ***
        $categories = $this->categoryModel->findAll(); // Mengambil semua kategori
        $categoriesLookup = [];
        foreach ($categories as $cat) {
           $categoriesLookup[$cat['id']] = $cat; // Kunci menggunakan ID kategori
       }
       
        // Gabungkan data soal (termasuk marker/judul) dan kirim ke view
       $finalQuestionsList = [];
       foreach ($questionOrderWithMarkers as $item) {
         if (str_contains($item, 'TITLE_')) {
                // Ini adalah penanda kategori
            $categoryId = str_replace('TITLE_', '', $item);
            
                // Ambil nama kategori menggunakan Category ID
            $categoryName = $categoriesLookup[$categoryId]['name'] ?? $categoriesLookup[$categoryId]['category_name'] ?? 'Kategori Tidak Dikenal';
            
            $finalQuestionsList[] = [
                'is_title' => true,
                'title' => $categoryName, 
                'id' => $item 
            ];
        } else {
                // Ini adalah soal
            if (isset($questionsLookup[$item])) {
                $finalQuestionsList[] = array_merge($questionsLookup[$item], ['is_title' => false]);
            }
        }
    }


    return view('participant/exams/exams_start', [
        'title' => 'Ujian',
        'setting' => $setting,
        'session' => $session,
        'questions' => $finalQuestionsList, 
        'current_index' => $currentIndex,
        'remaining_seconds' => $remainingSeconds,
    ]);
}

public function take($settingId)
{
    $exam = $this->examSettingModel->find($settingId);

    if (!$exam || $exam['status'] !== 'started') {
        return redirect()->back()->with('error', 'Ujian tidak tersedia');
    }

    return view('participant/exams/take', [
        'exam' => $exam
    ]);
}

public function save_answer()
{
    if (!$this->request->isAJAX()) {
        return $this->fail('Invalid request');
    }

    $sessionId = $this->request->getPost('session_id');
    $questionId = $this->request->getPost('question_id');
    $answer = $this->request->getPost('answer');
    $currentIndex = $this->request->getPost('current_index');

    $sessionModel = new \App\Models\ExamSessionModel();
    $session = $sessionModel->find($sessionId);

    if (!$session) {
        return $this->response->setJSON(['status' => 'error', 'message' => 'Session tidak ditemukan']);
    }

    // Decode jawaban sebelumnya
    $answers = json_decode($session['answers'], true) ?? [];
    $answers[$questionId] = $answer;

    // Simpan ke database
    $sessionModel->update($sessionId, [
        'answers' => json_encode($answers),
        'current_index' => $currentIndex,
        'updated_at' => date('Y-m-d H:i:s')
    ]);

    return $this->response->setJSON([
        'status' => 'success',
        'saved' => true,
        'answers' => $answers
    ]);
}

}