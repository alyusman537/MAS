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
    
    public function daftarInfaq($nia, $status)
    {
        $db = $this->db->table('pembayaran as p');
        $db->select('p.*, infaq.acara, infaq.rutin, infaq.nominal');
        $db->where('p.validator IS '.$status.' AND infaq.aktif = "1"');
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
    
    public function daftarTungguBaru()
    {
        $db = $this->db->table('pembayaran as p');
        $db->select('p.*, infaq.acara, infaq.rutin, infaq.tanggal_acara, anggota.nama');
        $db->where('p.bayar = 0 AND p.validator IS NULL AND infaq.aktif = 1');
        $db->join('infaq', 'infaq.kode=p.kode_infaq', 'left');
        $db->join('anggota', 'anggota.nia=p.nia', 'left');
        $db->orderBy('p.tanggal', 'DESC');
        $db->limit(50);
        $data = $db->get();
        if(!$data) return false;
        return $data->getResult();
    }
    public function daftarTungguPending()
    {
        $db = $this->db->table('pembayaran as p');
        $db->select('p.*, infaq.acara, infaq.rutin, infaq.tanggal_acara, anggota.nama');
        $db->where('p.bayar >= infaq.nominal AND p.validator IS NULL AND infaq.aktif = 1');
        $db->join('infaq', 'infaq.kode=p.kode_infaq', 'left');
        $db->join('anggota', 'anggota.nia=p.nia', 'left');
        $db->orderBy('p.tanggal', 'DESC');
        $db->limit(50);
        $data = $db->get();
        if(!$data) return false;
        return $data->getResult();
    }

    public function daftarTungguLunas()
    {
        $db = $this->db->table('pembayaran as p');
        $db->select('p.*, infaq.acara, infaq.rutin, infaq.tanggal_acara, anggota.nama');
        $db->where('p.bayar >= infaq.nominal AND p.validator IS NOT NULL AND infaq.aktif = 1');
        $db->join('infaq', 'infaq.kode=p.kode_infaq', 'left');
        $db->join('anggota', 'anggota.nia=p.nia', 'left');
        $db->orderBy('p.tanggal', 'DESC');
        $db->limit(50);
        $data = $db->get();
        if(!$data) return false;
        return $data->getResult();
    }

    public function hitungInfaq($kode_infaq, $status)
    {
        $kondisi = null;
        if($status == 'lunas') {
            $kondisi = 'p.bayar >= infaq.nominal AND p.validator IS NOT NULL AND infaq.aktif = 1';
        }
        if($status == 'pending') {
            $kondisi = 'p.bayar >= infaq.nominal AND p.validator IS NOT NULL AND infaq.aktif = 1';
        }
        if($status == 'baru') {
            $kondisi = 'p.bayar = 0 AND p.validator IS NULL AND infaq.aktif = 1';
        }
        $db = $this->db->table('pembayaran as p');
        $db->selectCount('p.nomor_pembayaran');
        $db->select('infaq.kode, infaq.acara, infaq.tanggal_acara');
        $db->where('p.kode_infaq', $kode_infaq);
        $db->where($kondisi);
        $db->join('infaq', 'p.kode_infaq = infaq.kode');
        $db->limit(1);
        $data = $db->get();
        if(!$data) return false;
        return $data->getResult();
    }
    public function listBayar($kode_infaq)
    {
        $db = $this->db->table('pembayaran as p');
        $db->select('p.*, infaq.acara, infaq.rutin, infaq.tanggal_acara, anggota.nama, anggota.wilayah');
        $db->where('p.kode_infaq', $kode_infaq);
        // $db->where('p.bayar >= infaq.nominal AND p.validator IS NOT NULL AND infaq.aktif = 1');
        $db->join('infaq', 'infaq.kode=p.kode_infaq');
        $db->join('anggota', 'anggota.nia=p.nia', 'left');
        $db->orderBy('anggota.nama', 'ASC');
        $db->orderBy('anggota.wilayah', 'ASC');
        $data = $db->get();
        if(!$data) return false;
        return $data->getResult();
    }


}
