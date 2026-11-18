<?php

namespace App\Models;

use CodeIgniter\Model;

class AssessmentModel extends Model
{
    protected $table            = 'assessments';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = ['title', 'type', 'assessment_date'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'title'           => 'required|min_length[3]|max_length[120]',
        'type'            => 'required|in_list[tugas,formatif,sumatif]',
        'assessment_date' => 'required|valid_date[Y-m-d]',
    ];

    protected $validationMessages = [
        'title' => [
            'required'   => 'Judul asesmen wajib diisi.',
            'min_length' => 'Judul asesmen minimal 3 karakter.',
            'max_length' => 'Judul asesmen maksimal 120 karakter.',
        ],
    ];
}
