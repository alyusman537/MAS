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

    public function loginAdmin()
    {
        return view('/user/login');
    }
}
