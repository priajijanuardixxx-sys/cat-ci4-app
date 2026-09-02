<?php

namespace App\Models;

use CodeIgniter\Model;

class ExamSessionModel extends Model
{
    protected $table = 'exam_sessions';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'user_id', 'exam_setting_id', 'question_order', 'answers',
        'current_index', 'status', 'started_at', 'paused_at', 'finished_at','score'
    ];
    protected $useTimestamps = true;
    protected $updatedField = 'updated_at';
}
