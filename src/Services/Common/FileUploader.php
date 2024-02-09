<?php

namespace App\Services\Common;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploader
{

    public function upload(UploadedFile $file, $targetDirectory, $fileName = "")
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        //$safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
        //$fileName = $safeFilename.'-'.uniqid('', true).'.'.$file->guessExtension();
        $safeFilename = "";
        if(!empty($fileName)){
            $fileName = $fileName.'.'.$file->guessExtension();
        }else{
            $fileName = time().'-'.uniqid('', true).'.'.$file->guessExtension();
        }
        
        
        if ($file->guessClientExtension() === "webp"){
            $im = imagecreatefromwebp($file);
            $fileName = $safeFilename.'-'.uniqid('', true).'.jpg';
            imagejpeg($im, $targetDirectory .'/'. $fileName , 100);
        }else{
            try {
                $file->move($targetDirectory, $fileName);
            } catch (FileException $e) {
                // ... handle exception if something happens during file upload
            }
        }

        return $fileName;
    }

}
