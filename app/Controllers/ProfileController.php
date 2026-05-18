<?php

namespace App\Controllers;

class ProfileController extends BaseController
{
    public function index()
    {
        // Memanggil service session
        $session = session();

        // Mengambil data dari session dan menyiapkannya untuk View
        $data = [
            // Gunakan null coalescing operator (??) untuk menghindari error jika session kosong
            'username'     => $session->get('username') ?? 'Guest',
            'role'         => $session->get('role') ?? '-',
            'email'        => $session->get('email') ?? '-',
            'waktu_login'  => $session->get('waktu_login') ?? '-',
            // Jika ada session logged_in, statusnya 'Sudah Login', jika tidak 'Belum Login'
            'status_login' => $session->get('isLoggedIn') ? 'Sudah Login' : 'Belum Login'
        ];

        // Mengirimkan array $data ke view 'profile'
        return view('profile', $data); 
    }
}