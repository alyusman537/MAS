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
    protected $allowedFields    = ['nia', 'nama', 'wa', 'alamat', 'wilayah', 'level', 'password', 'aktif'];

    protected bool $allowEmptyInserts = false;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';


    public function allAnggota()
    {
        $db = $this->db->table('anggota');
        $db->select('id, nia, nama, alamat, wa, wilayah, level, aktif');
        $db->where('aktif', '1');
        $db->orderBy('nama', 'ASC');
        $data = $db->get();

        if(!$data) return false;
        return $data->getResult();
    }

    public function anggotaById($id)
    {
        $db = $this->db->table('anggota');
        $db->select('id, nia, nama, alamat, wa, wilayah, level, aktif');
        $db->where('id', $id);
        $data = $db->get();

        if(!$data) return false;
        return $data->getResult();
    }
}
