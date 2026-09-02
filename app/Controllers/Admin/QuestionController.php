<?php namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\QuestionModel;
use App\Models\CategoryModel;
use App\Models\EventModel;
use App\Models\RoleModel;
use PhpOffice\PhpSpreadsheet\IOFactory;

class QuestionController extends BaseController
{
    protected $questionModel;
    protected $categoryModel;
    protected $eventModel;
    protected $roleModel;

    public function __construct()
    {
        $this->questionModel = new QuestionModel();
        $this->categoryModel = new CategoryModel();
        $this->eventModel = new EventModel();
        $this->roleModel = new RoleModel();
        helper(['form', 'url', 'html']);
    }

    private function getActiveEventId()
    {
       
        if (session()->get('role_name') === 'Super Admin') {
            return null; 
        }
        return session()->get('event_id');
    }


    private function getQuestionBaseQuery()
    {
        $eventId = $this->getActiveEventId();
        $role = session()->get('role_name');

        $query = $this->questionModel
        ->select('questions.*, categories.name as category_name')
        ->join('categories', 'categories.id = questions.category_id')
        ->orderBy('questions.id', 'DESC');
        
        // Filter wajib untuk Panitia
        if ($eventId !== null) {
            $query->where('questions.event_id', $eventId);
        }
        
        return $query;
    }

    public function index()
    {
        $eventId = $this->getActiveEventId();
        $role = session()->get('role_name');
        
        // Cek kategori: Panitia harus memiliki kategori di event-nya
        // ... (logic check category count) ...
        
        $data['title'] = 'Bank Soal';
        
        // --- DATA PENTING UNTUK MODAL IMPORT ---
        $queryCategories = $this->categoryModel;
        if ($eventId !== null) {
            $queryCategories->where('event_id', $eventId);
        }
        $data['categories'] = $queryCategories->findAll(); // Variabel $categories dikirim di sini
        // --- END DATA PENTING ---
        
        $data['role'] = $role;

        return view('admin/question/index', $data); // $data yang berisi $categories dikirim ke view
    }
    
    public function create()
    {
        $eventId = $this->getActiveEventId();
        
        if ($eventId === null && session()->get('role_name') !== 'Super Admin') {
            session()->setFlashdata('error', 'Event tidak teridentifikasi.');
            return redirect()->to(base_url('admin/questions'));
        }
        
        $data['title'] = 'Tambah Soal Baru';
        
        // Filter kategori berdasarkan Event ID
        $queryCategories = $this->categoryModel;
        if ($eventId !== null) {
            $queryCategories->where('event_id', $eventId);
        }

        $data['categories'] = $queryCategories->findAll();

        $data['role'] = session()->get('role_name');
        
        return view('admin/question/create', $data);
    }

    public function edit($id = null)
    {
        $eventId = $this->getActiveEventId();
        $question = $this->questionModel->find($id);

        if (!$question) {
            session()->setFlashdata('error', 'Soal tidak ditemukan.');
            return redirect()->to(base_url('admin/questions'));
        }

        if ($eventId !== null && $question['event_id'] !== $eventId) {
            session()->setFlashdata('error', 'Akses Ditolak: Soal ini bukan milik Event Anda.');
            return redirect()->to(base_url('admin/questions'));
        }

        $data['question'] = $question;
        $data['title'] = 'Edit Soal ID: ' . $id;
        
        $queryCategories = $this->categoryModel;
        if ($eventId !== null) {
            $queryCategories->where('event_id', $eventId);
        }
        $data['categories'] = $queryCategories->findAll();

        $data['role'] = session()->get('role_name');

        return view('admin/question/edit', $data);
    }

    public function save()
    {
        $eventId = $this->getActiveEventId();
        $id = $this->request->getPost('id');

        if ($eventId === null && session()->get('role_name') !== 'Super Admin') {
            session()->setFlashdata('error', 'Event tidak teridentifikasi.');
            return redirect()->to(base_url('admin/questions'));
        }

        if (!$this->validate([
            'category_id' => 'required|is_natural_no_zero',
            'question_text' => 'required',
            'option_a' => 'required',
            'option_b' => 'required',
            'option_c' => 'required',
            'option_d' => 'required',
            'correct_answer' => 'required|alpha|exact_length[1]'
        ])) {
            session()->setFlashdata('error', 'Gagal menyimpan. Harap periksa input Anda.');
            return redirect()->back()->withInput();
        }

        
        $data = [
            'event_id'       => $eventId ?? $this->request->getPost('event_id_for_sa'), 
            'category_id'    => $this->request->getPost('category_id'),
            'question_text'  => $this->request->getPost('question_text'),
            'option_a'       => $this->request->getPost('option_a'),
            'option_b'       => $this->request->getPost('option_b'),
            'option_c'       => $this->request->getPost('option_c'),
            'option_d'       => $this->request->getPost('option_d'),
            'option_e'       => $this->request->getPost('option_e') ?? null, 
            'correct_answer' => strtoupper($this->request->getPost('correct_answer')),
        ];

        $message = empty($id) ? 'ditambahkan' : 'diperbarui';

        if (empty($id)) {
            $data['created_at'] = date('Y-m-d H:i:s');
            $this->questionModel->insert($data);
        } else {
            $data['updated_at'] = date('Y-m-d H:i:s');
            $this->questionModel->update($id, $data);
        }
        
        session()->setFlashdata('success', 'Soal berhasil ' . $message . ' ke Bank Soal.');

        return redirect()->to(base_url('admin/questions'));
    }

    public function delete($id = null)
    {
        $eventId = $this->getActiveEventId();
        $question = $this->questionModel->find($id);

        if ($id === null || !$question) {
            session()->setFlashdata('error', 'Soal tidak ditemukan.');
            return redirect()->to(base_url('admin/questions'));
        }
        
        if ($eventId !== null && $question['event_id'] !== $eventId) {
            session()->setFlashdata('error', 'Akses Ditolak: Soal ini bukan milik Event Anda.');
            return redirect()->to(base_url('admin/questions'));
        }

        $this->questionModel->delete($id);
        session()->setFlashdata('success', 'Soal berhasil dihapus.');
        return redirect()->to(base_url('admin/questions'));
    }
    public function import()
    {
        $eventId = $this->getActiveEventId();
        $isSuperAdmin = session()->get('role_name') === 'Super Admin';
        
        // Cek Keabsahan Event ID
        if ($eventId === null && !$isSuperAdmin) {
            session()->setFlashdata('error', 'Event tidak teridentifikasi. Tidak dapat mengimpor soal.');
            return redirect()->to(base_url('admin/questions'));
        }
        
        // Ambil Category ID dari form POST
        $targetCategoryId = $this->request->getPost('category_id');

        // 1. Validasi File dan Category ID
        $validationRule = [
            'excel_file' => [
                'label' => 'File Excel',
                'rules' => 'uploaded[excel_file]|max_size[excel_file,2048]|ext_in[excel_file,xls,xlsx,csv]',
                'errors' => [
                    'uploaded' => 'Anda harus memilih file untuk diupload.',
                    'max_size' => 'Ukuran file terlalu besar (maksimal 2MB).',
                    'ext_in' => 'Format file harus .xls, .xlsx, atau .csv.',
                ]
            ],
            'category_id' => [
                'label' => 'Kategori Tujuan',
                'rules' => 'required|is_natural_no_zero',
                'errors' => ['required' => 'Kategori tujuan harus dipilih.']
            ]
        ];

        if (! $this->validate($validationRule)) {
            $errors = $this->validator->getErrors();
            // Ini akan kembali ke halaman, tapi SweetAlert tidak akan muncul karena ini full redirect
            session()->setFlashdata('error', "Gagal Validasi: " . reset($errors)); 
            return redirect()->to(base_url('admin/questions'));
        }

        $file = $this->request->getFile('excel_file');

        // 2. Pindahkan File
        $newName = $file->getRandomName();
        $file->move(WRITEPATH . 'uploads', $newName);
        $filePath = WRITEPATH . 'uploads/' . $newName;

        try {
            // 3. Muat File Excel
            $spreadsheet = IOFactory::load($filePath);
            $sheet = $spreadsheet->getActiveSheet();
            $highestRow = $sheet->getHighestRow();
            $importedData = [];
            $errorRows = 0;
            $successCount = 0;

            // 4. Baca Data dari Baris Kedua (Lewati Header)
            for ($row = 2; $row <= $highestRow; $row++) {
                // Kolom A (Kategori Name) di Excel sekarang diabaikan
                $questionText = trim($sheet->getCell('B' . $row)->getValue());
                $optionA = trim($sheet->getCell('C' . $row)->getValue());
                $optionB = trim($sheet->getCell('D' . $row)->getValue());
                $optionC = trim($sheet->getCell('E' . $row)->getValue());
                $optionD = trim($sheet->getCell('F' . $row)->getValue());
                $optionE = trim($sheet->getCell('G' . $row)->getValue());
                $correctAnswer = strtoupper(trim($sheet->getCell('H' . $row)->getValue()));

                // Validasi Kunci #1: Data wajib ada 
                if (empty($questionText) || empty($optionA) || empty($correctAnswer)) {
                    $errorRows++;
                    continue; 
                }
                
                // Siapkan data untuk dimasukkan
                $data = [
                    'event_id' => $eventId, 
                    'category_id' => $targetCategoryId, // Menggunakan ID dari dropdown POST
                    'question_text' => $questionText,
                    'option_a' => $optionA,
                    'option_b' => $optionB,
                    'option_c' => $optionC,
                    'option_d' => $optionD,
                    'option_e' => $optionE,
                    'correct_answer' => $correctAnswer,
                    'created_at' => date('Y-m-d H:i:s')
                ];
                
                $importedData[] = $data;
                $successCount++;
            }

            // 5. Simpan ke Database
            if (!empty($importedData)) {
                $this->questionModel->insertBatch($importedData);
            }
            
            // 6. Bersihkan File yang Diupload
            unlink($filePath);

            $message = "Berhasil mengimpor **{$successCount}** soal!";
            if ($errorRows > 0) {
                $message .= " Terdapat {$errorRows} baris yang gagal diimpor (Data soal kosong).";
            }
            session()->setFlashdata('success', $message);

        } catch (\Exception $e) {
            // Tangani error file atau database
            session()->setFlashdata('error', 'Terjadi kesalahan saat memproses file: ' . $e->getMessage());
        }

        return redirect()->to(base_url('admin/questions'));
    }
}
