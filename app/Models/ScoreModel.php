<?php

namespace App\Models;

use CodeIgniter\Model;

class ScoreModel extends Model
{
    protected $table            = 'scores';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = ['assessment_id', 'student_id', 'score'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'assessment_id' => 'required|integer',
        'student_id'    => 'required|integer',
        'score'         => 'permit_empty|integer|greater_than_equal_to[0]|less_than_equal_to[100]',
    ];
}
