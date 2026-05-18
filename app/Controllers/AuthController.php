<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class AuthController extends BaseController
{
    function __construct()
    {
        helper('form');
    }

    public function login()
    {
        if ($this->request->getPost()) {
            $username = $this->request->getVar('username');
            $password = $this->request->getVar('password');

            // 1. Tambahkan data email di sini
            $dataUser = [
                'username' => 'azfa', 
                'password' => '202cb962ac59075b964b07152d234b70', 
                'role'     => 'admin',
                'email'    => 'azfa@mhs.dinus.ac.id' // <--- Tambahan baru 
            ]; 

            if ($username == $dataUser['username']) {
                if (md5($password) == $dataUser['password']) {
                    
                    // 2. Tambahkan email dan waktu_login ke dalam session
                    session()->set([
                        'username'    => $dataUser['username'],
                        'role'        => $dataUser['role'],
                        'email'       => $dataUser['email'],          // <--- Tambahan baru
                        'waktu_login' => date('Y-m-d H:i:s'),         // <--- Tambahan baru
                        'isLoggedIn'  => TRUE
                    ]);

                    return redirect()->to(base_url('/'));
                } else {
                    session()->setFlashdata('failed', 'Username & Password Salah');
                    return redirect()->back();
                }
            } else {
                session()->setFlashdata('failed', 'Username Tidak Ditemukan');
                return redirect()->back();
            }
        } else {
            return view('v_login');
        }
    }

    // INI ADALAH FUNGSI LOGOUT YANG TADI HILANG
    public function logout()
    {
        session()->destroy();
        return redirect()->to('login');
    }

}