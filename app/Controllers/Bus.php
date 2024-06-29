<?php

namespace App\Controllers;

use App\Helpers\JwtHelper;
use App\Models\BusModel;
use App\Models\UserModel;
use CodeIgniter\API\ResponseTrait;
use Config\Token;
use \Firebase\JWT\JWT;
use CodeIgniter\HTTP\Response;

class Bus extends BaseController
{
    use ResponseTrait;
    protected $busModel;

    const HTTP_SERVER_ERROR = 500;
    const HTTP_BAD_REQUEST = 400;
    const HTTP_UNAUTHORIZED = 401;
    const HTTP_NOT_FOUND = 401;
    const HTTP_SUCCESS = 200;
    const HTTP_SUCCESS_CREATE = 201;

    public function __construct()
    {
        $this->busModel = new BusModel();
    }

    public function getAllBus(): Response
    {
        try {
            $buses = $this->busModel->findAll();
            $data = [
                'code' => self::HTTP_SUCCESS,
                'message' => 'Daftar bus berhasil diambil',
                'buses' => $buses,
            ];
            return $this->respond($data, self::HTTP_SUCCESS);
        } catch (\Throwable $th) {
            $message = 'Terjadi kesalahan dalam pengambilan data bus: ' . $th->getMessage();
            return $this->messageResponse($message, self::HTTP_SERVER_ERROR);
        }
    }

}
