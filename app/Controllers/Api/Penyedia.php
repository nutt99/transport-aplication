<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use App\Models\PenyediaModel;
use App\Models\TransportModel;
use CodeIgniter\Database\Exceptions\DatabaseException;
use CodeIgniter\Database\Exceptions\DataException;

class Penyedia extends ResourceController
{
    protected $modelName = PenyediaModel::class;
    protected $format    = 'json';

    // GET /api/penyedia
    public function index()
    {
        return $this->respond([
            'status' => 'success',
            'data'   => $this->model->getPenyediaWithTransport(),
        ]);
    }

    // GET /api/penyedia/(:id)
    public function show($id = null)
    {
        $data = $this->model->getPenyediaWithTransport((int)$id);
        if (!$data) {
            return $this->failNotFound('Data penyedia tidak ditemukan.');
        }

        return $this->respond([
            'status' => 'success',
            'data'   => $data,
        ]);
    }

    // POST /api/penyedia
    public function create()
    {
        $input = $this->request->getJSON(true) ?: $this->request->getPost();
        $data  = array_intersect_key($input ?? [], array_flip([
            'id_transport', 'nama_penyedia', 'latitude', 'longitude',
        ]));

        if (empty($data)) {
            return $this->failValidationErrors('Body request tidak boleh kosong.');
        }

        // Pastikan id_transport valid di tabel transport
        if (!empty($data['id_transport'])) {
            $transportModel = new TransportModel();
            if (!$transportModel->find($data['id_transport'])) {
                return $this->failValidationErrors(['id_transport' => 'ID Transport tidak ditemukan di database.']);
            }
        }

        $id = $this->model->insert($data);
        if ($id === false) {
            return $this->failValidationErrors($this->model->errors());
        }

        return $this->respondCreated([
            'status'  => 'success',
            'message' => 'Data penyedia berhasil ditambahkan.',
            'data'    => $this->model->getPenyediaWithTransport((int)$id),
        ]);
    }

    // PUT / PATCH /api/penyedia/(:id)
    public function update($id = null)
    {
        if (!$this->model->find($id)) {
            return $this->failNotFound('Data penyedia tidak ditemukan.');
        }

        $input = $this->request->getJSON(true) ?: $this->request->getRawInput();
        $data  = array_intersect_key($input ?? [], array_flip([
            'id_transport', 'nama_penyedia', 'latitude', 'longitude',
        ]));

        if (empty($data)) {
            return $this->failValidationErrors('Tidak ada field valid yang dikirim untuk diperbarui.');
        }

        if (isset($data['id_transport'])) {
            $transportModel = new TransportModel();
            if (!$transportModel->find($data['id_transport'])) {
                return $this->failValidationErrors(['id_transport' => 'ID Transport tidak ditemukan di database.']);
            }
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
            'message' => 'Data penyedia berhasil diperbarui.',
            'data'    => $this->model->getPenyediaWithTransport((int)$id),
        ]);
    }

    // DELETE /api/penyedia/(:id)
    public function delete($id = null)
    {
        if (!$this->model->find($id)) {
            return $this->failNotFound('Data penyedia tidak ditemukan.');
        }

        try {
            $this->model->delete($id);
        } catch (DatabaseException $e) {
            return $this->fail('Data tidak bisa dihapus karena masih digunakan pada jadwal.', 409);
        }

        return $this->respondDeleted([
            'status'  => 'success',
            'message' => 'Data penyedia berhasil dihapus.',
        ]);
    }
}