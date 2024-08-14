<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;

class Render extends BaseController
{
    use ResponseTrait;
    public function image($imageName)
    {
        $mimeType = 'image/jpg';
        try {
            $image = file_get_contents(WRITEPATH.'uploads/profile/'.$imageName);
            $this->response
            ->setStatusCode(200)
            ->setContentType($mimeType)
            ->setBody($image)
            ->send();
        } catch (\Throwable) {
            $no_image = file_get_contents(WRITEPATH.'/no_photo.jpg');
            $this->response
            ->setStatusCode(404)
            ->setContentType($mimeType)
            ->setBody($no_image)
            ->send();
        }

    }

    public function bukti($imageName)
    {
        $mimeType = 'image/jpg';
        try {
            $image = file_get_contents(WRITEPATH.'uploads/bukti/'.$imageName);

            $this->response
            ->setStatusCode(200)
            ->setContentType($mimeType)
            ->setBody($image)
            ->send();
        } catch (\Throwable ) {
            $no_image = file_get_contents(WRITEPATH.'/No_Image_Available.jpg');
            $this->response
            ->setStatusCode(404)
            ->setContentType($mimeType)
            ->setBody($no_image)
            ->send();
        }
       
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
