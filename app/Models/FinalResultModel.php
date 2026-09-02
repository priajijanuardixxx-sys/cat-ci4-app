<?php 
namespace App\Models;

use CodeIgniter\Model;

class FinalResultModel extends Model
{
    protected $table = 'final_results';
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id','event_id','final_score','status','created_at'];
    protected $useTimestamps = false;
}
