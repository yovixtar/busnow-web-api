<?php

namespace App\Controllers;

use App\Helpers\JwtHelper;
use App\Models\UserModel;
use CodeIgniter\API\ResponseTrait;
use Config\Token;
use \Firebase\JWT\JWT;
use CodeIgniter\HTTP\Response;

class Auth extends BaseController
{
    use ResponseTrait;
    protected $userModel;

    const HTTP_SERVER_ERROR = 500;
    const HTTP_BAD_REQUEST = 400;
    const HTTP_UNAUTHORIZED = 401;
    const HTTP_NOT_FOUND = 401;
    const HTTP_SUCCESS = 200;
    const HTTP_SUCCESS_CREATE = 201;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function login(): Response
    {
        try {
            // Mengambil request pengguna
            $credential = $this->request->getPost('credential');
            $password = $this->request->getPost('password');

            // Validasi request
            if (empty($credential) || empty($password)) {
                $message = "Email / Username dan password harus diisi.";
                return $this->messageResponse($message, self::HTTP_BAD_REQUEST);
            }

            if (!is_string($password)) {
                $message = "Password harus berupa string.";
                return $this->messageResponse($message, self::HTTP_BAD_REQUEST);
            }

            // Pencocokan data pengguna
            $hashedPassword = sha1($password);

            $user = $this->userModel->where('email', $credential)->orWhere('username', $credential)->find();

            if (!$user || $user[0]['password'] !== $hashedPassword) {
                $message = "Gagal Login. Email / Username atau password salah.";
                return $this->messageResponse($message, self::HTTP_UNAUTHORIZED);
            }

            $key = Token::JWT_SECRET_KEY;
            $payload = [
                'id_user' => $user[0]['id_user'],
                'username' => $user[0]['username'],
                'email' => $user[0]['email'],
                'nama' => $user[0]['nama'],
                'timestamp' => time(),
            ];
            $token = JWT::encode($payload, $key, 'HS256');

            $this->userModel->update($user[0]['id_user'], ['token' => $token]);

            // Pengkondisian berhasil login
            $message = "Berhasil Login";
            $data = [
                'code' => self::HTTP_SUCCESS,
                'message' => $message,
                'token' => $token,
            ];
            return $this->respond($data, self::HTTP_SUCCESS);
        } catch (\Throwable $th) {
            $message = 'Terjadi kesalahan dalam proses login.';
            return $this->messageResponse($message, self::HTTP_SERVER_ERROR);
        }
    }


    public function signup(): Response
    {
        try {
            // Ambil data POST dari request
            $nama = $this->request->getPost('nama');
            $username = $this->request->getPost('username');
            $email = $this->request->getPost('email');
            $password = $this->request->getPost('password');

            // Verifikasi request
            if (empty($email) || empty($username) || empty($nama) || empty($password)) {
                $message = "Email, Username, nama, dan password harus diisi.";
                return $this->messageResponse($message, self::HTTP_BAD_REQUEST);
            }

            if (!is_string($password)) {
                $message = "Password harus berupa string.";
                return $this->messageResponse($message, self::HTTP_BAD_REQUEST);
            }

            // Hash password menggunakan SHA1
            $hashedPassword = sha1($password);

            // Cek apakah pengguna sudah ada berdasarkan NIP
            $existingUser = $this->userModel->where('email', $email)->orWhere('username', $username)->find();

            if ($existingUser) {
                $message = "Pengguna dengan Email dan Username tersebut sudah ada.";
                return $this->messageResponse($message, self::HTTP_BAD_REQUEST);
            }

            // Tambahkan pengguna baru ke dalam tabel pengguna
            $data = [
                'nama' => $nama,
                'email' => $email,
                'username' => $username,
                'password' => $hashedPassword,
            ];

            $this->userModel->insert($data);

            // Kirim respons berhasil menambahkan pengguna
            $message = "Berhasil mendaftar sebagai pengguna.";
            $data = [
                'code' => self::HTTP_SUCCESS,
                'message' => $message,
            ];
            return $this->respond($data, self::HTTP_SUCCESS);
        } catch (\Throwable $th) {
            // Tangani kesalahan dan kirim respons error
            $message = 'Terjadi kesalahan dalam proses penambahan pengguna.'.$th;
            return $this->messageResponse($message, self::HTTP_SERVER_ERROR);
        }
    }














    public function DaftarPengguna(): Response
    {
        try {
            $decoded = JwtHelper::decodeTokenFromRequest($this->request);

            if (!$decoded) {
                return $this->messageResponse('Token tidak valid', self::HTTP_UNAUTHORIZED);
            }

            $currentNIP = $decoded->nip;

            $role = $this->request->getGet('role');

            $pengguna = $this->userModel->withDeleted();
            $pengguna->orderBy('role', 'ASC')->orderBy('deleted_at', 'ASC')->orderBy('nama', 'ASC');

            if (!empty($role)) {
                $pengguna->where('role', $role);
            }

            if (!empty($currentNIP)) {
                $pengguna->where('nip !=', $currentNIP);
            }
            
            $pengguna = $pengguna->findAll();

            // Jika tidak ada pengguna, kirim respons kosong
            if (empty($pengguna)) {
                $data = [
                    'code' => self::HTTP_SUCCESS,
                    'data' => [],
                ];
                return $this->respond($data, self::HTTP_SUCCESS);
            }

            // Format data nip dan nama dari pengguna
            $formattedData = array_map(function ($user) {
                return [
                    'nip' => $user['nip'],
                    'nama' => $user['nama'],
                    'role' => $user['role'],
                    'active' => ($user['deleted_at'] == null) ? true : false,
                ];
            }, $pengguna);

            // Kirim respons dengan data nip dan nama semua pengguna (kecuali admin)
            $data = [
                'code' => self::HTTP_SUCCESS,
                'data' => $formattedData,
            ];
            return $this->respond($data, self::HTTP_SUCCESS);
        } catch (\Exception $e) {
            // Tangani kesalahan dan kirim respons error
            $message = 'Terjadi kesalahan dalam mengambil data pengguna. Error : ' . $e;
            return $this->messageResponse($message, self::HTTP_SERVER_ERROR);
        }
    }

    public function PenggunaSekarang(string $nip): Response
    {
        try {
            // Periksa apakah NIP kosong
            if (empty($nip)) {
                $message = "NIP harus diisi.";
                return $this->messageResponse($message, self::HTTP_BAD_REQUEST);
            }

            // Cari pengguna berdasarkan NIP
            $pengguna = $this->userModel->find($nip);

            // Periksa apakah pengguna ditemukan
            if (!$pengguna) {
                $message = "Pengguna dengan NIP tersebut tidak ditemukan.";
                return $this->messageResponse($message, self::HTTP_NOT_FOUND);
            }

            // Format data pengguna
            $formattedData = [
                'nip' => $pengguna['nip'],
                'nama' => $pengguna['nama'],
                'password' => $pengguna['password'],
                'role' => $pengguna['role'],
                'token' => $pengguna['token'],
            ];

            // Kirim respons dengan data pengguna
            $data = [
                'code' => self::HTTP_SUCCESS,
                'data' => $formattedData,
            ];
            return $this->respond($data, self::HTTP_SUCCESS);
        } catch (\Throwable $th) {
            // Tangani kesalahan dan kirim respons error
            $message = 'Terjadi kesalahan dalam mengambil data pengguna.';
            return $this->messageResponse($message, self::HTTP_SERVER_ERROR);
        }
    }


    public function GantiPassword(): Response
    {
        try {
            $nip = $this->request->getPost('nip');
            $password_baru = $this->request->getPost('password-baru');

            if (empty($nip) || empty($password_baru)) {
                $message = "NIP dan password (Baru) harus diisi.";
                return $this->messageResponse($message, self::HTTP_BAD_REQUEST);
            }

            if (!is_string($password_baru)) {
                $message = "Password harus berupa string.";
                return $this->messageResponse($message, self::HTTP_BAD_REQUEST);
            }

            // Hash password baru menggunakan SHA1
            $hashedPassword = sha1($password_baru);

            // Cek apakah pengguna dengan NIP tersebut ada di database
            $existingUser = $this->userModel->find($nip);

            if (!$existingUser) {
                $message = "Pengguna dengan NIP tersebut tidak ditemukan.";
                return $this->messageResponse($message, 404);
            }

            // Update password untuk pengguna yang bersangkutan
            $this->userModel->set(['password' => $hashedPassword])->where('nip', $nip)->update();

            // Kirim respons berhasil mengubah password
            $message = "Berhasil mengubah password.";
            return $this->messageResponse($message, self::HTTP_SUCCESS);
        } catch (\Throwable $th) {
            // Tangani kesalahan dan kirim respons error
            $message = 'Terjadi kesalahan dalam proses pengubahan password.';
            return $this->messageResponse($message, self::HTTP_SERVER_ERROR);
        }
    }

    public function GantiNama(): Response
    {
        try {
            $nip = $this->request->getPost('nip');
            $nama_baru = $this->request->getPost('nama-baru');

            if (empty($nip) || empty($nama_baru)) {
                $message = "NIP dan Nama (Baru) harus diisi.";
                return $this->messageResponse($message, self::HTTP_BAD_REQUEST);
            }

            // Cek apakah pengguna dengan NIP tersebut ada di database
            $user = $this->userModel->find($nip);

            if (!$user) {
                $message = "Pengguna dengan NIP tersebut tidak ditemukan.";
                return $this->messageResponse($message, 404);
            }

            // Update password untuk pengguna yang bersangkutan
            $this->userModel->update($user['nip'], ['nama' => $nama_baru]);

            $key = Token::JWT_SECRET_KEY;
            $payload = [
                'nip' => $nip,
                'nama' => $nama_baru,
                'role' => $user['role'],
                'timestamp' => time(),
            ];
            $token = JWT::encode($payload, $key, 'HS256');

            $this->userModel->update($user['nip'], ['token' => $token]);

            $message = "Berhasil mengubah nama.";
            $data = [
                'code' => self::HTTP_SUCCESS,
                'message' => $message,
                'token' => $token,
            ];
            return $this->respond($data, self::HTTP_SUCCESS);
        } catch (\Throwable $th) {
            // Tangani kesalahan dan kirim respons error
            $message = 'Terjadi kesalahan dalam proses pengubahan nama.';
            return $this->messageResponse($message, self::HTTP_SERVER_ERROR);
        }
    }

    public function SwitchStatusPengguna(): Response
    {
        try {
            $nip = $this->request->getPost('nip');

            if (empty($nip)) {
                $message = "NIP harus diisi.";
                return $this->messageResponse($message, self::HTTP_BAD_REQUEST);
            }

            // Cek apakah pengguna dengan NIP tersebut ada di database
            $pengguna = $this->userModel->withDeleted()->find($nip);

            if (!$pengguna) {
                $message = "Pengguna dengan NIP tersebut tidak ditemukan.";
                return $this->messageResponse($message, self::HTTP_BAD_REQUEST);
            }

            if ($pengguna['deleted_at'] !== null) {
                $this->userModel->update($nip, ['deleted_at' => null]);
                $message = "Berhasil mengaktifkan akun pengguna.";
            } else {
                $this->userModel->delete($nip);
                $message = "Berhasil menonaktifkan akun pengguna.";
            }

            return $this->messageResponse($message, self::HTTP_SUCCESS);
        } catch (\Throwable $th) {
            // Tangani kesalahan dan kirim respons error
            $message = 'Terjadi kesalahan dalam proses aktivasi/non-aktivasi akun pengguna.' . $th;
            return $this->messageResponse($message, self::HTTP_SERVER_ERROR);
        }
    }

    public function HapusPengguna(string $nip): Response
    {
        try {
            if (empty($nip)) {
                $message = "NIP harus diisi.";
                return $this->messageResponse($message, self::HTTP_BAD_REQUEST);
            }

            // Periksa apakah pengguna dengan NIP tersebut ada di database
            $pengguna = $this->userModel->withDeleted()->find($nip);

            if (!$pengguna) {
                $message = "Pengguna dengan NIP tersebut tidak ditemukan.";
                return $this->messageResponse($message, self::HTTP_BAD_REQUEST);
            }

            // Hapus permanen pengguna
            $this->userModel->where('nip', $nip)->purgeDeleted();

            $message = "Berhasil menghapus permanen akun pengguna dengan NIP: $nip.";
            return $this->messageResponse($message, self::HTTP_SUCCESS);
        } catch (\Throwable $th) {
            // Tangani kesalahan dan kirim respons error
            $message = 'Terjadi kesalahan dalam proses penghapusan permanen akun pengguna.';
            return $this->messageResponse($message, self::HTTP_SERVER_ERROR);
        }
    }
}
