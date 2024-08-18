<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class View extends BaseController
{
    public function userLogin()
    {
        return view('/user/login');
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


    public function loginAdmin()
    {
        return view('/user/login');
    }
}
