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


    // Web App Controller

    public function indexWeb()
    {
        $data['buses'] = $this->busModel->findAll();
        return view('bus/index', $data);
    }

    public function createWeb()
    {
        return view('bus/create');
    }

    public function storeWeb()
    {
        try {
            // $validation = \Config\Services::validation();

            // $validation->setRules([
            //     'asal' => 'required',
            //     'kursi' => 'required|numeric',
            //     'tujuan' => 'required',
            //     'nama' => 'required',
            //     'gambar' => 'uploaded[gambar]|max_size[gambar,2048]|is_image[gambar]'
            // ]);

            // if (!$validation->withRequest($this->request)->run()) {
            //     echo 'error';
            //     // return redirect()->back()->withInput()->with('errors', $validation->getErrors());
            // }

            $file = $this->request->getFile('gambar');
            if ($file->isValid() && !$file->hasMoved()) {
                $newName = $file->getRandomName();
                $file->move(ROOTPATH . 'public\assets\images', $newName);
                $gambar = $newName;
            } else {
                echo 'error 2';
                return redirect()->back()->withInput()->with('error', 'Error uploading image.');
            }

            $data = [
                'asal' => $this->request->getPost('asal'),
                'kursi' => $this->request->getPost('kursi'),
                'tujuan' => $this->request->getPost('tujuan'),
                'nama' => $this->request->getPost('nama'),
                'gambar' => base_url('assets/images/' . $gambar)
            ];

            $this->busModel->insert($data);

            return redirect()->to('/bus')->with('success', 'Bus created successfully.');
        } catch (\Throwable $th) {
            echo $th;
        }
    }

    public function editWeb($id)
    {
        $data['bus'] = $this->busModel->find($id);
        return view('bus/update', $data);
    }

    public function updateWeb($id)
    {
        $bus = $this->busModel->find($id);

        $data = [
            'nama' => $this->request->getPost('nama'),
            'asal' => $this->request->getPost('asal'),
            'tujuan' => $this->request->getPost('tujuan'),
        ];

        if ($this->request->getFile('gambar')->isValid()) {
            // Delete old image
            if ($bus['gambar'] && file_exists(ROOTPATH . 'public\assets\images\\' . basename($bus['gambar']))) {
                unlink(ROOTPATH . 'public\assets\images\\' . basename($bus['gambar']));
            }

            // Upload new image
            $gambar = $this->request->getFile('gambar');
            $gambarName = $gambar->getRandomName();
            $gambar->move(ROOTPATH . 'public\assets\images\\', $gambarName);
            $data['gambar'] = base_url('assets/images/' . $gambarName);
        }

        $this->busModel->update($id, $data);
        return redirect()->to('/bus');
    }

    public function deleteWeb($id)
    {
        $bus = $this->busModel->find($id);

        if ($bus['gambar'] && file_exists(ROOTPATH . 'public\assets\images\\' . basename($bus['gambar']))) {
            unlink(ROOTPATH . 'public\assets\images\\' . basename($bus['gambar']));
        }

        $this->busModel->delete($id);
        return redirect()->to('/bus');
    }
}
