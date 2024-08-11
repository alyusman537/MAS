<?php

namespace App\Models;

use CodeIgniter\Model;

class ModelPembayaran extends Model
{
    protected $table            = 'pembayaran';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['tanggal', 'nomor_pembayaran', 'kode_infaq', 'nia', 'bayar', 'tanggal_bayar', 'bukti_bayar', 'validator', 'tanggal_validasi'];

    protected bool $allowEmptyInserts = false;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';   
    
}
