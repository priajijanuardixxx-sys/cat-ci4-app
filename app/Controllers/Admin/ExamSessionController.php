<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ExamSettingModel;
use CodeIgniter\I18n\Time;

class ExamSessionController extends BaseController
{
    protected $examSettingModel;

    public function __construct()
    {
        $this->examSettingModel = new ExamSettingModel();
    }

    // 🟢 Mulai ujian
    public function start($id)
    {
        $exam = $this->examSettingModel
        ->where('id', $id)
        ->first();

        if (!$exam) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Pengaturan ujian tidak ditemukan'
            ]);
        }

        $now = Time::now('Asia/Jakarta');
        $endTime = $now->addMinutes($exam['duration']);

        $this->examSettingModel->update($exam['id'], [
            'start_time' => $now->toDateTimeString(),
            'end_time'   => $endTime->toDateTimeString(),
            'status'     => 'started'
        ]);

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Ujian dimulai',
            'start_time' => $now->toDateTimeString(),
            'end_time' => $endTime->toDateTimeString()
        ]);
    }

    // ⏸️ Pause ujian
    public function pause($settingId)
    {
        $exam = $this->examSettingModel->find($settingId);
        if (!$exam) return $this->failNotFound('Exam not found');

        $now = Time::now('Asia/Jakarta');
        $remainingSeconds = 0;
        if ($exam['end_time']) {
            $end = new Time($exam['end_time']);
            $remainingSeconds = max(0, $end->getTimestamp() - $now->getTimestamp());
        }

        $this->examSettingModel->update($settingId, [
            'status' => 'paused',
            'is_paused' => 1,
            'last_paused_at' => $now->toDateTimeString(),
            'paused_seconds' => $remainingSeconds // bisa tambah kolom paused_seconds
        ]);

        return $this->response->setJSON(['status' => 'success', 'message' => 'Ujian dijeda']);
    }

    // ▶️ Lanjutkan ujian
    // Resume ujian
    public function resume($settingId)
    {
        $exam = $this->examSettingModel->find($settingId);
        if (!$exam) return $this->failNotFound('Exam not found');

        $now = Time::now('Asia/Jakarta');
        $remainingSeconds = $exam['paused_seconds'] ?? ($exam['duration'] * 60);

        $newEnd = $now->addSeconds($remainingSeconds);

        $this->examSettingModel->update($settingId, [
            'status' => 'started',
            'is_paused' => 0,
            'end_time' => $newEnd->toDateTimeString(),
            'last_paused_at' => null
        ]);

        return $this->response->setJSON(['status' => 'success', 'message' => 'Ujian dilanjutkan']);
    }


   // 🔁 Cek status ujian (AJAX)
    public function status($settingId)
    {
        $exam = $this->examSettingModel->find($settingId);
        if (!$exam) return $this->failNotFound('Exam not found');

        $now = Time::now('Asia/Jakarta');
        $status = $exam['status'];
        $remainingSeconds = 0;

        if ($status === 'started' && $exam['end_time']) {
            $end = new Time($exam['end_time']);
            $remainingSeconds = max(0, $end->getTimestamp() - $now->getTimestamp());
            if ($remainingSeconds <= 0) {
                $this->examSettingModel->update($settingId, ['status' => 'finished']);
                $status = 'finished';
                $remainingSeconds = 0;
            }
        } elseif ($status === 'paused') {
            $remainingSeconds = $exam['paused_seconds'] ?? ($exam['duration'] * 60);
        } elseif ($status === 'scheduled') {
            $remainingSeconds = $exam['duration'] * 60;
        }

        return $this->response->setJSON([
            'status' => 'success',
            'exam_status' => $status,
            'remaining_seconds' => $remainingSeconds,
            'exam_type_id' => $exam['exam_type_id'] 
        ]);
    }

    public function reset($settingId)
    {
    // Cek metode POST
        if ($this->request->getMethod() !== 'POST') {
            return $this->response
            ->setStatusCode(405)
            ->setJSON(['status' => 'error', 'message' => 'Metode tidak diizinkan']);
        }

    // Ambil setting
        $exam = $this->examSettingModel->find($settingId);
        if (!$exam) {
            return $this->response->setStatusCode(404)
            ->setJSON(['status' => 'error', 'message' => 'Data ujian tidak ditemukan']);
        }

    // Reset waktu
        $data = [
            'status' => 'scheduled',
            'start_time' => null,
            'end_time' => null,
            'paused_at' => null,
        ];
        $this->examSettingModel->update($settingId, $data);

        return $this->response
        ->setJSON(['status' => 'success', 'message' => 'Waktu ujian telah di-reset']);
    }


}
