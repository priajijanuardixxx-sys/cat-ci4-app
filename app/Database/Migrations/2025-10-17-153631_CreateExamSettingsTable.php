<?php 
namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateExamSettingsTable extends Migration
{
	public function up()
	{
		$this->forge->addField([
			'id' => [
				'type'           => 'INT',
				'constraint'     => 5,
				'unsigned'       => true,
				'auto_increment' => true,
			],
			'event_id' => [
				'type'           => 'INT',
				'constraint'     => 11,
				'unsigned'       => true,
                'unique'         => true, // Kunci: Hanya satu set pengaturan per Event
            ],
            // Pengaturan Ujian CAT
            'duration_minutes' => [
            	'type'       => 'INT',
            	'constraint' => 4,
                'default'    => 120, // Default 120 menit (Pasal 6 ayat 9a)
            ],
            'passing_grade_cat' => [
            	'type'       => 'INT',
            	'constraint' => 3,
                'default'    => 60,  // Default 60 (Pasal 6 ayat 9a)
            ],
            'score_per_question' => [
            	'type'       => 'DECIMAL',
            	'constraint' => '5,4',
                'default'    => 0.00, // Dihitung otomatis: 100 / Total Soal
            ],

            // Pembobotan Uji Kemampuan (Skill Test) - Pasal 6 ayat 8
            'word_score' => [
            	'type'       => 'INT',
            	'constraint' => 2,
                'default'    => 10, // Maksimal 10
            ],
            'excel_score' => [
            	'type'       => 'INT',
            	'constraint' => 2,
                'default'    => 20, // Maksimal 20
            ],
            'ppt_score' => [
            	'type'       => 'INT',
            	'constraint' => 2,
                'default'    => 10, // Maksimal 10
            ],
            'internet_score' => [
            	'type'       => 'INT',
            	'constraint' => 2,
                'default'    => 10, // Maksimal 10
            ],
            
            // Pengaturan Jadwal
            'start_time_cat' => [
            	'type' => 'DATETIME',
            	'null' => true,
            ],
            'end_time_cat' => [
            	'type' => 'DATETIME',
            	'null' => true,
            ],
            'active_status' => [
            	'type'    => 'TINYINT',
            	'constraint' => 1,
                'default' => 0, // 0 = Draft, 1 = Aktif
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
        // Foreign Key ke tabel events
		$this->forge->addForeignKey('event_id', 'events', 'id', 'CASCADE', 'CASCADE'); 
		$this->forge->createTable('exam_settings');
	}

	public function down()
	{
		$this->forge->dropTable('exam_settings');
	}
}

