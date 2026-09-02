<?php namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\QuestionModel;
use App\Models\CategoryModel;
use Config\Services;

class AjaxController extends BaseController
{
    protected $questionModel;
    protected $categoryModel;

    public function __construct()
    {
        $this->questionModel = new QuestionModel();
        $this->categoryModel = new CategoryModel();
    }

    private function getQuestionBaseQuery()
    {
        $db = db_connect();
        $eventId = session()->get('event_id');
        $role    = session()->get('role_name');

        $query = $db->table('questions')
        ->select('questions.*, categories.name AS category_name')
        ->join('categories', 'categories.id = questions.category_id', 'left')
        ->orderBy('questions.id', 'DESC');

        if ($eventId !== null && $role === 'Panitia') {
            $query->where('questions.event_id', $eventId);
        }

        return $query;
    }


    public function loadQuestionsIndex()
    {
        $search  = $this->request->getGet('search') ?? '';
        $page    = (int) ($this->request->getGet('page_questions') ?? 1);
        $perPage = 10;

        if ($page < 1) $page = 1;

        $builder = $this->getQuestionBaseQuery();

        if (!empty($search)) {
            $builder->groupStart()
            ->like('questions.question_text', $search)
            ->orLike('categories.name', $search)
            ->groupEnd();
        }

        // clone agar count tidak mengubah $builder
        $countBuilder = clone $builder;
        $total = (int) $countBuilder->countAllResults();

        $offset = ($page - 1) * $perPage;
        if ($offset < 0) $offset = 0;

        $questions = $builder->limit($perPage, $offset)->get()->getResultArray();

        // PERBAIKAN: argumen ke-4 = template string, argumen ke-5 = segment (int)
        // Gunakan segment yang sesuai struktur URL pagination mu (default biasanya 3)
        $pager = Services::pager();
        $pagerLinks = $pager->makeLinks($page, $perPage, $total, 'default_full', 3);

        $data = [
            'questions'    => $questions,
            'pager'        => $pagerLinks, // HTML string
            'total'        => $total,
            'page'         => $page,
            'perPage'      => $perPage,
            'search_query' => $search,
        ];

        return view('admin/question/table_content', $data);
    }
}
