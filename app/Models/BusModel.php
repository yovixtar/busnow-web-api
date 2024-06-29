<?php

namespace App\Models;

use CodeIgniter\Model;

class BusModel extends Model
{
    protected $table            = 'bus';
    protected $primaryKey       = 'id_bus';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = ['asal','kursi','tujuan', 'nama', 'gambar'];

}
