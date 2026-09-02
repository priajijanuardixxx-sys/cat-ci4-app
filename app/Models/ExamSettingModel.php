<?php

namespace App\Models;

use CodeIgniter\Model;

class ExamSettingModel extends Model
{
    protected $table = 'exam_settings';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'exam_type_id',
        'duration',
        'passing_grade',
        'start_time',
        'end_time',
        'mode',
        'randomize_questions',
        'show_result',
        'status',
        'is_paused',          // 1 = dijeda, 0 = tidak
        'last_paused_at',     // datetime terakhir pause
        'paused_seconds',     // sisa detik saat pause
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
}
