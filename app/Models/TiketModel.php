<?php

namespace App\Models;

use CodeIgniter\Model;

class TiketModel extends Model
{
    protected $table            = 'tiket';
    protected $primaryKey       = 'id_tiket';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = ['id_bus','tanggal_berangkat','jam_berangkat', 'jam_sampai', 'kelas', 'tarif', 'kursi'];
}
