<?php 
namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ExamTypeModel;
use App\Models\ExamSettingModel;
use App\Models\CategoryModel;
use App\Models\ExamSettingCategoryModel;

class ExamController extends BaseController
{
    protected $examTypeModel;
    protected $examSettingModel;
    protected $categoryModel;
    protected $examSettingCategoryModel;

    public function __construct()
    {
        $this->examTypeModel = new ExamTypeModel();
        $this->examSettingModel = new ExamSettingModel();
        $this->categoryModel = new CategoryModel();
        $this->examSettingCategoryModel = new ExamSettingCategoryModel();
        helper(['form', 'url']);
    }

    private function getActiveEventId()
    {
        return session()->get('event_id');
    }


    public function index()
    {
        $data['title'] = 'Pengaturan Ujian';
        return view('admin/exams/index', $data);
    }

    /** 🔹 AJAX: Muat tabel jenis ujian */
    public function loadExamTypes()
    {
    // Cek peran pengguna (Asumsi peran Superadmin adalah 'superadmin')
        $userRole = session()->get('role'); 
        $isSuperAdmin = ($userRole === 'superadmin');

    // Ambil Event ID hanya jika pengguna BUKAN Superadmin
        $eventId = null;
        if (!$isSuperAdmin) {
        $eventId = $this->getActiveEventId(); // Ambil ID event dari sesi/model
    }

    // 1. Buat Subquery untuk mendapatkan ID setting terbaru per exam_type
    $latestSettingIdSubquery = $this->examTypeModel->db->table('exam_settings')
    ->select('MAX(id) as latest_id, exam_type_id')
    ->groupBy('exam_type_id')
    ->getCompiledSelect(); 

    $builder = $this->examTypeModel
    ->select('exam_types.*, es.id AS setting_id, es.status, es.start_time, es.end_time, es.duration')

        // Gabungkan exam_types dengan hasil subquery
    ->join("({$latestSettingIdSubquery}) AS latest_es", 'latest_es.exam_type_id = exam_types.id', 'left')

        // Gabungkan lagi dengan exam_settings untuk mendapatkan detail status
    ->join('exam_settings AS es', 'es.id = latest_es.latest_id', 'left');

    // *** PERBAIKAN: LOGIKA KONDISIONAL ***
    if (!$isSuperAdmin && $eventId) {
        // Hanya tambahkan kondisi WHERE jika pengguna BUKAN Superadmin dan Event ID tersedia
        $builder->where('exam_types.event_id', $eventId);
    }
    // **********************************
    
    $data['examTypes'] = $builder
    ->orderBy('exam_types.id', 'ASC')
    ->findAll();

    return view('admin/exams/table_types', $data);
}


/** 🔹 Tambah / Update Jenis Ujian */
public function saveExamType()
{
    if (!$this->request->isAJAX() || $this->request->getMethod() !== 'POST') {
        return $this->response->setStatusCode(405)
        ->setJSON(['status' => 'error', 'message' => 'Metode tidak diizinkan']);
    }

    $eventId = $this->getActiveEventId();
    $id = $this->request->getPost('id');
    $data = [
        'event_id' => $eventId,
        'name' => $this->request->getPost('name'),
        'description' => $this->request->getPost('description'),
    ];

    if (empty($id)) {
        $this->examTypeModel->insert($data);
        $message = 'Jenis ujian berhasil ditambahkan!';
    } else {
        $this->examTypeModel->update($id, $data);
        $message = 'Jenis ujian berhasil diperbarui!';
    }

    return $this->response->setJSON(['status' => 'success', 'message' => $message]);
}

/** 🔹 Hapus Jenis Ujian */
public function deleteExamType($id)
{
    $eventId = $this->getActiveEventId();
    $examType = $this->examTypeModel->find($id);

    if (!$examType || $examType['event_id'] != $eventId) {
        return $this->response->setJSON(['status' => 'error', 'message' => 'Data tidak ditemukan.']);
    }

        // Hapus setting & relasi kategori
    $settings = $this->examSettingModel->where('exam_type_id', $id)->findAll();
    foreach ($settings as $s) {
        $this->examSettingCategoryModel->where('exam_setting_id', $s['id'])->delete();
    }
    $this->examSettingModel->where('exam_type_id', $id)->delete();

    $this->examTypeModel->delete($id);

    return $this->response->setJSON(['status' => 'success', 'message' => 'Jenis ujian berhasil dihapus.']);
}

/** 🔹 Muat form setting ujian berdasarkan exam_type_id */
public function loadExamSetting($examTypeId)
{
    $setting = $this->examSettingModel
    ->where('exam_type_id', $examTypeId)
    ->first();

        // jika belum ada, siapkan default
    if (!$setting) {
        $setting = [
            'id' => '',
            'exam_type_id' => $examTypeId,
            'duration' => 60,
            'passing_grade' => 70,
            'mode' => 'online',
            'randomize_questions' => 1,
            'show_result' => 1,
            'start_time' => null,
            'end_time' => null,
        ];
    }

    $categories = $this->categoryModel
    ->where('event_id', $this->getActiveEventId())
    ->findAll();

    $linked = $this->examSettingCategoryModel
    ->where('exam_setting_id', $setting['id'])
    ->findAll();

    $linkedIds = array_column($linked, 'category_id');

    return view('admin/exams/form_setting', [
        'setting' => $setting,
        'categories' => $categories,
        'linkedIds' => $linkedIds,
    ]);
}

/** 🔹 Simpan setting ujian */
public function saveExamSetting()
{
    if (!$this->request->isAJAX()) {
        return $this->response->setStatusCode(405)->setJSON([
            'status' => 'error',
            'message' => 'Metode tidak diizinkan.'
        ]);
    }

    // Ambil input
    $id = $this->request->getPost('id');
    $examTypeId = $this->request->getPost('exam_type_id');

    $data = [
        'exam_type_id'        => $examTypeId,
        'start_time'          => $this->request->getPost('start_time'),
        'end_time'            => $this->request->getPost('end_time'),
        'duration'            => $this->request->getPost('duration'),
        'passing_grade'       => $this->request->getPost('passing_grade'),
        'mode'                => $this->request->getPost('mode'),
        'randomize_questions' => $this->request->getPost('randomize_questions') ? 1 : 0,
        'show_result'         => $this->request->getPost('show_result') ? 1 : 0,
    ];

    // Validasi ringan
    if (empty($examTypeId)) {
        return $this->response->setJSON([
            'status'  => 'error',
            'message' => 'Tipe ujian tidak valid.'
        ]);
    }

    // ==============================
    // CEK: jika tidak ada ID, cari berdasarkan exam_type_id
    // ==============================
    if (empty($id)) {
        $existing = $this->examSettingModel
        ->where('exam_type_id', $examTypeId)
        ->first();

        if ($existing) {
            // Sudah ada → update
            $id = $existing['id'];
            $this->examSettingModel->update($id, $data);
        } else {
            // Belum ada → insert baru
            $id = $this->examSettingModel->insert($data);
        }
    } else {
        // Ada ID → update langsung
        $this->examSettingModel->update($id, $data);
    }

    // ==============================
    // Simpan relasi kategori soal
    // ==============================
    $categories = $this->request->getPost('categories') ?? [];

    // Hapus kategori lama
    $this->examSettingCategoryModel
    ->where('exam_setting_id', $id)
    ->delete();

    // Tambahkan kategori baru
    foreach ($categories as $catId) {
        $this->examSettingCategoryModel->insert([
            'exam_setting_id' => $id,
            'category_id'     => $catId,
            'question_count'  => 0,
        ]);
    }

    // ==============================
    // Respon sukses
    // ==============================
    return $this->response->setJSON([
        'status'  => 'success',
        'message' => 'Pengaturan ujian berhasil disimpan!',
        'setting_id' => $id, // bisa dipakai di frontend nanti
    ]);
}

}
