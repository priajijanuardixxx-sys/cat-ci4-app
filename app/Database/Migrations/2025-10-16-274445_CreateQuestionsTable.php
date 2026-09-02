<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateQuestionsTable extends Migration
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
            'category_id' => [
                'type'           => 'INT',
                'constraint'     => 5,
                'unsigned'       => true,
            ],
            'question_text' => [
                'type' => 'TEXT',
            ],
            'option_a' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'option_b' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'option_c' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'option_d' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'option_e' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true, // Pilihan E bisa null jika soal hanya 4 pilihan
            ],
            'correct_answer' => [
                'type'       => 'CHAR',
                'constraint' => '1', // Menyimpan 'A', 'B', 'C', 'D', atau 'E'
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
        // Foreign Key ke tabel categories
        $this->forge->addForeignKey('category_id', 'categories', 'id', 'CASCADE', 'CASCADE'); 
        $this->forge->createTable('questions');
    }

    public function down()
    {
        $this->forge->dropTable('questions');
    }
}