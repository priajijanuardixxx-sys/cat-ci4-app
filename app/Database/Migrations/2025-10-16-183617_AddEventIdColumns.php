<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddEventIdColumns extends Migration
{
    public function up()
    {
        // --- 1. Modifikasi tabel users ---
        $fields_users = [
            'event_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'null'           => true, // Super Admin tidak terikat event
                'after'          => 'role_id', 
            ],
        ];
        $this->forge->addColumn('users', $fields_users);

        // --- 2. Modifikasi tabel categories ---
        $fields_categories = [
            'event_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'after'          => 'id',
            ],
        ];
        $this->forge->addColumn('categories', $fields_categories);

        // --- 3. Modifikasi tabel questions ---
        $fields_questions = [
            'event_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'after'          => 'category_id',
            ],
        ];
        $this->forge->addColumn('questions', $fields_questions);
        
        // --- 4. Modifikasi tabel exams ---
        $fields_exams = [
            'event_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'after'          => 'user_id',
            ],
        ];
        $this->forge->addColumn('exams', $fields_exams);

        // Catatan: Menambahkan Foreign Key harus dilakukan setelah semua tabel ada. 
        // Untuk saat ini, kita biarkan tanpa FK di kode Migration ini untuk menghindari error urutan.
    }

    public function down()
    {
        // --- 1. Drop columns from users ---
        $this->forge->dropColumn('users', 'event_id');

        // --- 2. Drop columns from categories ---
        $this->forge->dropColumn('categories', 'event_id');

        // --- 3. Drop columns from questions ---
        $this->forge->dropColumn('questions', 'event_id');

        // --- 4. Drop columns from exams ---
        $this->forge->dropColumn('exams', 'event_id');
    }
}