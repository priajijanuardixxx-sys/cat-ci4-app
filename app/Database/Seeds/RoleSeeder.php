<?php namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run()
    {
        // 1. Mengosongkan tabel
        $this->db->table('roles')->emptyTable();
        
        // 2. MERESET AUTO_INCREMENT KE 1 (SOLUSI MASALAH ID)
        $this->db->query("ALTER TABLE roles AUTO_INCREMENT = 1"); 
        
        $roles_data = [
            ['name' => 'Super Admin'], // ID 1
            ['name' => 'Panitia'],     // ID 2
            ['name' => 'Korektor'],    // ID 3
            ['name' => 'Peserta'],     // ID 4
        ];

        // 3. Memasukkan data baru
        $this->db->table('roles')->insertBatch($roles_data);
    }
}