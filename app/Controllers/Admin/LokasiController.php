<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\LokasiModel;
use App\Models\JadwalModel;

class LokasiController extends BaseController
{
    protected LokasiModel $lokasiModel;
    protected JadwalModel $jadwalModel;

    public function __construct()
    {
        $this->lokasiModel = new LokasiModel();
        $this->jadwalModel = new JadwalModel();
    }

    public function index()
    {
        $q    = trim((string) $this->request->getGet('q'));
        $tipe = trim((string) $this->request->getGet('tipe'));

        $builder = $this->lokasiModel->orderBy('id', 'DESC');

        if (!empty($q)) {
            $escaped = $this->lokasiModel->db->escapeLikeString(strtolower($q));
            $builder->where("LOWER(nama_lokasi) LIKE '%" . $escaped . "%'");
        }

        if (!empty($tipe)) {
            $hasCol = $this->lokasiModel->db->fieldExists('tipe_lokasi', 'lokasi');
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

        $lokasiList = $builder->findAll();

        // Auto-assign tipe_lokasi untuk tampilan jika belum terisi di database
        foreach ($lokasiList as &$item) {
            if (empty($item['tipe_lokasi'])) {
                $nama = strtolower($item['nama_lokasi']);
                if (str_contains($nama, 'bandara') || str_contains($nama, 'airport')) {
                    $item['tipe_lokasi'] = 'bandara';
                } elseif (str_contains($nama, 'stasiun')) {
                    $item['tipe_lokasi'] = 'stasiun';
                } elseif (str_contains($nama, 'terminal')) {
                    $item['tipe_lokasi'] = 'terminal';
                } elseif (str_contains($nama, 'pelabuhan') || str_contains($nama, 'dermaga')) {
                    $item['tipe_lokasi'] = 'pelabuhan';
                } elseif (str_contains($nama, 'halte')) {
                    $item['tipe_lokasi'] = 'halte';
                } else {
                    $item['tipe_lokasi'] = 'terminal';
                }
            }
        }
        unset($item);

        // Hitung statistik
        $semua = $this->lokasiModel->findAll();
        $stats = [
            'total'     => count($semua),
            'bandara'   => 0,
            'stasiun'   => 0,
            'terminal'  => 0,
            'halte'     => 0,
            'pelabuhan' => 0,
        ];

        foreach ($semua as $s) {
            $t = $s['tipe_lokasi'] ?? '';
            if (empty($t)) {
                $nama = strtolower($s['nama_lokasi']);
                if (str_contains($nama, 'bandara')) $t = 'bandara';
                elseif (str_contains($nama, 'stasiun')) $t = 'stasiun';
                elseif (str_contains($nama, 'terminal')) $t = 'terminal';
                elseif (str_contains($nama, 'halte')) $t = 'halte';
                elseif (str_contains($nama, 'pelabuhan') || str_contains($nama, 'dermaga')) $t = 'pelabuhan';
                else $t = 'terminal';
            }
            if (isset($stats[$t])) {
                $stats[$t]++;
            } else {
                $stats['terminal']++;
            }
        }

        return view('admin/lokasi/index', [
            'lokasiList' => $lokasiList,
            'stats'      => $stats,
            'q'          => $q,
            'tipe'       => $tipe,
        ]);
    }

    public function simpan()
    {
        $namaLokasi = trim((string) $this->request->getPost('nama_lokasi'));
        $tipeLokasi = trim((string) $this->request->getPost('tipe_lokasi'));
        $latitude   = trim((string) $this->request->getPost('latitude'));
        $longitude  = trim((string) $this->request->getPost('longitude'));

        if (empty($namaLokasi) || empty($latitude) || empty($longitude)) {
            return redirect()->back()->withInput()->with('error', 'Nama lokasi, latitude, dan longitude wajib diisi.');
        }

        if (!is_numeric($latitude) || !is_numeric($longitude)) {
            return redirect()->back()->withInput()->with('error', 'Format latitude dan longitude harus berupa angka desimal.');
        }

        $data = [
            'nama_lokasi' => $namaLokasi,
            'latitude'    => $latitude,
            'longitude'   => $longitude,
        ];

        if ($this->lokasiModel->db->fieldExists('tipe_lokasi', 'lokasi')) {
            $data['tipe_lokasi'] = !empty($tipeLokasi) ? $tipeLokasi : 'terminal';
        }

        $insertId = $this->lokasiModel->insert($data);
        if ($insertId === false) {
            return redirect()->back()->withInput()->with('error', implode(', ', $this->lokasiModel->errors()));
        }

        return redirect()->to(base_url('admin/lokasi'))->with('success', "Titik lokasi \"{$namaLokasi}\" berhasil ditambahkan.");
    }

    public function update($id = null)
    {
        $id = (int) $id;
        $existing = $this->lokasiModel->find($id);
        if (!$existing) {
            return redirect()->to(base_url('admin/lokasi'))->with('error', 'Data lokasi tidak ditemukan.');
        }

        $namaLokasi = trim((string) $this->request->getPost('nama_lokasi'));
        $tipeLokasi = trim((string) $this->request->getPost('tipe_lokasi'));
        $latitude   = trim((string) $this->request->getPost('latitude'));
        $longitude  = trim((string) $this->request->getPost('longitude'));

        if (empty($namaLokasi) || empty($latitude) || empty($longitude)) {
            return redirect()->back()->withInput()->with('error', 'Nama lokasi, latitude, dan longitude wajib diisi.');
        }

        if (!is_numeric($latitude) || !is_numeric($longitude)) {
            return redirect()->back()->withInput()->with('error', 'Format latitude dan longitude harus berupa angka desimal.');
        }

        $data = [
            'nama_lokasi' => $namaLokasi,
            'latitude'    => $latitude,
            'longitude'   => $longitude,
        ];

        if ($this->lokasiModel->db->fieldExists('tipe_lokasi', 'lokasi')) {
            $data['tipe_lokasi'] = !empty($tipeLokasi) ? $tipeLokasi : 'terminal';
        }

        $this->lokasiModel->update($id, $data);

        return redirect()->to(base_url('admin/lokasi'))->with('success', "Data lokasi \"{$namaLokasi}\" berhasil diperbarui.");
    }

    public function hapus($id = null)
    {
        $id = (int) $id;
        $existing = $this->lokasiModel->find($id);
        if (!$existing) {
            return redirect()->to(base_url('admin/lokasi'))->with('error', 'Data lokasi tidak ditemukan.');
        }

        // Cek relasi ke jadwal (kolom id_lokasi_asal dan id_lokasi_tujuan)
        $terpakai = $this->jadwalModel
            ->where('id_lokasi_asal', $id)
            ->orWhere('id_lokasi_tujuan', $id)
            ->countAllResults();
        if ($terpakai > 0) {
            return redirect()->to(base_url('admin/lokasi'))->with('error', "Lokasi \"{$existing['nama_lokasi']}\" sedang digunakan pada {$terpakai} jadwal rute aktif. Hapus atau alihkan jadwal rute terlebih dahulu.");
        }

        $this->lokasiModel->delete($id);

        return redirect()->to(base_url('admin/lokasi'))->with('success', "Lokasi \"{$existing['nama_lokasi']}\" berhasil dihapus.");
    }
}
