<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;

class Render extends BaseController
{
    use ResponseTrait;
    public function image($imageName)
    {
        if(($image = file_get_contents(WRITEPATH.'uploads/foto/'.$imageName)) === FALSE) return $this->fail('foto tidak ditemukan', 404);;
        
        // choose the right mime type
        $mimeType = 'image/jpg';

        $this->response
        ->setStatusCode(200)
        ->setContentType($mimeType)
        ->setBody($image)
        ->send();
    }

    public function js($jsName)
    {
        if(($js = file_get_contents(__DIR__.'/../Views/JS/'.$jsName)) === FALSE) return $this->fail('foto tidak ditemukan', 404); //show_404();
        // choose the right mime type
        $mimeType = 'text/javascript';

        $this->response
        ->setStatusCode(200)
        ->setContentType($mimeType)
        ->setBody($js)
        ->send();
    }
}
