<?php namespace App\Controllers\SuperAdmin;

use App\Controllers\BaseController;
use App\Models\EventModel;
use App\Models\UserModel;
use App\Models\RoleModel;

class EventController extends BaseController
{
    protected $eventModel;
    protected $userModel;
    protected $roleModel;

    public function __construct()
    {
        $this->eventModel = new EventModel();
        $this->userModel = new UserModel();
        $this->roleModel = new RoleModel();
        helper(['form', 'url', 'html']);
    }

    // Metode untuk Query Dasar Event (termasuk join Panitia)
    private function getEventsBaseQuery()
    {
        return $this->eventModel
            ->select('events.*, users.full_name as panitia_name')
            ->join('users', 'users.id = events.panitia_user_id', 'left')
            ->orderBy('events.id', 'DESC');
    }

    // --- R (Read): Tampilkan semua events (dengan Paginasi) ---
    public function index()
    {
        $data['title'] = 'Kelola Event Ujian';
        
        $baseQuery = $this->getEventsBaseQuery();
        
        // Data untuk halaman pertama (page 1)
        $data['events'] = $baseQuery->paginate(10, 'events'); // 10 baris per halaman
        $data['pager'] = $this->eventModel->pager;

        return view('superadmin/event/index', $data);
    }
    
    // --- C (Create): Tampilkan formulir ---
    public function create()
    {
        $data['title'] = 'Tambah Event Baru';
        
        // Ambil role_id untuk Panitia
        $panitiaRoleId = $this->roleModel->where('name', 'Panitia')->first()['id'];
        
        // Ambil user yang ber-role Panitia
        $data['panitia_users'] = $this->userModel
            ->where('role_id', $panitiaRoleId)
            ->findAll(); 
        
        return view('superadmin/event/create', $data);
    }

    // --- U (Update): Tampilkan form edit ---
    public function edit($id = null)
    {
        if (session()->get('role_name') !== 'Super Admin') {
            session()->setFlashdata('error', 'Akses Ditolak.');
            return redirect()->to(base_url('dashboard'));
        }
        
        $event = $this->eventModel->find($id);
        
        if (!$event) {
            session()->setFlashdata('error', 'Event tidak ditemukan.');
            return redirect()->to(base_url('superadmin/events'));
        }

        $data['event'] = $event;
        $data['title'] = 'Edit Event: ' . $event['name'];
        
        // Ambil role_id untuk Panitia
        $panitiaRoleId = $this->roleModel->where('name', 'Panitia')->first()['id'];
        
        // Ambil semua user yang ber-role Panitia
        $data['panitia_users'] = $this->userModel
            ->where('role_id', $panitiaRoleId)
            ->findAll();
        
        return view('superadmin/event/edit', $data);
    }

    // --- C (Create) & U (Update): Simpan data via AJAX JSON Response ---
    public function save()
    {
        if (!$this->request->isAJAX() || $this->request->getMethod() != 'POST') {
            return $this->response->setStatusCode(405)->setJSON(['status' => 'error', 'message' => 'Metode tidak diizinkan.']);
        }
        
        $id = $this->request->getPost('id'); // Ada jika Update

        // 1. Validasi
        if (!$this->validate([
            'name' => 'required|min_length[5]',
            'organizer' => 'required',
            'location' => 'required',
            'panitia_user_id' => 'permit_empty|is_natural',
        ])) {
            return $this->response->setJSON([
                'status' => 'error', 
                'message' => 'Validasi Gagal. Harap periksa input Anda.',
                'errors' => $this->validator->getErrors()
            ]);
        }

        // 2. Simpan Data
        $data = [
            'name' => $this->request->getPost('name'),
            'organizer' => $this->request->getPost('organizer'),
            'location' => $this->request->getPost('location'),
            'panitia_user_id' => $this->request->getPost('panitia_user_id') ?? null,
            'is_active' => $this->request->getPost('is_active') ? 1 : 0,
            'updated_at' => date('Y-m-d H:i:s'),
        ];
        
        $message = empty($id) ? 'ditambahkan' : 'diperbarui';

        // 3. Simpan/Update
        if (empty($id)) {
            $data['created_at'] = date('Y-m-d H:i:s');
            $this->eventModel->insert($data);
        } else {
            $this->eventModel->update($id, $data);
        }
        
        // 4. Respon Sukses
        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Event **' . $data['name'] . '** berhasil ' . $message . '!',
            'redirect' => base_url('superadmin/events') 
        ]);
    }
    
    // --- D (Delete): Metode Hapus via AJAX JSON Response ---
    public function delete_ajax($id = null)
    {
        if (session()->get('role_name') !== 'Super Admin') {
            return $this->response->setStatusCode(403)->setJSON(['status' => 'error', 'message' => 'Akses Ditolak.']);
        }
        
        if ($id === null || !$this->eventModel->find($id)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Event tidak ditemukan.']);
        }
        
        $eventName = $this->eventModel->find($id)['name'];
        
        // Menghapus data event
        $this->eventModel->delete($id);
        
        // Respon Sukses
        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Event **' . $eventName . '** berhasil dihapus!',
            'redirect' => base_url('superadmin/events') 
        ]);
    }
    
    // --- Metode AJAX untuk Live Search dan Paginasi ---
    public function search_ajax()
    {
        $search = $this->request->getGet('search') ?? '';
        $page = (int)($this->request->getGet('page_events') ?? 1); // Kunci: Gunakan 'page_events'

        $baseQuery = $this->getEventsBaseQuery();

        // Terapkan filter pencarian
        if (!empty($search)) {
            $baseQuery->groupStart()
                        ->like('events.name', $search)
                        ->orLike('events.organizer', $search)
                        ->orLike('users.full_name', $search)
                    ->groupEnd();
        }

        // Terapkan paginasi
        $data['events'] = $baseQuery->paginate(10, 'events', $page);
        $data['pager'] = $this->eventModel->pager;
        $data['search_query'] = $search; 

        // Mengembalikan potongan view tabel saja
        return view('superadmin/event/table_content', $data);
    }
}
