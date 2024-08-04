<?php

namespace App\Models;

use CodeIgniter\Model;

class ModelInfaq extends Model
{
    protected $table            = 'infaq';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['tanggal', 'kode', 'header', 'keterangan', 'nominal', 'rutin', 'aktif'];

    protected bool $allowEmptyInserts = false;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    

}
