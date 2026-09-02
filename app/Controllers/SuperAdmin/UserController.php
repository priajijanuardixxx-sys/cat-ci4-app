<?php namespace App\Controllers\SuperAdmin;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\RoleModel;
use App\Models\EventModel;

class UserController extends BaseController
{
    protected $userModel;
    protected $roleModel;
    protected $eventModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->roleModel = new RoleModel();
        $this->eventModel = new EventModel();
        helper(['form', 'url', 'html']);
    }

    // Metode untuk mengambil user dengan filter role Panitia dan Korektor
    private function getUsersBaseQuery()
    {
        $panitiaRoleId = $this->roleModel->where('name', 'Panitia')->first()['id'];
        $korektorRoleId = $this->roleModel->where('name', 'Korektor')->first()['id'];

        return $this->userModel
            ->select('users.*, roles.name as role_name, events.name as event_name')
            ->join('roles', 'roles.id = users.role_id')
            ->join('events', 'events.id = users.event_id', 'left')
            ->whereIn('users.role_id', [$panitiaRoleId, $korektorRoleId])
            ->orderBy('users.id', 'DESC');
    }

    // --- R (Read): Tampilkan semua Panitia dan Korektor (dengan Paginasi) ---
    public function index()
    {
        $data['title'] = 'Kelola Akun Panitia & Korektor';
        
        $baseQuery = $this->getUsersBaseQuery();
        
        // Data untuk halaman pertama (page 1)
        // Gunakan BaseQuery untuk paginate
        $data['users'] = $baseQuery->paginate(10, 'users'); // 10 baris per halaman
        $data['pager'] = $this->userModel->pager;

        return view('superadmin/user/index', $data);
    }
    
    // --- C (Create): Tampilkan form pendaftaran ---
    public function create()
    {
        $data['title'] = 'Daftarkan Akun Baru';
        
        // Hanya sediakan pilihan role Panitia, Korektor
        $data['roles'] = $this->roleModel->whereIn('name', ['Panitia', 'Korektor'])->findAll();
        $data['events'] = $this->eventModel->findAll();
        
        return view('superadmin/user/create', $data);
    }

    // --- U (Update): Tampilkan form edit ---
    public function edit($id = null)
    {
        $user = $this->userModel->find($id);

        if (!$user) {
            session()->setFlashdata('error', 'Akun tidak ditemukan.');
            return redirect()->to(base_url('superadmin/users'));
        }

        $data['user'] = $user;
        $data['title'] = 'Edit Akun: ' . $user['full_name'];
        $data['roles'] = $this->roleModel->whereIn('name', ['Panitia', 'Korektor'])->findAll();
        $data['events'] = $this->eventModel->findAll();

        return view('superadmin/user/edit', $data);
    }

    // --- C (Create) & U (Update): Simpan data via AJAX JSON Response ---
    public function save()
    {
        // PERBAIKAN PENTING: Gunakan 'POST' huruf kapital
        if (!$this->request->isAJAX() || $this->request->getMethod() != 'POST') {
            return $this->response->setStatusCode(405)->setJSON(['status' => 'error', 'message' => 'Metode tidak diizinkan.']);
        }
        
        $id = $this->request->getPost('id'); // Ada jika Update

        // 1. Validasi
        $validationRules = [
            'full_name' => 'required',
            'role_id'   => 'required|is_natural_no_zero',
            'event_id'  => 'permit_empty|is_natural',
        ];
        
        // Aturan validasi khusus
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

        // 2. Siapkan Data
        $roleId = $this->request->getPost('role_id');
        $roleName = $this->roleModel->find($roleId)['name'];
        $password = $this->request->getPost('password');
        
        $data = [
            'username'  => $this->request->getPost('username'),
            'full_name' => $this->request->getPost('full_name'),
            'role_id'   => $roleId,
            // Tugaskan ke Event jika Panitia/Korektor dan event_id dikirim
            'event_id'  => (!empty($this->request->getPost('event_id')) && ($roleName == 'Panitia' || $roleName == 'Korektor'))
                           ? $this->request->getPost('event_id') : null,
        ];

        // Tambahkan password hanya jika ada input baru
        if (!empty($password)) {
            $data['password'] = $password; // Model akan otomatis hash
        }

        // 3. Simpan/Update
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
            'message' => 'Akun **' . $data['full_name'] . '** berhasil ' . $message . '!',
            'redirect' => base_url('superadmin/users')
        ]);
    }
    
    // --- D (Delete): Hapus akun via AJAX ---
    public function delete_ajax($id = null)
    {
        if (session()->get('role_name') !== 'Super Admin') {
            return $this->response->setStatusCode(403)->setJSON(['status' => 'error', 'message' => 'Akses Ditolak.']);
        }
        
        $user = $this->userModel->find($id);

        if ($id === null || !$user) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Akun tidak ditemukan.']);
        }
        
        if ($user['username'] === session()->get('username')) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Anda tidak bisa menghapus akun yang sedang digunakan.']);
        }

        $userName = $user['full_name'];
        
        $this->userModel->delete($id);
        
        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Akun **' . $userName . '** berhasil dihapus!',
            'redirect' => base_url('superadmin/users') 
        ]);
    }

    // --- Metode AJAX untuk Live Search dan Paginasi ---
    public function search_ajax()
    {
        // Ambil parameter dari request
        $search = $this->request->getGet('search') ?? '';
        
        // PERBAIKAN KRUSIAL: Konversi nilai halaman ke integer
        $page = (int)($this->request->getGet('page_users') ?? 1); 

        $baseQuery = $this->getUsersBaseQuery();

        // Terapkan filter pencarian
        if (!empty($search)) {
            $baseQuery->groupStart()
                        ->like('users.full_name', $search)
                        ->orLike('users.username', $search)
                        ->orLike('roles.name', $search)
                        ->orLike('events.name', $search)
                    ->groupEnd();
        }

        // Terapkan paginasi
        // Masalah teratasi karena $page sudah menjadi integer
        $data['users'] = $baseQuery->paginate(10, 'users', $page);
        $data['pager'] = $this->userModel->pager;
        $data['search_query'] = $search; 

        // Mengembalikan potongan view tabel saja
        return view('superadmin/user/table_content', $data);
    }
}
