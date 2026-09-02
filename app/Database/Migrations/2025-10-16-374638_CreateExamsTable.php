<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateExamsTable extends Migration
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
            'user_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
            ],
            'start_time' => [
                'type' => 'DATETIME',
            ],
            'end_time' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'is_finished' => [
                'type'    => 'TINYINT',
                'constraint' => 1,
                'default' => 0, // 0 = In Progress, 1 = Finished
            ],
            'score_cat' => [ // Skor Ujian Tertulis CAT (max 100)
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'default'    => 0,
            ],
            'score_skill' => [ // Skor Uji Kemampuan Komputer (max 50, input manual)
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'default'    => 0,
            ],
            'score_total' => [ // Skor Total (CAT + Skill, max 150)
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'default'    => 0,
            ],
        ]);
        $this->forge->addPrimaryKey('id');
        // Foreign Key ke tabel users
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE'); 
        $this->forge->createTable('exams');
    }

    public function down()
    {
        $this->forge->dropTable('exams');
    }
}