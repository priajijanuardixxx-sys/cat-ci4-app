<?php

namespace App\Controllers\Participant;

use App\Controllers\BaseController;
use App\Models\ExamTypeModel;
use App\Models\ExamSettingModel;

class Dashboard extends BaseController
{
    protected $examTypeModel;
    protected $examSettingModel;

    public function __construct()
    {
        $this->examTypeModel = new ExamTypeModel();
        $this->examSettingModel = new ExamSettingModel();
    }

    public function index()
    {
        // Ambil data session
        $full_name  = session()->get('full_name');
        $event_name = session()->get('event_name');
        $event_id   = session()->get('event_id');

        // Hitung total ujian untuk event peserta
        $totalExams = $this->examTypeModel
        ->where('event_id', $event_id)
        ->countAllResults();

        $data = [
            'title'       => 'Dashboard Peserta',
            'full_name'   => $full_name,
            'event_name'  => $event_name,
            'username'    => session()->get('username'),
            'role'        => session()->get('role_name'),
            'total_exams' => $totalExams, // total ujian
        ];

        return view('participant/dashboard', $data);
    }

    public function schedule()
    {
        $event_id = session()->get('event_id');

    // Ambil semua ujian untuk event peserta
        $exams = $this->examTypeModel
        ->select('exam_types.id, exam_types.name, exam_types.description, exam_settings.start_time, exam_settings.end_time, exam_settings.duration, exam_settings.status')
        ->join('exam_settings', 'exam_settings.exam_type_id = exam_types.id', 'left')
        ->where('exam_types.event_id', $event_id)
        ->findAll();

        $data = [
            'title' => 'Jadwal Ujian',
            'schedules' => $exams
        ];

        return view('participant/schedule', $data);
    }

    

}
