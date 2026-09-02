<?php 
namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\RoleModel;
use App\Models\EventModel;

class Auth extends BaseController
{
    public function __construct()
    {
        // Memuat helper form dan url di BaseController
        helper(['form', 'url']);
    }

    public function index()
    {
        // Redirect jika sudah login
        if (session()->get('isLoggedIn')) {
            return redirect()->to(base_url('/dashboard'));
        }
        
        $data['title'] = 'Login Aplikasi CAT';
        return view('auth/login', $data);
    }

    public function attemptLogin()
    {
        $session = session();
        $userModel = new UserModel();
        $roleModel = new RoleModel();
        $eventModel = new EventModel(); 
        
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        $user = $userModel->where('username', $username)->first();

        // 1. Cek Ketersediaan User
        if (!$user) {
            $session->setFlashdata('error', 'Username tidak ditemukan.');
            return redirect()->back()->withInput();
        }

        // 2. Cek Password
        if (!password_verify((string)$password, $user['password'])) {
            $session->setFlashdata('error', 'Password salah.');
            return redirect()->back()->withInput();
        }
        
        // 3. Login Sukses: Ambil Role, Ambil Event, dan Set Sesi
        $role = $roleModel->find($user['role_id']);
        
        $event = null;
        if ($user['event_id']) {
            $event = $eventModel->find($user['event_id']);
        }
        
        $ses_data = [
            'user_id'    => $user['id'],
            'username'   => $user['username'],
            'full_name'  => $user['full_name'],
            'role_id'    => $user['role_id'],
            'role_name'  => $role['name'], // 'Super Admin', 'Panitia', 'Korektor', atau 'Peserta'
            'isLoggedIn' => TRUE,
            
            // --- DATA MULTI-EVENT KRUSIAL ---
            'event_id'   => $user['event_id'] ?? null,
            'event_name' => $event ? $event['name'] : 'GLOBAL',
        ];
        
        $session->set($ses_data);
        
        // --- 4. LOGIKA REDIRECT BERDASARKAN ROLE ---
        if ($user['role_id'] == 4) {
             // Redirect Peserta ke Dashboard Khusus
             return redirect()->to(base_url('participant/dashboard'));
        }
        // Redirect Admin/Panitia/Korektor ke Dashboard Admin
        return redirect()->to(base_url('/dashboard'));
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to(base_url('/login'));
    }
}
