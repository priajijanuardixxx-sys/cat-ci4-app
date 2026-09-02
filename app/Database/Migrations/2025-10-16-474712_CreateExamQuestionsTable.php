<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateExamQuestionsTable extends Migration
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
            'exam_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
            ],
            'question_id' => [ // ID soal dari Bank Soal
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
            ],
            'user_answer' => [ // Jawaban yang dipilih peserta
                'type'       => 'CHAR',
                'constraint' => '1',
                'null'       => true, // Null jika belum dijawab
            ],
            'is_correct' => [ // Status jawaban (digunakan untuk skor cepat)
                'type'    => 'TINYINT',
                'constraint' => 1,
                'default' => 0, // 0=Salah/Kosong, 1=Benar
            ],
            'question_order' => [ // Urutan soal ini muncul bagi peserta (untuk pengacakan)
                'type'       => 'INT',
                'constraint' => 3, 
            ],
        ]);
        $this->forge->addPrimaryKey('id');
        // Foreign Key ke tabel exams
        $this->forge->addForeignKey('exam_id', 'exams', 'id', 'CASCADE', 'CASCADE'); 
        // Foreign Key ke tabel questions
        $this->forge->addForeignKey('question_id', 'questions', 'id', 'CASCADE', 'CASCADE'); 
        $this->forge->createTable('exam_questions');
    }

    public function down()
    {
        $this->forge->dropTable('exam_questions');
    }
}