<?php

namespace App\Controllers;

use App\Helpers\JwtHelper;
use App\Models\UserModel;
use App\Models\AdminModel;
use CodeIgniter\API\ResponseTrait;
use Config\Token;
use \Firebase\JWT\JWT;
use CodeIgniter\HTTP\Response;

class Auth extends BaseController
{
    use ResponseTrait;
    protected $userModel, $adminModel;

    const HTTP_SERVER_ERROR = 500;
    const HTTP_BAD_REQUEST = 400;
    const HTTP_UNAUTHORIZED = 401;
    const HTTP_NOT_FOUND = 401;
    const HTTP_SUCCESS = 200;
    const HTTP_SUCCESS_CREATE = 201;

    public $sessionDriver            = 'CodeIgniter\Session\Handlers\FileHandler';

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->adminModel = new AdminModel();
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
            $message = 'Terjadi kesalahan dalam proses penambahan pengguna.' . $th;
            return $this->messageResponse($message, self::HTTP_SERVER_ERROR);
        }
    }


    // Web App

    public function loginWeb()
    {
        return view('auth/login');
    }

    public function loginAccWeb()
    {

        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        // Validate input
        if (empty($username) || empty($password)) {
            return redirect()->to('/login');
        }

        $admin = $this->adminModel->where('username', $username)->first();

        if (!$admin || (sha1($password) !== $admin['password'])) {
            return redirect()->to('/login');
        }

        $session = session();
        $session->set([
            'username' => $admin['username'],
            'isLoggedIn' => true
        ]);

        return redirect()->to('/home');
    }

    public function logoutWeb()
    {
        // Example using session:
        $session = session();
        $session->destroy();

        // Redirect to login page
        return redirect()->to('/login');
    }

    public function getAllUsersWeb()
    {
        $pengguna = $this->userModel->findAll();
        return view('pengguna/index', ['pengguna' => $pengguna]);
    }
}
