<?php namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use App\Models\EventModel;

class UserSeeder extends Seeder
{
    public function run()
    {
        $roleModel = new \App\Models\RoleModel();
        
        // --- PENGAMBILAN ID ROLE YANG DINAMIS ---
        $superAdmin = $roleModel->where('name', 'Super Admin')->first();
        $panitia    = $roleModel->where('name', 'Panitia')->first();
        $peserta    = $roleModel->where('name', 'Peserta')->first();

        // Cek darurat (untuk memastikan ID tidak NULL)
        if (!$superAdmin || !$panitia || !$peserta) {
            echo "Kesalahan: Roles 'Super Admin', 'Panitia', atau 'Peserta' tidak ditemukan. Jalankan RoleSeeder terlebih dahulu.\n";
            return;
        }

        // Password default: '123456'
        $defaultPassword = password_hash('123456', PASSWORD_DEFAULT);
        $eventModel = new EventModel(); 

        // --- Event Contoh ---
        $eventData = [
            'name'      => 'P3D Desa Binangun 2025',
            'organizer' => 'Panitia P3D Binangun',
            'location'  => 'Kecamatan Banyumas',
            'is_active' => 1,
            'created_at'=> date('Y-m-d H:i:s'),
            'updated_at'=> date('Y-m-d H:i:s'),
        ];
        
        // Hapus data users dan events lama (untuk bersih-bersih)
        $this->db->table('users')->emptyTable();
        $this->db->table('events')->emptyTable();
        
        // Masukkan Event Baru
        $eventModel->insert($eventData);
        $eventId = $eventModel->getInsertID(); 

        $users_data = [
            // AKUN SUPER ADMIN
            [
                'role_id'   => $superAdmin['id'], // ID unik Super Admin (misalnya 1)
                'username'  => 'superadmin',
                'password'  => $defaultPassword,
                'full_name' => 'Admin Utama Sistem CAT',
                'event_id'  => null,
                'created_at'=> date('Y-m-d H:i:s'),
                'updated_at'=> date('Y-m-d H:i:s'),
            ],
            // AKUN PANITIA
            [
                'role_id'   => $panitia['id'], // ID unik Panitia (misalnya 2)
                'username'  => 'panitia',
                'password'  => $defaultPassword,
                'full_name' => 'Bambang Edi Sunarto (Panitia Binangun)',
                'event_id'  => $eventId,
                'created_at'=> date('Y-m-d H:i:s'),
                'updated_at'=> date('Y-m-d H:i:s'),
            ],
            // AKUN PESERTA
            [
                'role_id'   => $peserta['id'], // ID unik Peserta (misalnya 4)
                'username'  => 'peserta01',
                'password'  => $defaultPassword,
                'full_name' => 'Calon Perangkat Desa 001',
                'event_id'  => $eventId,
                'created_at'=> date('Y-m-d H:i:s'),
                'updated_at'=> date('Y-m-d H:i:s'),
            ],
        ];

        // Memasukkan data ke tabel 'users'
        $this->db->table('users')->insertBatch($users_data);
        
        // Update Event dengan panitia_user_id yang benar
        $panitiaUser = $this->db->table('users')->where('username', 'panitia')->get()->getRowArray();
        if ($panitiaUser) {
            $eventModel->update($eventId, ['panitia_user_id' => $panitiaUser['id']]);
        }
    }
}