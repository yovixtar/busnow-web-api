<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('/api', 'Home::index');

$routes->post('/api/login', 'Auth::login');
$routes->post('/api/signup', 'Auth::signup');

$routes->get('/api/saldo', 'Payment::getSaldo');
$routes->post('/api/saldo', 'Payment::addSaldo');

$routes->get('/api/bus', 'Bus::getAllBus');
$routes->get('/api/tiket-by-bus/(:segment)', 'Tiket::GetTiketByIdBus/$1');
$routes->get('/api/tiket-by-filter', 'Tiket::getTiketByFilter');

$routes->post('/api/pesan-tiket', 'Payment::buyTiket');


















$routes->get('/kegiatan', 'Kegiatan::DaftarKegiatan', ['filter' => 'adminuserfilter']);
$routes->post('/kegiatan/target', 'Kegiatan::TambahTarget', ['filter' => 'userfilter']);
$routes->post('/kegiatan/realisasi', 'Kegiatan::TambahRealisasi', ['filter' => 'userfilter']);
$routes->delete('/kegiatan/(:segment)', 'Kegiatan::HapusKegiatan/$1', ['filter' => 'userfilter']);

$routes->get('/laporan', 'Laporan::DaftarLaporan', ['filter' => 'userfilter']);
$routes->post('/laporan', 'Laporan::TambahLaporan', ['filter' => 'userfilter']);
$routes->post('/laporan/switch-status', 'Laporan::SwitchStatusLaporan', ['filter' => 'userfilter']);
$routes->delete('/laporan/(:segment)', 'Laporan::HapusLaporan/$1', ['filter' => 'userfilter']);

$routes->get('/verifikasi/laporan/(:segment)', 'Verifikasi::DaftarVerifikasiLaporan/$1', ['filter' => 'userfilter']);
$routes->get('/verifikasi/kegiatan/(:segment)', 'Verifikasi::DaftarVerifikasiKegiatan/$1', ['filter' => 'userfilter']);
$routes->post('/verifikasi', 'Verifikasi::VerifikasiPetugas', ['filter' => 'userfilter']);

$routes->post('/pengawas/verifikasi', 'Verifikasi::VerifikasiPengawas', ['filter' => 'adminfilter']);

$routes->get('/pengguna/(:segment)', 'Auth::PenggunaSekarang/$1', ['filter' => 'adminuserfilter']);
$routes->post('/pengguna/ganti-nama', 'Auth::GantiNama', ['filter' => 'adminuserfilter']);
$routes->post('/pengguna/ganti-password', 'Auth::GantiPassword', ['filter' => 'adminuserfilter']);

$routes->get('/pengawas/laporan', 'Laporan::DaftarLaporanPengawas', ['filter' => 'adminfilter']);

$routes->get('/pengguna', 'Auth::DaftarPengguna', ['filter' => 'adminfilter']);
$routes->post('/pengguna', 'Auth::TambahPengguna', ['filter' => 'adminfilter']);
$routes->delete('/pengguna/(:segment)', 'Auth::HapusPengguna/$1', ['filter' => 'adminfilter']);
$routes->post('/pengguna/switch-status', 'Auth::SwitchStatusPengguna', ['filter' => 'adminfilter']);
