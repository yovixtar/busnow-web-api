<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'user';
    protected $primaryKey       = 'id_user';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = ['username','password', 'nama', 'email', 'no_telepon', 'saldo', 'token'];

}
