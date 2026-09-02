<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateEventsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'organizer' => [ // Siapa yang menyelenggarakan (informasi umum)
                'type'       => 'VARCHAR',
                'constraint' => '150',
            ],
            'location' => [ // Tempat diselenggarakan
                'type'       => 'VARCHAR',
                'constraint' => '150',
            ],
            'panitia_user_id' => [ // User ID Panitia Utama yang ditunjuk (FK ke users)
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'null'           => true,
            ],
            'is_active' => [
                'type'    => 'TINYINT',
                'constraint' => 1,
                'default' => 1, // Default aktif
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addPrimaryKey('id');
        // Foreign Key ke users.id (akan ditambahkan di migration users)
        $this->forge->createTable('events');
    }

    public function down()
    {
        $this->forge->dropTable('events');
    }
}