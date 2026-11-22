<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use App\Models\JadwalModel;
use App\Models\PenyediaModel;
use App\Models\LokasiModel;
use CodeIgniter\Database\Exceptions\DatabaseException;
use CodeIgniter\Database\Exceptions\DataException;

class Jadwal extends ResourceController
{
    protected $modelName = JadwalModel::class;
    protected $format    = 'json';

    // GET /api/jadwal
    public function index()
    {
        return $this->respond([
            'status' => 'success',
            'data'   => $this->model->getJadwalDetail(),
        ]);
    }

    // GET /api/jadwal/(:id)
    public function show($id = null)
    {
        $data = $this->model->getJadwalDetail((int)$id);
        if (!$data) {
            return $this->failNotFound('Data jadwal tidak ditemukan.');
        }

        return $this->respond([
            'status' => 'success',
            'data'   => $data,
        ]);
    }

    // POST /api/jadwal
    public function create()
    {
        $input = $this->request->getJSON(true) ?: $this->request->getPost();
        $data  = array_intersect_key($input ?? [], array_flip([
            'id_penyedia', 'id_lokasi_asal', 'id_lokasi_tujuan', 'start_hours', 'ended_hours', 'tarif_per_km',
        ]));

        if (empty($data)) {
            return $this->failValidationErrors('Body request tidak boleh kosong.');
        }

        $relasiError = $this->validasiRelasi($data);
        if (!empty($relasiError)) {
            return $this->failValidationErrors($relasiError);
        }

        $id = $this->model->insert($data);
        if ($id === false) {
            return $this->failValidationErrors($this->model->errors());
        }

        return $this->respondCreated([
            'status'  => 'success',
            'message' => 'Data jadwal berhasil ditambahkan.',
            'data'    => $this->model->getJadwalDetail((int)$id),
        ]);
    }

    // PUT / PATCH /api/jadwal/(:id)
    public function update($id = null)
    {
        $existing = $this->model->find($id);
        if (!$existing) {
            return $this->failNotFound('Data jadwal tidak ditemukan.');
        }

        $input = $this->request->getJSON(true) ?: $this->request->getRawInput();
        $data  = array_intersect_key($input ?? [], array_flip([
            'id_penyedia', 'id_lokasi_asal', 'id_lokasi_tujuan', 'start_hours', 'ended_hours', 'tarif_per_km',
        ]));

        if (empty($data)) {
            return $this->failValidationErrors('Tidak ada field valid yang dikirim untuk diperbarui.');
        }

        $relasiError = $this->validasiRelasi($data, $existing);
        if (!empty($relasiError)) {
            return $this->failValidationErrors($relasiError);
        }

        try {
            if ($this->model->update($id, $data) === false) {
                return $this->failValidationErrors($this->model->errors());
            }
        } catch (DataException $e) {
            // Tidak ada perubahan nilai dari data sebelumnya
        }

        return $this->respond([
            'status'  => 'success',
            'message' => 'Data jadwal berhasil diperbarui.',
            'data'    => $this->model->getJadwalDetail((int)$id),
        ]);
    }

    // DELETE /api/jadwal/(:id)
    public function delete($id = null)
    {
        if (!$this->model->find($id)) {
            return $this->failNotFound('Data jadwal tidak ditemukan.');
        }

        try {
            $this->model->delete($id);
        } catch (DatabaseException $e) {
            return $this->fail('Gagal menghapus data jadwal.', 409);
        }

        return $this->respondDeleted([
            'status'  => 'success',
            'message' => 'Data jadwal berhasil dihapus.',
        ]);
    }

    /**
     * Validasi keberadaan ID relasi dan pastikan asal != tujuan
     */
    private function validasiRelasi(array $data, ?array $existing = null): array
    {
        $errors = [];

        if (!empty($data['id_penyedia'])) {
            $penyediaModel = new PenyediaModel();
            if (!$penyediaModel->find($data['id_penyedia'])) {
                $errors['id_penyedia'] = 'ID Penyedia tidak ditemukan di database.';
            }
        }

        $lokasiModel = new LokasiModel();
        if (!empty($data['id_lokasi_asal']) && !$lokasiModel->find($data['id_lokasi_asal'])) {
            $errors['id_lokasi_asal'] = 'ID Lokasi asal tidak ditemukan di database.';
        }

        if (!empty($data['id_lokasi_tujuan']) && !$lokasiModel->find($data['id_lokasi_tujuan'])) {
            $errors['id_lokasi_tujuan'] = 'ID Lokasi tujuan tidak ditemukan di database.';
        }

        $asal   = $data['id_lokasi_asal']   ?? ($existing['id_lokasi_asal']   ?? null);
        $tujuan = $data['id_lokasi_tujuan'] ?? ($existing['id_lokasi_tujuan'] ?? null);

        if (!empty($asal) && !empty($tujuan) && (int)$asal === (int)$tujuan) {
            $errors['id_lokasi_tujuan'] = 'Lokasi asal dan lokasi tujuan tidak boleh sama.';
        }

        return $errors;
    }
}