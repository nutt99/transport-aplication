<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use App\Models\LokasiModel;
use CodeIgniter\Database\Exceptions\DatabaseException;
use CodeIgniter\Database\Exceptions\DataException;

class Lokasi extends ResourceController
{
    protected $modelName = LokasiModel::class;
    protected $format    = 'json';

    // GET /api/lokasi
    public function index()
    {
        $q    = trim((string) $this->request->getGet('q'));
        $tipe = trim((string) $this->request->getGet('tipe'));

        $builder = $this->model->orderBy('nama_lokasi', 'ASC');

        if (!empty($q)) {
            $escaped = $this->model->db->escapeLikeString(strtolower($q));
            $builder->where("LOWER(nama_lokasi) LIKE '%" . $escaped . "%'");
        }

        if (!empty($tipe)) {
            $hasCol = false;
            try {
                $hasCol = $this->model->db->fieldExists('tipe_lokasi', 'lokasi');
            } catch (\Throwable $e) {
                $hasCol = false;
            }
            $t = strtolower($tipe);

            $builder->groupStart();
            if ($hasCol) {
                $builder->where('tipe_lokasi', $t);
            }

            if ($t === 'bandara') {
                $builder->orWhere("LOWER(nama_lokasi) LIKE '%bandara%'")
                        ->orWhere("LOWER(nama_lokasi) LIKE '%airport%'");
            } elseif ($t === 'stasiun') {
                $builder->orWhere("LOWER(nama_lokasi) LIKE '%stasiun%'");
            } elseif ($t === 'halte') {
                $builder->orWhere("LOWER(nama_lokasi) LIKE '%halte%'");
            } elseif ($t === 'pelabuhan') {
                $builder->orWhere("LOWER(nama_lokasi) LIKE '%pelabuhan%'")
                        ->orWhere("LOWER(nama_lokasi) LIKE '%dermaga%'");
            } elseif ($t === 'terminal') {
                $builder->orWhere("LOWER(nama_lokasi) LIKE '%terminal%'");
            }
            $builder->groupEnd();
        }

        return $this->respond([
            'status' => 'success',
            'data'   => $builder->findAll(),
        ]);
    }

    // GET /api/lokasi/(:id)
    public function show($id = null)
    {
        $data = $this->model->find($id);
        if (!$data) {
            return $this->failNotFound('Data lokasi/lokasi tidak ditemukan.');
        }

        return $this->respond([
            'status' => 'success',
            'data'   => $data,
        ]);
    }

    /**
     * Memastikan kolom tipe_lokasi ada di tabel lokasi; jika belum ada, coba tambahkan otomatis
     * atau hilangkan dari array data jika database tidak memiliki kolom tersebut agar tidak error.
     */
    private function filterDataLokasi(array $data): array
    {
        if (array_key_exists('tipe_lokasi', $data)) {
            $hasCol = false;
            try {
                $hasCol = $this->model->db->fieldExists('tipe_lokasi', 'lokasi');
                if (!$hasCol) {
                    $this->model->db->query("ALTER TABLE lokasi ADD COLUMN IF NOT EXISTS tipe_lokasi VARCHAR(30) DEFAULT 'terminal'");
                    $hasCol = $this->model->db->fieldExists('tipe_lokasi', 'lokasi');
                }
            } catch (\Throwable $e) {
                $hasCol = false;
            }

            if (!$hasCol) {
                unset($data['tipe_lokasi']);
            }
        }

        return $data;
    }

    // POST /api/lokasi
    public function create()
    {
        $input = $this->request->getJSON(true) ?: $this->request->getPost();
        $data  = array_intersect_key($input ?? [], array_flip(['nama_lokasi', 'tipe_lokasi', 'latitude', 'longitude']));

        if (empty($data)) {
            return $this->failValidationErrors('Body request tidak boleh kosong.');
        }

        if (empty($data['tipe_lokasi'])) {
            $data['tipe_lokasi'] = 'terminal';
        }

        $data = $this->filterDataLokasi($data);

        $id = $this->model->insert($data);
        if ($id === false) {
            return $this->failValidationErrors($this->model->errors());
        }

        return $this->respondCreated([
            'status'  => 'success',
            'message' => 'Data lokasi/lokasi berhasil ditambahkan.',
            'data'    => $this->model->find($id),
        ]);
    }

    // PUT / PATCH /api/lokasi/(:id)
    public function update($id = null)
    {
        if (!$this->model->find($id)) {
            return $this->failNotFound('Data lokasi/lokasi tidak ditemukan.');
        }

        $input = $this->request->getJSON(true) ?: $this->request->getRawInput();
        $data  = array_intersect_key($input ?? [], array_flip(['nama_lokasi', 'tipe_lokasi', 'latitude', 'longitude']));

        if (empty($data)) {
            return $this->failValidationErrors('Tidak ada field valid yang dikirim untuk diperbarui.');
        }

        $data = $this->filterDataLokasi($data);

        try {
            if ($this->model->update($id, $data) === false) {
                return $this->failValidationErrors($this->model->errors());
            }
        } catch (DataException $e) {
            // Tidak ada perubahan nilai dari data sebelumnya
        }

        return $this->respond([
            'status'  => 'success',
            'message' => 'Data lokasi/lokasi berhasil diperbarui.',
            'data'    => $this->model->find($id),
        ]);
    }

    // DELETE /api/lokasi/(:id)
    public function delete($id = null)
    {
        if (!$this->model->find($id)) {
            return $this->failNotFound('Data lokasi/lokasi tidak ditemukan.');
        }

        // Cek apakah lokasi masih digunakan pada jadwal aktif
        $jadwalModel = new \App\Models\JadwalModel();
        $terpakai = $jadwalModel
            ->where('id_lokasi_asal', $id)
            ->orWhere('id_lokasi_tujuan', $id)
            ->countAllResults();

        if ($terpakai > 0) {
            return $this->fail("Lokasi ini tidak bisa dihapus karena masih digunakan pada {$terpakai} jadwal rute aktif.", 409);
        }

        try {
            $this->model->delete($id);
        } catch (DatabaseException $e) {
            return $this->fail('Data tidak bisa dihapus karena masih digunakan pada jadwal.', 409);
        }

        return $this->respondDeleted([
            'status'  => 'success',
            'message' => 'Data lokasi/lokasi berhasil dihapus.',
        ]);
    }
}