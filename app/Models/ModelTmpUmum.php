<?php

namespace App\Models;

use CodeIgniter\Model;

class ModelTmpUmum extends Model
{
    protected $table            = 'tmp_umum';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    =  [ 'nominal', 'nia', 'keterangan', 'bukti'];

    protected bool $allowEmptyInserts = false;

    

}
