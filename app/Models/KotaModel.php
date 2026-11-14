<?php

namespace App\Models;

use CodeIgniter\Model;

class KotaModel extends Model
{
    protected $table            = 'kota';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'nama_kota',
        'latitude',
        'longitude',
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
        'nama_kota' => 'required|min_length[2]|max_length[100]',
        'latitude'  => 'required|decimal',
        'longitude' => 'required|decimal',
    ];
    protected $validationMessages   = [
        'nama_kota' => [
            'required'   => 'Nama kota wajib diisi.',
            'min_length' => 'Nama kota minimal 2 karakter.',
            'max_length' => 'Nama kota maksimal 100 karakter.',
        ],
        'latitude' => [
            'required' => 'Latitude wajib diisi.',
            'decimal'  => 'Format latitude harus berupa angka desimal.',
        ],
        'longitude' => [
            'required' => 'Longitude wajib diisi.',
            'decimal'  => 'Format longitude harus berupa angka desimal.',
        ],
    ];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;
}
