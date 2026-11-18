<?php

namespace App\Models;

use CodeIgniter\Model;

class StudentModel extends Model
{
    protected $table            = 'siswa';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = ['nis', 'name', 'class_name'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'name'       => 'required|min_length[2]|max_length[80]',
        'nis'        => 'permit_empty|max_length[20]|is_unique[siswa.nis,id,{id}]',
        'class_name' => 'permit_empty|max_length[40]',
    ];

    protected $validationMessages = [
        'name' => [
            'required'   => 'Nama siswa wajib diisi.',
            'min_length' => 'Nama siswa minimal 2 karakter.',
            'max_length' => 'Nama siswa maksimal 80 karakter.',
        ],
        'nis' => [
            'max_length' => 'NIS maksimal 20 karakter.',
            'is_unique'  => 'NIS ini sudah digunakan siswa lain.',
        ],
    ];
}
