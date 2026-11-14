<?php

namespace App\Models;

use CodeIgniter\Model;

class TransportModel extends Model
{
    protected $table            = 'transport';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'jenis',
        'created_at',
        'updated_at',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'jenis' => 'required|min_length[2]|max_length[50]',
    ];
    protected $validationMessages   = [
        'jenis' => [
            'required'   => 'Jenis transportasi wajib diisi.',
            'min_length' => 'Jenis transportasi minimal 2 karakter.',
            'max_length' => 'Jenis transportasi maksimal 50 karakter.',
        ],
    ];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;
}
