<?php namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CategoryModel;
use App\Models\QuestionModel;

class CategoryController extends BaseController
{
    protected $categoryModel;
    protected $questionModel;

    public function __construct()
    {
        $this->categoryModel = new CategoryModel();
        $this->questionModel = new QuestionModel();
        helper(['form', 'url', 'html']);
    }

    private function getActiveEventId()
    {
        return session()->get('event_id');
    }

    public function index()
    {
        $data['title'] = 'Kelola Kategori Soal';
        return view('admin/category/index', $data);
    }

    public function loadCategoryIndex()
    {
        $eventId = $this->getActiveEventId();

        $categories = $this->categoryModel
            ->where('event_id', $eventId)
            ->orderBy('id', 'DESC')
            ->findAll();

        // Hitung jumlah soal per kategori
        foreach ($categories as &$cat) {
            $count = $this->questionModel
                ->where('category_id', $cat['id'])
                ->countAllResults();

            $cat['question_count'] = $count;
            $cat['has_questions']  = $count > 0;
        }

        $data['categories'] = $categories;
        return view('admin/category/table_content', $data);
    }

    public function save()
    {
        if (!$this->request->isAJAX() || $this->request->getMethod() != 'POST') {
            return $this->response->setStatusCode(405)->setJSON([
                'status' => 'error',
                'message' => 'Metode tidak diizinkan.'
            ]);
        }

        $eventId = $this->getActiveEventId();
        $id = $this->request->getPost('id');

        if (!$this->validate([
            'name' => 'required|max_length[100]',
            'required_count' => 'required|is_natural',
        ])) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Validasi gagal.',
                'errors' => $this->validator->getErrors()
            ]);
        }

        $data = [
            'name' => $this->request->getPost('name'),
            'required_count' => $this->request->getPost('required_count'),
            'event_id' => $eventId,
        ];

        $message = empty($id) ? 'ditambahkan' : 'diperbarui';

        if (empty($id)) {
            $this->categoryModel->insert($data);
        } else {
            $this->categoryModel->update($id, $data);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Kategori **' . $data['name'] . '** berhasil ' . $message . '!',
        ]);
    }

    public function delete_ajax($id = null)
    {
        $eventId = $this->getActiveEventId();
        $category = $this->categoryModel->find($id);

        if (!$category || $category['event_id'] != $eventId) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Kategori tidak ditemukan atau bukan milik event ini.'
            ]);
        }

        $categoryName = $category['name'];

        $this->questionModel->where('category_id', $id)->delete();
        $this->categoryModel->delete($id);

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Kategori **' . $categoryName . '** berhasil dihapus!',
        ]);
    }

    // 🔹 NEW: AJAX Preview Soal per Kategori
    public function previewQuestionsAjax($categoryId)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(405);
        }

        $category = $this->categoryModel->find($categoryId);
        if (!$category) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Kategori tidak ditemukan.']);
        }

        $questions = $this->questionModel
            ->where('category_id', $categoryId)
            ->orderBy('id', 'DESC')
            ->findAll();

        $html = view('admin/category/preview_modal_content', [
            'category' => $category,
            'questions' => $questions
        ]);

        return $this->response->setJSON([
            'status' => 'success',
            'html' => $html
        ]);
    }
}
