<?php

namespace App\Models;

use CodeIgniter\Model;

class PesananModel extends Model
{
    protected $table            = 'pesanan';
    protected $primaryKey       = 'id_pesanan';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = ['id_tiket','id_user','total', 'metode_pembayaran', 'nama', 'kursi', 'waktu_pesan'];
}
