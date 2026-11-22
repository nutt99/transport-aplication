<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use App\Models\TransportModel;
use CodeIgniter\Database\Exceptions\DatabaseException;
use CodeIgniter\Database\Exceptions\DataException;

class Moda extends ResourceController
{
    protected $modelName = TransportModel::class;
    protected $format    = 'json';

    // GET /api/moda
    public function index()
    {
        return $this->respond([
            'status' => 'success',
            'data'   => $this->model->orderBy('id', 'ASC')->findAll(),
        ]);
    }

    // GET /api/moda/(:id)
    public function show($id = null)
    {
        $data = $this->model->find($id);
        if (!$data) {
            return $this->failNotFound('Data jenis transportasi tidak ditemukan.');
        }

        return $this->respond([
            'status' => 'success',
            'data'   => $data,
        ]);
    }

    // POST /api/moda
    public function create()
    {
        $input = $this->request->getJSON(true) ?: $this->request->getPost();
        $data  = array_intersect_key($input ?? [], array_flip(['jenis']));

        if (empty($data)) {
            return $this->failValidationErrors('Body request tidak boleh kosong.');
        }

        $id = $this->model->insert($data);
        if ($id === false) {
            return $this->failValidationErrors($this->model->errors());
        }

        return $this->respondCreated([
            'status'  => 'success',
            'message' => 'Data jenis transportasi berhasil ditambahkan.',
            'data'    => $this->model->find($id),
        ]);
    }

    // PUT / PATCH /api/moda/(:id)
    public function update($id = null)
    {
        if (!$this->model->find($id)) {
            return $this->failNotFound('Data jenis transportasi tidak ditemukan.');
        }

        $input = $this->request->getJSON(true) ?: $this->request->getRawInput();
        $data  = array_intersect_key($input ?? [], array_flip(['jenis']));

        if (empty($data)) {
            return $this->failValidationErrors('Tidak ada field valid yang dikirim untuk diperbarui.');
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
            'message' => 'Data jenis transportasi berhasil diperbarui.',
            'data'    => $this->model->find($id),
        ]);
    }

    // DELETE /api/moda/(:id)
    public function delete($id = null)
    {
        if (!$this->model->find($id)) {
            return $this->failNotFound('Data jenis transportasi tidak ditemukan.');
        }

        try {
            $this->model->delete($id);
        } catch (DatabaseException $e) {
            return $this->fail('Data tidak bisa dihapus karena masih digunakan oleh data penyedia.', 409);
        }

        return $this->respondDeleted([
            'status'  => 'success',
            'message' => 'Data jenis transportasi berhasil dihapus.',
        ]);
    }
}