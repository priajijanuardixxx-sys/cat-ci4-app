<?php 
namespace App\Models;

use CodeIgniter\Model;

class ExamSettingCategoryModel extends Model
{
    protected $table = 'exam_setting_categories';
    protected $primaryKey = 'id';
    protected $allowedFields = ['exam_setting_id','category_id','question_count','created_at'];
    protected $useTimestamps = false;
}
