<?php 
namespace App\Models;

use CodeIgniter\Model;

class ExamTypeModel extends Model
{
    protected $table = 'exam_types';
    protected $primaryKey = 'id';
    protected $allowedFields = ['event_id','name','description','is_active','created_at','updated_at'];
    protected $useTimestamps = true;
}
