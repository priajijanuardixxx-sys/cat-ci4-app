<?php 
namespace App\Models;

use CodeIgniter\Model;

class ExamResultModel extends Model
{
    protected $table = 'exam_results';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'user_id','exam_type_id','score','status','started_at','finished_at','created_at'
    ];
    protected $useTimestamps = false;
}
