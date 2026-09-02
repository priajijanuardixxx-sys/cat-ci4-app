<?php 
namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\RoleModel;
use App\Models\ExamTypeModel;


class PesertaController extends BaseController
{
    protected $userModel;
    protected $roleModel;
    protected $examTypeModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->roleModel = new RoleModel();
        $this->examTypeModel = new ExamTypeModel();
        helper(['form', 'url', 'html']);
    }

    private function getPesertaBaseQuery()
    {
        $eventId = session()->get('event_id');
        $pesertaRoleId = $this->roleModel->where('name', 'Peserta')->first()['id'];

        return $this->userModel
        ->select('users.*')
        ->where('event_id', $eventId)
        ->where('role_id', $pesertaRoleId)
        ->orderBy('users.id', 'DESC');
    }

    public function index()
    {
        $data['title'] = 'Kelola Akun Peserta Ujian';
        
        $baseQuery = $this->getPesertaBaseQuery();
        

        $data['users'] = $baseQuery->paginate(10, 'peserta');
        $data['pager'] = $this->userModel->pager;

        return view('admin/peserta/index', $data);
    }
    

    public function create()
    {
        $data['title'] = 'Daftarkan Peserta Ujian Baru';
        return view('admin/peserta/create', $data);
    }


    public function edit($id = null)
    {
        $userId = session()->get('user_id');
        $eventId = session()->get('event_id');
        $pesertaRoleId = $this->roleModel->where('name', 'Peserta')->first()['id'];
        
        $user = $this->userModel->find($id);


        if (!$user) {
            session()->setFlashdata('error', 'Peserta tidak ditemukan.');
            return redirect()->to(base_url('admin/peserta'));
        }
        

        if ($user['event_id'] != $eventId || $user['role_id'] != $pesertaRoleId) {
            session()->setFlashdata('error', 'Akses Ditolak: Peserta ini bukan milik Event Anda.');
            return redirect()->to(base_url('admin/peserta'));
        }

        $data['user'] = $user;
        $data['title'] = 'Edit Peserta: ' . $user['full_name'];
        
        return view('admin/peserta/edit', $data);
    }


    public function save()
    {
        if (!$this->request->isAJAX() || $this->request->getMethod() != 'POST') {
            return $this->response->setStatusCode(405)->setJSON(['status' => 'error', 'message' => 'Metode tidak diizinkan.']);
        }
        
        $eventId = session()->get('event_id');
        $pesertaRoleId = $this->roleModel->where('name', 'Peserta')->first()['id'];
        $id = $this->request->getPost('id');
        

        $validationRules = [
            'full_name' => 'required',
        ];
        
        if (empty($id)) {
            $validationRules['username'] = 'required|is_unique[users.username]|alpha_dash|min_length[4]';
            $validationRules['password'] = 'required|min_length[6]';
        } else {
            $validationRules['username'] = "required|alpha_dash|min_length[4]|is_unique[users.username,id,{$id}]";
            if (!empty($this->request->getPost('password'))) {
                $validationRules['password'] = 'min_length[6]';
            }
        }
        
        if (!$this->validate($validationRules)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Validasi Gagal.', 'errors' => $this->validator->getErrors()]);
        }


        $data = [
            'username'  => $this->request->getPost('username'),
            'full_name' => $this->request->getPost('full_name'),
            'role_id'   => $pesertaRoleId,
            'event_id'  => $eventId,
        ];

        if (!empty($this->request->getPost('password'))) {
            $data['password'] = $this->request->getPost('password');
        }

        $message = empty($id) ? 'ditambahkan' : 'diperbarui';
        
        if (empty($id)) {
            $data['created_at'] = date('Y-m-d H:i:s');
            $this->userModel->insert($data);
        } else {
            $data['updated_at'] = date('Y-m-d H:i:s');
            $this->userModel->update($id, $data);
        }
        
        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Peserta **' . $data['full_name'] . '** berhasil ' . $message . '!',
            'redirect' => base_url('admin/peserta')
        ]);
    }
    

    public function delete($id = null)
    {
        $eventId = session()->get('event_id');
        $pesertaRoleId = $this->roleModel->where('name', 'Peserta')->first()['id'];
        
        $user = $this->userModel->find($id);


        if (!$user || $user['event_id'] != $eventId || $user['role_id'] != $pesertaRoleId) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Akses Ditolak: Peserta tidak valid.']);
        }
        
        $userName = $user['full_name'];
        

        $this->userModel->delete($id);
        
        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Peserta **' . $userName . '** berhasil dihapus!',
            'redirect' => base_url('admin/peserta')
        ]);
    }

    public function search_ajax()
    {

        $search = $this->request->getGet('search') ?? '';
        $page = (int)($this->request->getGet('page_peserta') ?? 1);

        $baseQuery = $this->getPesertaBaseQuery();

        if (!empty($search)) {
            $baseQuery->groupStart()
            ->like('users.full_name', $search)
            ->orLike('users.username', $search)
            ->groupEnd();
        }

        $data['users'] = $baseQuery->paginate(10, 'peserta', $page);
        $data['pager'] = $this->userModel->pager;
        $data['search_query'] = $search; 

        return view('admin/peserta/table_content', $data);
    }

    public function listByExamType($examTypeId = null)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403, 'Forbidden');
        }

        if (!$examTypeId) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Exam type ID tidak ditemukan'
            ]);
        }

    // Ambil event_id dari exam_type
        $examType = $this->examTypeModel->find($examTypeId);
        if (!$examType) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Jenis ujian tidak ditemukan'
            ]);
        }

        $eventId = $examType['event_id'];

    // Ambil peserta berdasarkan event_id
        $participants = $this->userModel
        ->where('event_id', $eventId)
        ->where('role_id',4)
        ->findAll();

        $html = view('admin/peserta/ajax_list', ['participants' => $participants]);
        return $this->response->setJSON([
            'status' => 'success',
            'html' => $html
        ]);
    }

}
