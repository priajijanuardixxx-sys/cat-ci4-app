<?php 
namespace App\Controllers;

use App\Controllers\BaseController;

class Dashboard extends BaseController
{
    public function index()
    {
        $role = session()->get('role_name');

        $data['title'] = 'Dashboard ' . $role;
        $data['role'] = $role;
       
        $data['event_name'] = session()->get('event_name');
        if ($role === 'Super Admin' || $role === 'Panitia' || $role === 'Korektor') {
            if ($role === 'panitia' && !session()->get('event_id')) {
                 $data['title'] = 'Akses Terbatas';
                 return view('dashboard/no_event_assigned', $data);
            }
            return view('dashboard/home_content', $data); 
            
        } else if ($role === 'Peserta') {
            return view('dashboard/peserta_dashboard', $data);
            
        } else {
            return redirect()->to(base_url('logout'));
        }
    }
}
