<?php

namespace App\Controllers;

use App\Helpers\JwtHelper;
use App\Models\BusModel;
use App\Models\TiketModel;
use App\Models\UserModel;
use CodeIgniter\API\ResponseTrait;
use Config\Token;
use \Firebase\JWT\JWT;
use CodeIgniter\HTTP\Response;

class Tiket extends BaseController
{
    use ResponseTrait;
    protected $busModel, $tiketModel;

    const HTTP_SERVER_ERROR = 500;
    const HTTP_BAD_REQUEST = 400;
    const HTTP_UNAUTHORIZED = 401;
    const HTTP_NOT_FOUND = 401;
    const HTTP_SUCCESS = 200;
    const HTTP_SUCCESS_CREATE = 201;

    public function __construct()
    {
        $this->busModel = new BusModel();
        $this->tiketModel = new TiketModel();
    }

    public function getTiketByIdBus($id_bus): Response
    {
        try {
            $tikets = $this->tiketModel
                ->select('tiket.id_tiket, bus.nama AS nama_bus, tiket.kelas, tiket.tanggal_berangkat, tiket.jam_berangkat, tiket.jam_sampai, bus.asal, bus.tujuan, bus.gambar AS gambar_bis, bus.kursi AS kursi_tiket')
                ->join('bus', 'bus.id_bus = tiket.id_bus')
                ->where('tiket.id_bus', $id_bus)
                ->findAll();

            if (!$tikets) {
                return $this->messageResponse('Tiket tidak ditemukan', self::HTTP_NOT_FOUND);
            }

            $data = [
                'code' => self::HTTP_SUCCESS,
                'message' => 'Daftar tiket berhasil diambil',
                'tikets' => $tikets,
            ];
            return $this->respond($data, self::HTTP_SUCCESS);
        } catch (\Throwable $th) {
            $message = 'Terjadi kesalahan dalam pengambilan data tiket: ' . $th->getMessage();
            return $this->messageResponse($message, self::HTTP_SERVER_ERROR);
        }
    }

    public function getTiketByFilter(): Response
    {
        try {
            $asal = $this->request->getGet('asal');
            $tujuan = $this->request->getGet('tujuan');
            $tanggal_berangkat = $this->request->getGet('tanggal_berangkat');
            $kursi = $this->request->getGet('kursi');

            $builder = $this->tiketModel->builder();
            $builder->select('tiket.id_tiket, bus.nama AS nama_bus, tiket.kelas, tiket.tanggal_berangkat, tiket.jam_berangkat, tiket.jam_sampai, bus.asal, bus.tujuan, bus.gambar AS gambar_bis, bus.kursi AS kursi_bus, tiket.tarif');
            $builder->join('bus', 'bus.id_bus = tiket.id_bus');

            if ($asal) {
                $builder->where('bus.asal', $asal);
            }
            if ($tujuan) {
                $builder->where('bus.tujuan', $tujuan);
            }
            if ($tanggal_berangkat) {
                $builder->where('tiket.tanggal_berangkat', $tanggal_berangkat);
            }
            if ($kursi) {
                $builder->where('tiket.kursi >=', $kursi);
            }

            $tikets = $builder->get()->getResult();

            if (!$tikets) {
                return $this->messageResponse('Tiket tidak ditemukan', self::HTTP_NOT_FOUND);
            }

            $data = [
                'code' => self::HTTP_SUCCESS,
                'message' => 'Daftar tiket berhasil diambil',
                'tikets' => $tikets,
            ];
            return $this->respond($data, self::HTTP_SUCCESS);
        } catch (\Throwable $th) {
            $message = 'Terjadi kesalahan dalam pengambilan data tiket: ' . $th->getMessage();
            return $this->messageResponse($message, self::HTTP_SERVER_ERROR);
        }
    }



    // Web App

    public function indexWeb()
    {
        $tikets = $this->tiketModel->select('tiket.*, bus.nama as nama_bus, CONCAT(bus.asal, "-", bus.tujuan) as asal_tujuan')
            ->join('bus', 'bus.id_bus = tiket.id_bus')
            ->findAll();

        return view('tiket/index', ['tikets' => $tikets]);
    }

    public function createWeb()
    {
        $buses = $this->busModel->findAll();

        return view('tiket/create', ['bus' => $buses]);
    }

    public function storeWeb() {
        $data = [
            'id_bus' => $this->request->getPost('id_bus'),
            'tanggal_berangkat' => $this->request->getPost('tanggal_berangkat'),
            'jam_berangkat' => $this->request->getPost('jam_berangkat'),
            'jam_sampai' => $this->request->getPost('jam_sampai'),
            'kelas' => $this->request->getPost('kelas'),
            'kursi' => $this->request->getPost('kursi'),
            'tarif' => $this->request->getPost('tarif'),
        ];

        $this->tiketModel->save($data);
        return redirect()->to('/tiket');
    }

    public function editWeb($id)
    {
        $tiket = $this->tiketModel->find($id);
        $buses = $this->busModel->findAll();
        
        return view('tiket/update', ['tiket' => $tiket, 'bus' => $buses]);
    }

    public function updateWeb($id) {
        $data = [
            'jam_berangkat' => $this->request->getPost('jam_berangkat'),
            'jam_sampai' => $this->request->getPost('jam_sampai'),
            'kelas' => $this->request->getPost('kelas'),
            'kursi' => $this->request->getPost('kursi'),
            'tarif' => $this->request->getPost('tarif'),
        ];

        $this->tiketModel->update($id, $data);
        return redirect()->to('/tiket');
    }

    public function deleteWeb($id)
    {
        $this->tiketModel->delete($id);
        return redirect()->to('/tiket')->with('success', 'Ticket deleted successfully.');
    }
}
