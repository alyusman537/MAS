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
    protected $allowedFields    = ['tanggal', 'kode', 'acara', 'tanggal_acara', 'keterangan', 'nominal', 'rutin', 'aktif'];

    protected bool $allowEmptyInserts = false;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function listInfaq()
    {
        $db = $this->db->table('infaq as i');
        $db->select('i.kode, i.acara, i.nominal, i.rutin');
        
    }
    

}
