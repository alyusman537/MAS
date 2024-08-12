<?php

namespace App\Models;

use CodeIgniter\Model;

class ModelAnggota extends Model
{
    protected $table            = 'anggota';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['nia', 'nama', 'wa', 'alamat', 'wilayah', 'level', 'password', 'foto', 'email', 'aktif'];

    protected bool $allowEmptyInserts = false;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';


    public function allAnggota()
    {
        $db = $this->db->table('anggota');
        $db->select('id, nia, nama, alamat, wa, wilayah, level, email, aktif');
        $db->where(['aktif' => 'aktif']);//, 'nia <> "0537"']);
        $db->where('nia <> 0537');
        $db->orderBy('nama', 'ASC');
        $data = $db->get();

        if(!$data) return false;
        return $data->getResult();
    }

    public function anggotaById($nia)
    {
        $db = $this->db->table('anggota as a');
        $db->select('id, nia, nama, alamat, wa, wilayah, level, foto, email, aktif');
        $db->where('nia', $nia);
        $db->limit(1);
        $data = $db->get();

        if($nia === '0537') return false;
        if(!$data) return false;
        return $data->getResult();
    }
}
