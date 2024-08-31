<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class View extends BaseController
{
    public function index()
    {
        return view('/welcome_message');
    }
    public function userLogin()
    {
        return view('/user/login');
    }
    public function userResetPassword()
    {
        return view('/user/reset-password');
    }
    public function userProfile()
    {
        return view('/user/profile');
    }
    public function userInfaq()
    {
        return view('/user/infaq');
    }
    public function userInfaqUmum()
    {
        return view('/user/infaq-umum');
    }
    public function userKas()
    {
        return view('/user/laporan-kas');
    }

///////////// ADMIN ///////////////////
    public function adminLogin()
    {
        return view('/admin/admin-login');
    }

    public function adminDashboard()
    {
        return view('/admin/admin-dashboard');
    }

    public function adminWilayah()
    {
        return view('/admin/admin-wilayah');
    }

    public function adminAnggota()
    {
        return view('/admin/admin-anggota');
    }
    public function adminInfaq()
    {
        return view('/admin/admin-infaq');
    }
    public function adminPenerimaanInfaq()
    {
        return view('/admin/admin-terima-infaq');
    }
    public function adminPenerimaanInfaqUmum()
    {
        return view('/admin/admin-terima-infaq-umum');
    }
    public function adminTransaksiKas()
    {
        return view('/admin/admin-transaksi-kas');
    }
    public function adminPdfKas()
    {
        return view('/pdf/pdfKas');
    }
    public function adminPdfAnggota()
    {
        return view('/pdf/pdfAnggota');
    }
}
