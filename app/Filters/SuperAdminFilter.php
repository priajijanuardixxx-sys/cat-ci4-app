<?php 
namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class SuperAdminFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Cek login (AuthFilter seharusnya sudah menangani ini)
        if (!session()->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }
        
        // Cek Role: Hanya Super Admin
        if (session()->get('role_name') !== 'Super Admin') {
            session()->setFlashdata('error', 'Akses Ditolak. Fitur ini hanya untuk Admin Utama Sistem.');
            return redirect()->to(base_url('dashboard'));
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Kosong
    }
}