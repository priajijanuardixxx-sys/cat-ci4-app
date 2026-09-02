<?php 
namespace App\Models;
use CodeIgniter\Model;

class ExamModel extends Model
{
    protected $table = 'exams';
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id', 'start_time', 'end_time', 'is_finished', 'score_cat', 'score_skill', 'score_total'];
}