<?php 
namespace App\Models;
use CodeIgniter\Model;

class ExamQuestionModel extends Model
{
    protected $table = 'exam_questions';
    protected $primaryKey = 'id';
    protected $allowedFields = ['exam_id', 'question_id', 'user_answer', 'is_correct', 'question_order'];
}