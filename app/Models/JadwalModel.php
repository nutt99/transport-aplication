<?php

namespace App\Models;

use CodeIgniter\Model;

class JadwalModel extends Model
{
    protected $table            = 'jadwal';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_penyedia',
        'id_kota_asal',
        'id_kota_tujuan',
        'start_hours',
        'ended_hours',
        'tarif_per_km',
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
        'id_penyedia'    => 'required|is_natural_no_zero',
        'id_kota_asal'   => 'permit_empty|is_natural_no_zero',
        'id_kota_tujuan' => 'permit_empty|is_natural_no_zero',
        'start_hours'    => 'required',
        'ended_hours'    => 'required',
        'tarif_per_km'   => 'required|numeric',
    ];
    protected $validationMessages   = [
        'id_penyedia' => [
            'required'           => 'ID Penyedia wajib dipilih.',
            'is_natural_no_zero' => 'ID Penyedia harus berupa angka positif.',
        ],
        'start_hours' => [
            'required' => 'Jam berangkat (start_hours) wajib diisi.',
        ],
        'ended_hours' => [
            'required' => 'Jam tiba (ended_hours) wajib diisi.',
        ],
        'tarif_per_km' => [
            'required' => 'Tarif per km wajib diisi.',
            'numeric'  => 'Tarif per km harus berupa angka.',
        ],
    ];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    /**
     * Ambil data jadwal lengkap dengan join penyedia, transport, kota asal, dan kota tujuan
     */
    public function getJadwalDetail(?int $id = null)
    {
        $builder = $this->select('
                jadwal.*,
                penyedia.nama_penyedia,
                transport.id as id_transport,
                transport.jenis as jenis_transport,
                kota_asal.nama_kota as nama_kota_asal,
                kota_asal.latitude as lat_kota_asal,
                kota_asal.longitude as lng_kota_asal,
                kota_tujuan.nama_kota as nama_kota_tujuan,
                kota_tujuan.latitude as lat_kota_tujuan,
                kota_tujuan.longitude as lng_kota_tujuan
            ')
            ->join('penyedia', 'penyedia.id = jadwal.id_penyedia', 'left')
            ->join('transport', 'transport.id = penyedia.id_transport', 'left')
            ->join('kota as kota_asal', 'kota_asal.id = jadwal.id_kota_asal', 'left')
            ->join('kota as kota_tujuan', 'kota_tujuan.id = jadwal.id_kota_tujuan', 'left');

        if ($id !== null) {
            return $builder->where('jadwal.id', $id)->first();
        }

        return $builder->findAll();
    }

    /**
     * Cari jadwal berdasarkan kota asal dan tujuan
     */
    public function cariJadwal(?int $idKotaAsal = null, ?int $idKotaTujuan = null)
    {
        $builder = $this->select('
                jadwal.*,
                penyedia.nama_penyedia,
                transport.id as id_transport,
                transport.jenis as jenis_transport,
                kota_asal.nama_kota as nama_kota_asal,
                kota_tujuan.nama_kota as nama_kota_tujuan
            ')
            ->join('penyedia', 'penyedia.id = jadwal.id_penyedia', 'left')
            ->join('transport', 'transport.id = penyedia.id_transport', 'left')
            ->join('kota as kota_asal', 'kota_asal.id = jadwal.id_kota_asal', 'left')
            ->join('kota as kota_tujuan', 'kota_tujuan.id = jadwal.id_kota_tujuan', 'left');

        if ($idKotaAsal !== null) {
            $builder->where('jadwal.id_kota_asal', $idKotaAsal);
        }

        if ($idKotaTujuan !== null) {
            $builder->where('jadwal.id_kota_tujuan', $idKotaTujuan);
        }

        return $builder->findAll();
    }
}
