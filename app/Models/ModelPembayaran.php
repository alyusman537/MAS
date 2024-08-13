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
    
    public function belum($nia)
    {
        $db = $this->db->table('pembayaran as p');
        $db->select('p.*, infaq.acara, infaq.rutin');
        $db->where('p.validator IS NULL AND p.tanggal_validasi IS NULL AND infaq.aktif = "1"');
        $db->where('p.nia', $nia);
        $db->join('infaq', 'infaq.kode=p.kode_infaq', 'left');
        $db->orderBy('p.tanggal', 'DESC');
        $db->limit(50);
        $data = $db->get();
        if(!$data) return false;
        return $data->getResult();
    }

    public function lunas($nia)
    {
        $db = $this->db->table('pembayaran as p');
        $db->select('p.*, infaq.acara, infaq.rutin');
        $db->where('p.validator IS NOT NULL AND p.tanggal_validasi IS NOT NULL');
        $db->where('p.nia' , $nia);
        $db->join('infaq', 'infaq.kode=p.kode_infaq', 'left');
        $db->orderBy('p.tanggal', 'DESC');
        $db->limit(50);
        $data = $db->get();
        if(!$data) return false;
        return $data->getResult();
    }
    
}
