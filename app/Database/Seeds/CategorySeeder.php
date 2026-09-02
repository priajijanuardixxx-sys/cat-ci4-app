<?php namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $categories_data = [
            ['name' => 'Undang-Undang Dasar 1945', 'required_count' => 20],
            ['name' => 'UU Nomor 6 Tahun 2014 tentang Desa', 'required_count' => 20],
            ['name' => 'UU Nomor 3 Tahun 2024 tentang Desa', 'required_count' => 20],
            ['name' => 'Bahasa Indonesia', 'required_count' => 20],
            ['name' => 'Matematika', 'required_count' => 20],
            ['name' => 'Pengetahuan Umum', 'required_count' => 20],
            // 'Muatan Lokal' tidak diberi jumlah wajib karena di dokumen kosong, kita biarkan opsional
        ];

        // Memasukkan data ke tabel 'categories'
        $this->db->table('categories')->insertBatch($categories_data);
    }
}