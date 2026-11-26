<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\TransportModel;
use App\Models\PenyediaModel;
use CodeIgniter\Database\Exceptions\DataException;

class TransportController extends BaseController
{
    protected TransportModel $transportModel;
    protected PenyediaModel $penyediaModel;

    public function __construct()
    {
        $this->transportModel = new TransportModel();
        $this->penyediaModel  = new PenyediaModel();
    }

    public function index()
    {
        $q = trim((string) $this->request->getGet('q'));

        $builder = $this->transportModel->orderBy('id', 'ASC');

        if (!empty($q)) {
            $escaped = $this->transportModel->db->escapeLikeString(strtolower($q));
            $builder->where("LOWER(jenis) LIKE '%" . $escaped . "%'");
        }

        $transportList = $builder->findAll();

        // Hitung jumlah penyedia per moda transportasi
        $penyediaCounts = [];
        $semuaPenyedia  = $this->penyediaModel->select('id_transport')->findAll();
        foreach ($semuaPenyedia as $p) {
            $tid = (int) $p['id_transport'];
            $penyediaCounts[$tid] = ($penyediaCounts[$tid] ?? 0) + 1;
        }

        foreach ($transportList as &$item) {
            $item['jumlah_penyedia'] = $penyediaCounts[(int) $item['id']] ?? 0;
        }
        unset($item);

        $stats = [
            'total_transport' => $this->transportModel->countAllResults(),
            'total_penyedia'  => count($semuaPenyedia),
        ];

        return view('admin/transport/index', [
            'transportList' => $transportList,
            'stats'         => $stats,
            'q'             => $q,
        ]);
    }

    public function simpan()
    {
        $jenis = trim((string) $this->request->getPost('jenis'));

        if (empty($jenis)) {
            return redirect()->back()->withInput()->with('error', 'Jenis transportasi wajib diisi.');
        }

        $insertId = $this->transportModel->insert([
            'jenis' => $jenis,
        ]);

        if ($insertId === false) {
            return redirect()->back()->withInput()->with('error', implode(', ', $this->transportModel->errors()));
        }

        return redirect()->to(base_url('admin/transport'))->with('success', "Moda transportasi \"{$jenis}\" berhasil ditambahkan.");
    }

    public function update($id = null)
    {
        $id       = (int) $id;
        $existing = $this->transportModel->find($id);
        if (!$existing) {
            return redirect()->to(base_url('admin/transport'))->with('error', 'Data jenis transportasi tidak ditemukan.');
        }

        $jenis = trim((string) $this->request->getPost('jenis'));

        if (empty($jenis)) {
            return redirect()->back()->withInput()->with('error', 'Jenis transportasi wajib diisi.');
        }

        try {
            if ($this->transportModel->update($id, ['jenis' => $jenis]) === false) {
                return redirect()->back()->withInput()->with('error', implode(', ', $this->transportModel->errors()));
            }
        } catch (DataException $e) {
            // Tidak ada perubahan nilai dari data sebelumnya
        }

        return redirect()->to(base_url('admin/transport'))->with('success', "Moda transportasi \"{$jenis}\" berhasil diperbarui.");
    }

    public function hapus($id = null)
    {
        $id       = (int) $id;
        $existing = $this->transportModel->find($id);
        if (!$existing) {
            return redirect()->to(base_url('admin/transport'))->with('error', 'Data jenis transportasi tidak ditemukan.');
        }

        $terpakai = $this->penyediaModel->where('id_transport', $id)->countAllResults();
        if ($terpakai > 0) {
            return redirect()->to(base_url('admin/transport'))->with('error', "Moda \"{$existing['jenis']}\" tidak dapat dihapus karena masih digunakan oleh {$terpakai} penyedia armada.");
        }

        $this->transportModel->delete($id);

        return redirect()->to(base_url('admin/transport'))->with('success', "Moda transportasi \"{$existing['jenis']}\" berhasil dihapus.");
    }
}
