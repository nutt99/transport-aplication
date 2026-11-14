<?php

namespace App\Models;

use CodeIgniter\Model;

class PenyediaModel extends Model
{
    protected $table            = 'penyedia';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_transport',
        'nama_penyedia',
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
        'id_transport'  => 'required|is_natural_no_zero',
        'nama_penyedia' => 'required|min_length[2]|max_length[150]',
        'latitude'      => 'permit_empty|decimal',
        'longitude'     => 'permit_empty|decimal',
    ];
    protected $validationMessages   = [
        'id_transport' => [
            'required'            => 'ID Transport wajib dipilih.',
            'is_natural_no_zero'  => 'ID Transport harus berupa angka positif.',
        ],
        'nama_penyedia' => [
            'required'   => 'Nama penyedia wajib diisi.',
            'min_length' => 'Nama penyedia minimal 2 karakter.',
            'max_length' => 'Nama penyedia maksimal 150 karakter.',
        ],
        'latitude' => [
            'decimal' => 'Format latitude harus berupa angka desimal.',
        ],
        'longitude' => [
            'decimal' => 'Format longitude harus berupa angka desimal.',
        ],
    ];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    /**
     * Ambil data penyedia beserta jenis transportasinya
     */
    public function getPenyediaWithTransport(?int $id = null)
    {
        $builder = $this->select('penyedia.*, transport.jenis as jenis_transport')
                        ->join('transport', 'transport.id = penyedia.id_transport', 'left');

        if ($id !== null) {
            return $builder->where('penyedia.id', $id)->first();
        }

        return $builder->findAll();
    }
}
