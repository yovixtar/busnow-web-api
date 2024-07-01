<?php

namespace App\Controllers;

use App\Helpers\JwtHelper;
use App\Models\BusModel;
use App\Models\PesananModel;
use App\Models\TiketModel;
use App\Models\UserModel;
use CodeIgniter\API\ResponseTrait;
use Config\Token;
use \Firebase\JWT\JWT;
use CodeIgniter\HTTP\Response;

class Payment extends BaseController
{
    use ResponseTrait;
    protected $userModel, $tiketModel, $pesananModel, $busModel;

    const HTTP_SERVER_ERROR = 500;
    const HTTP_BAD_REQUEST = 400;
    const HTTP_UNAUTHORIZED = 401;
    const HTTP_NOT_FOUND = 401;
    const HTTP_SUCCESS = 200;
    const HTTP_SUCCESS_CREATE = 201;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->tiketModel = new TiketModel();
        $this->pesananModel = new PesananModel();
        $this->busModel = new BusModel();
    }

    public function getSaldo(): Response
    {
        try {
            $decoded = JwtHelper::decodeTokenFromRequest($this->request);

            if (!$decoded) {
                return $this->messageResponse('Token tidak valid', self::HTTP_UNAUTHORIZED);
            }

            $id_user = $decoded->id_user;

            $user = $this->userModel->find($id_user);
            if (!$user) {
                return $this->messageResponse('Pengguna tidak ditemukan', self::HTTP_NOT_FOUND);
            }

            $saldoSekarang = $user['saldo'];

            $message = "Saldo berhasil diambil";
            $data = [
                'code' => self::HTTP_SUCCESS,
                'message' => $message,
                'data' => $saldoSekarang,
            ];
            return $this->respond($data, self::HTTP_SUCCESS);
        } catch (\Throwable $th) {
            $message = 'Terjadi kesalahan dalam proses get saldo: ' . $th->getMessage();
            return $this->messageResponse($message, self::HTTP_SERVER_ERROR);
        }
    }

    public function addSaldo(): Response
    {
        try {
            $decoded = JwtHelper::decodeTokenFromRequest($this->request);

            if (!$decoded) {
                return $this->messageResponse('Token tidak valid', self::HTTP_UNAUTHORIZED);
            }

            $id_user = $decoded->id_user;
            $saldoTambahan = $this->request->getPost('saldo');

            if (!$saldoTambahan || !is_numeric($saldoTambahan) || $saldoTambahan <= 0) {
                return $this->messageResponse('Saldo tambahan tidak valid', self::HTTP_BAD_REQUEST);
            }

            $user = $this->userModel->find($id_user);
            if (!$user) {
                return $this->messageResponse('Pengguna tidak ditemukan', self::HTTP_NOT_FOUND);
            }

            $saldoSekarang = $user['saldo'] + $saldoTambahan;
            $this->userModel->update($id_user, ['saldo' => $saldoSekarang]);

            $message = "Saldo berhasil ditambahkan";
            $data = [
                'code' => self::HTTP_SUCCESS_CREATE,
                'message' => $message,
                'data' => $saldoSekarang,
            ];
            return $this->respond($data, self::HTTP_SUCCESS_CREATE);
        } catch (\Throwable $th) {
            $message = 'Terjadi kesalahan dalam proses add saldo: ' . $th->getMessage();
            return $this->messageResponse($message, self::HTTP_SERVER_ERROR);
        }
    }

    public function buyTiket(): Response
    {

        try {
            $decoded = JwtHelper::decodeTokenFromRequest($this->request);

            if (!$decoded) {
                return $this->messageResponse('Token tidak valid', self::HTTP_UNAUTHORIZED);
            }

            $id_user = $decoded->id_user;

            $user = $this->userModel->find($id_user);
            if (!$user) {
                return $this->messageResponse('Pengguna tidak ditemukan', self::HTTP_NOT_FOUND);
            }

            $id_tiket = $this->request->getPost('id_tiket');
            $metode_pembayaran = $this->request->getPost('metode_pembayaran');
            $nama = $this->request->getPost('nama');
            $kursi = $this->request->getPost('kursi');

            if (empty($id_tiket) || empty($metode_pembayaran) || empty($nama) || empty($kursi)) {
                return $this->messageResponse('Pemesanan tiket tidak valid', self::HTTP_BAD_REQUEST);
            }

            $tiket = $this->tiketModel->find($id_tiket);
            if (!$tiket) {
                return $this->messageResponse('Tiket tidak ditemukan', self::HTTP_NOT_FOUND);
            }

            if ($user['saldo'] < $tiket['tarif']) {
                return $this->messageResponse('Saldo anda tidak cukup!', self::HTTP_BAD_REQUEST);
            }

            $dataPesanan = [
                'id_tiket' => $id_tiket,
                'id_user' => $id_user,
                'total' => strval(intval($tiket['tarif']) * intval($kursi)),
                'metode_pembayaran' => $metode_pembayaran,
                'nama' => $nama,
                'kursi' => $kursi,
                'waktu_pesan' => date('Y-m-d H:i:s'),
            ];
            $this->pesananModel->insert($dataPesanan);

            $kursiTiketSekarang = $tiket['kursi'] - intval($kursi);
            $this->tiketModel->update($id_tiket, ['kursi' => $kursiTiketSekarang]);

            $saldoSekarang = $user['saldo'] - $tiket['tarif'];
            $this->userModel->update($id_user, ['saldo' => $saldoSekarang]);

            $bus = $this->busModel->find($tiket['id_bus']);
            $keberangkatan = $bus['asal'] . ' ke ' . $bus['tujuan'];

            $dataReturn = [
                'id_tiket' => $id_tiket,
                'nama' => $nama,
                'keberangkatan' => $keberangkatan,
                'kelas' => $tiket['kelas'],
                'tanggal' => $tiket['tanggal_berangkat'],
                'metode_pembayaran' => $metode_pembayaran,
                'total' => strval(intval($tiket['tarif']) * intval($kursi)),
                'waktu_pesan' => date('Y-m-d H:i:s'),
            ];

            $message = "Berhasil membeli Tiket";
            $data = [
                'code' => self::HTTP_SUCCESS_CREATE,
                'message' => $message,
                'data' => $dataReturn,
            ];
            return $this->respond($data, self::HTTP_SUCCESS_CREATE);
        } catch (\Throwable $th) {
            $message = 'Terjadi kesalahan dalam proses beli tiket: ' . $th->getMessage();
            return $this->messageResponse($message, self::HTTP_SERVER_ERROR);
        }
    }

    public function getAllPesanan()
    {
        try {
            $decoded = JwtHelper::decodeTokenFromRequest($this->request);

            if (!$decoded) {
                return $this->messageResponse('Token tidak valid', self::HTTP_UNAUTHORIZED);
            }

            $id_user = $decoded->id_user;

            $user = $this->userModel->find($id_user);
            if (!$user) {
                return $this->messageResponse('Pengguna tidak ditemukan', self::HTTP_NOT_FOUND);
            }

            $pesanans = $this->pesananModel->where('id_user', $id_user)->findAll();

            $dataReturn = [];

            foreach ($pesanans as $pesanan) {
                $tiket = $this->tiketModel->find($pesanan['id_tiket']);
                if (!$tiket) {
                    continue;
                }

                $bus = $this->busModel->find($tiket['id_bus']);
                if (!$bus) {
                    continue;
                }

                $keberangkatan = $bus['asal'] . ' ke ' . $bus['tujuan'];

                $dataPesanan = [
                    'id_pesanan' => $pesanan['id_pesanan'],
                    'nama' => $pesanan['nama'],
                    'keberangkatan' => $keberangkatan,
                    'kelas' => $tiket['kelas'],
                    'tanggal' => $tiket['tanggal_berangkat'],
                    'metode_pembayaran' => $pesanan['metode_pembayaran'],
                    'total' => $pesanan['total'],
                    'waktu_pesan' => $pesanan['waktu_pesan'],
                ];

                $dataReturn[] = $dataPesanan;
            }

            $message = "Berhasil mengambil Pesanan";
            $data = [
                'code' => self::HTTP_SUCCESS,
                'message' => $message,
                'data' => $dataReturn,
            ];
            return $this->respond($data, self::HTTP_SUCCESS);
        } catch (\Throwable $th) {
            $message = 'Terjadi kesalahan dalam proses pengambilan pesanan: ' . $th->getMessage();
            return $this->messageResponse($message, self::HTTP_SERVER_ERROR);
        }
    }

    public function getETiket($id_pesanan)
    {
        try {
            $pesanan = $this->pesananModel->find($id_pesanan);

            $tiket = $this->tiketModel->find($pesanan['id_tiket']);
            if (!$tiket) {
                return $this->messageResponse('Tiket tidak ditemukan', self::HTTP_NOT_FOUND);
            }

            $bus = $this->busModel->find($tiket['id_bus']);
            $keberangkatan = $bus['asal'] . ' ke ' . $bus['tujuan'];

            $dataReturn = [
                'nama' => $pesanan['nama'],
                'keberangkatan' => $keberangkatan,
                'kelas' => $tiket['kelas'],
                'tanggal' => $tiket['tanggal_berangkat'],
                'metode_pembayaran' => $pesanan['metode_pembayaran'],
                'total' => $tiket['tarif'],
                'waktu_pesan' => $pesanan['waktu_pesan'],
            ];

            $message = "Berhasil mengambil E-Tiket";
            $data = [
                'code' => self::HTTP_SUCCESS,
                'message' => $message,
                'data' => $dataReturn,
            ];
            return $this->respond($data, self::HTTP_SUCCESS);
        } catch (\Throwable $th) {
            $message = 'Terjadi kesalahan dalam proses pengambilan e-tiket: ' . $th->getMessage();
            return $this->messageResponse($message, self::HTTP_SERVER_ERROR);
        }
    }


    // Web App
    public function getPesananWeb()
    {
        $data = $this->pesananModel->select('id_pesanan, pesanan.nama, bus.nama as nama_bus, CONCAT(bus.asal, "-", bus.tujuan) as asal_tujuan, tiket.tanggal_berangkat, tiket.jam_berangkat, tiket.jam_sampai, tiket.kelas, pesanan.kursi, pesanan.total')
            ->join('tiket', 'tiket.id_tiket = pesanan.id_tiket')
            ->join('bus', 'bus.id_bus = tiket.id_bus')
            ->findAll();

        return view('pesanan/index', ['pesanan' => $data]);
    }
}
