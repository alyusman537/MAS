<?php

namespace App\Models;

use CodeIgniter\Model;

class ModelUmum extends Model
{
    protected $table            = 'umum';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [ 'tanggal','kode','nominal', 'nia', 'keterangan', 'bukti', 'validator','tanggal_validasi'];

    protected bool $allowEmptyInserts = false;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'nia' => 'required',
        'kode' => 'required|min_length[3]|is_unique[umum.kode]',
        'nominal' => 'required',
        'keterangan' => 'required|min_length[4]|max_length[300]',
    ];
    protected $validationMessages   = [
        'kode' => [
            'required' => '{field} tidak boleh kosong',
            'min_length' => '{field} harus diisi paling setidaknya 3 karakter',
            'is_unique' => 'Kode infaq umum sudah terpakai.'
        ],
        'nominal' => [
            'required' => '{field} tidak boleh kosong',
        ],
        'nia' => [
            'required' => '{field} tidak boleh kosong',
        ],
        'keterangan' => [
            'required' => '{field} tidak boleh kosong',
            'min_length' => '{field} harus diisi setidaknya 4 karakter',
            'max_length' => '{field} tidak boleh lebih dari 300 karakter'
        ],
    ];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    public function periode($tgl_awal, $tgl_akhir)
    {
        $db = $this->db->table('umum as u');
        $db->select('u.*, a.nama');
        $db->where('u.tanggal BETWEEN "'.$tgl_awal.'" AND "'.$tgl_akhir.'"');
        $db->join('anggota as a', 'u.validator = a.nia', 'left');
        $data = $db->get();
        if(!$data) return false;
        return $data->getResult();
    }
}
