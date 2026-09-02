<?php 
namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class PanitiaFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }

        // Cek Role: Hanya Panitia dan Super Admin (jika SA diizinkan mengakses data admin)
        $role = session()->get('role_name');

        if ($role !== 'Panitia' && $role !== 'Super Admin') {
            session()->setFlashdata('error', 'Akses Ditolak. Fitur ini hanya untuk Panitia atau Admin Utama.');
            return redirect()->to(base_url('dashboard'));
        }

        // Cek Event ID (Wajib untuk Panitia)
       
        if ($role === 'Panitia' && !session()->get('event_id')) {
            session()->setFlashdata('error', 'Akses Ditolak. Akun Panitia Anda belum terikat ke Event.');
            return redirect()->to(base_url('dashboard'));
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Kosong
    }
}