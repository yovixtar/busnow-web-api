<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Debug\Toolbar\Collectors\Views;

class Home extends BaseController
{
    use ResponseTrait;

    public function index() : \CodeIgniter\HTTP\Response
    {
        $message = "Selamat datang di API BusNow - Kelompok 4";
        return $this->messageResponse($message, 200);
    }

    public function homePage()
    {
        return view('home/index');
    }
}
