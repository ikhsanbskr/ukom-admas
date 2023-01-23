<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ModelMasyarakat;
use App\Models\ModelPetugas;

class MasyarakatLogin extends BaseController
{
    protected $modelMasyarakat;
    protected $modelPetugas;
    protected $helpers = ['form'];

    public function __construct()
    {
        $this->modelMasyarakat = new ModelMasyarakat();
        $this->modelPetugas = new ModelPetugas();
    }

    public function login()
    {
        return view('/authentication/login');
    }

    public function daftar()
    {
        return view('/authentication/daftar');
    }

    public function loginAuth()
    {
        $data = $this->request->getPost();
        $user = $this->modelMasyarakat->where('username', $data['username'])->first();
        $petugas = $this->modelPetugas->where('username', $data['username'])->first();

        // cek apakah username masyarakat ditemukan
        if ($user) {
            // cek password
            if ($user['password'] != md5($data['password'])) {
                session()->setFlashdata('error', 'Password salah!');
                return redirect()->to('/login');
            } else {
                // jika password dan user benar maka berikan session dan arahkan ke aplikasi
                $sessLogin = [
                    'isLogin' => true,
                    'nik' => $user['nik'],
                    'nama' => $user['nama'],
                    'username' => $user['username'],
                    'telp' => $user['telp']
                ];
                session()->set($sessLogin);
                return redirect()->to('/');
            }
        }

        // cek apakah username petugas ditemukan
        if ($petugas) {
            // cek password
            if ($petugas['password'] != md5($data['password'])) {
                session()->setFlashdata('error', 'Password salah!');
                return redirect()->to('/login');
            } else {
                $sessLogin = [
                    'isLogin' => true,
                    'id_petugas' => $petugas['id_petugas'],
                    'nama_petugas' => $petugas['nama_petugas'],
                    'username' => $petugas['username'],
                    'telp' => $petugas['telp'],
                    'level' => $petugas['level']
                ];

                session()->set($sessLogin);
                return redirect()->to('/');
            }
        } else {
            session()->setFlashdata('error', 'Username atau password salah!');
            return redirect()->to('/login');
        }
    }

    public function daftarAuth()
    {
        if (!$this->validate([
            'nik' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'NIK wajib diisi!'
                ]
            ],
            'nama' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Nama wajib diisi!'
                ]
            ],
            'telp' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Nomor telepon wajib diisi!'
                ]
            ],
            'username' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Username wajib diisi!'
                ]
            ],
            'password' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Password wajib diisi!'
                ]
            ],
        ])) {
            return redirect()->to('/daftar')->withInput();
        }

        $data = [
            'nik' => $this->request->getVar('nik'),
            'nama'  => $this->request->getVar('nama'),
            'telp' => $this->request->getVar('telp'),
            'username' => $this->request->getVar('username'),
            'password' => md5($this->request->getVar('password')),
        ];
        $this->modelMasyarakat->insert($data);

        session()->setFlashdata('pesan', 'Daftar berhasil!');
        return redirect()->to('/login');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/');
    }
}
