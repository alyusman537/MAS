<?php

namespace App\Models;

use CodeIgniter\Model;

class ModelMutasi extends Model
{
    protected $table            = 'mutasi';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['tanggal','nomor_mutasi','jenis','nominal','admin','keterangan'];

    protected bool $allowEmptyInserts = false;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    
    public function list($tglAwal, $tglAkhir) {
        $db = $this->db->table('mutasi as m');
        $db->select('m.*, anggota.nama as nama');
        $db->join('anggota', 'anggota.nia=m.admin');
        $db->where('m.tanggal BETWEEN "'.$tglAwal.'" AND "'.$tglAkhir.'"');
        $db->orderBy('m.tanggal', 'ASC');
        $data = $db->get();
        if(!$data) return false;
        return $data->getResult();
    }

    public function detail($nomor_mutasi) {
        $db = $this->db->table('mutasi as m');
        $db->select('m.*, anggota.nama as nama');
        $db->join('anggota', 'anggota.nia=m.admin', 'left');
        $db->where('nomor_mutasi', $nomor_mutasi);
        $db->limit(1);
        $data = $db->get();
        if(!$data) return false;
        return $data->getFirstRow();
    }

    // public function saldo($tanggal)
    // {
    //     $mm = $this->db->table('mutasi');
    //     $mm->selectSum('')
    // }
}
