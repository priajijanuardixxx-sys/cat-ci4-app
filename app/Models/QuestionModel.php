<?php 
namespace App\Models;
use CodeIgniter\Model;

class QuestionModel extends Model
{
	protected $table = 'questions';
	protected $primaryKey = 'id';
	protected $useTimestamps = true;
	protected $allowedFields = ['category_id', 'question_text', 'option_a', 'option_b', 'option_c', 'option_d', 'option_e', 'correct_answer','event_id'];

    /**
     * Ambil semua soal berdasarkan exam_id
     * @param int $examId
     * @return array
     */
    
    public function getQuestionsByExam($settingId)
    {
        $builder = $this->db->table('exam_setting_categories esc')
        // *** PERBAIKAN: Tambahkan alias category_id sebagai exam_type_id ***
        ->select('q.*, q.category_id AS exam_type_id') 
        // *****************************************************************
        ->join('questions q', 'q.category_id = esc.category_id', 'inner')
        ->where('esc.exam_setting_id', $settingId);

        $query = $builder->get();
        return $query->getResultArray();
    }

    public function getQuestionsByIds(array $ids)
    {
        if (empty($ids)) return [];
        $builder = $this->db->table('questions')->whereIn('id', $ids);
        $query = $builder->get();
        $questions = $query->getResultArray();

    // Urutkan sesuai urutan di $ids
        $ordered = [];
        $map = [];
        foreach ($questions as $q) $map[$q['id']] = $q;
        foreach ($ids as $id) {
            if (isset($map[$id])) $ordered[] = $map[$id];
        }
        return $ordered;
    }


}