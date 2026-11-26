<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class AuthController extends BaseController
{
    public function login()
    {
        $session = session();
        if ($session->get('is_admin_logged_in')) {
            return redirect()->to(base_url('admin/lokasi'));
        }

        return view('admin/login');
    }

    public function loginProcess()
    {
        $username = trim((string) $this->request->getPost('username'));
        $password = trim((string) $this->request->getPost('password'));

        $expectedUser = env('ADMIN_USERNAME', 'admin');
        $expectedPass = env('ADMIN_PASSWORD', 'admin123');

        if ($username === $expectedUser && $password === $expectedPass) {
            session()->set([
                'is_admin_logged_in' => true,
                'admin_username'     => $username,
                'admin_logged_at'   => date('Y-m-d H:i:s'),
            ]);

            return redirect()->to(base_url('admin/lokasi'))->with('success', 'Selamat datang di Panel Manajemen Lokasi Transportasi!');
        }

        return redirect()->back()->withInput()->with('error', 'Username atau password admin salah.');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to(base_url('admin/login'))->with('success', 'Anda telah berhasil logout.');
    }
}
