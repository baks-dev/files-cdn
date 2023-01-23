<?php
/*
 *  Copyright 2022.  Baks.dev <admin@baks.dev>
 *
 *  Licensed under the Apache License, Version 2.0 (the "License");
 *  you may not use this file except in compliance with the License.
 *  You may obtain a copy of the License at
 *
 *  http://www.apache.org/licenses/LICENSE-2.0
 *
 *  Unless required by applicable law or agreed to in writing, software
 *  distributed under the License is distributed on an "AS IS" BASIS,
 *  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *  See the License for the specific language governing permissions and
 *   limitations under the License.
 *
 */

namespace BaksDev\Files\Cdn\Controller;

use BaksDev\Core\Services\Security\RoleSecurity;
use GdImage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

#[RoleSecurity('ROLE_CDN')]
class ImageUploadController extends AbstractController
{
    #[Route('/cdn/upload/image', name: 'cdn.image.upload', methods: ['GET', 'POST'])]
    public function index(
      Request $request,
      Filesystem $filesystem,
      MessageBusInterface $bus,
    ) : Response
    {
        /* Директория загрузки файла */
        $uploadDir = $request->get('path');
        $uploadDir = $this->getParameter($uploadDir).$request->get('dir');
		
        /**
         * Файл изображения
         * @var UploadedFile $file
         */
        $file = $request->files->get('image');
        
        //dd($file);
        
        /* Если файл не существует */
        if(!file_exists($uploadDir.'/'.$file->getClientOriginalName()))
        {
            $file->move($uploadDir, $file->getClientOriginalName());
        }
        
        $fileInfo = pathinfo($uploadDir.'/'.$file->getClientOriginalName());
        
        /** Получаем файл для конвертации  */
        $filepath = $uploadDir.'/'.$fileInfo['basename'];
        $type = exif_imagetype($filepath); // [] if you don't have exif you could use getImageSize()
        
        $allowedTypes = [
          1,  // [] gif
          2,  // [] jpg
          3,  // [] png
          6   // [] bmp
        ];
        
        if(!in_array($type, $allowedTypes, true))
        {
            return $this->json(['status' => 500, 'message' => 'Error type imaes'], 500);
        }
        
        /** @var GdImage $img */
        
        switch($type)
        {
            case 1 :
                $img = imageCreateFromGif($filepath);
                break;
            case 2 :
                $img = imageCreateFromJpeg($filepath);
                break;
            case 3 :
                $img = imageCreateFromPng($filepath);
                break;
            case 6 :
                $img = imageCreateFromBmp($filepath);
                break;
        }
    
    
        /* Сохраняем оригинал */
    
        imagesavealpha($img, true);
        imagepalettetotruecolor($img);
        imagealphablending($img, false);
        imagewebp($img, $uploadDir.'/'.$fileInfo['filename'].'.webp', 100);
        
        
        $img_large = $this->resize($img, 1200);
        imagewebp($img_large, $uploadDir.'/'.$fileInfo['filename'].'.large.webp', 80);
        imagedestroy($img_large);
        
        $img_medium = $this->resize($img, 640);
        imagewebp($img_medium, $uploadDir.'/'.$fileInfo['filename'].'.medium.webp', 80);
        imagedestroy($img_medium);
        
        $img_small = $this->resize($img, 240);
        imagewebp($img_small, $uploadDir.'/'.$fileInfo['filename'].'.small.webp', 80);
        imagedestroy($img_small);
        
        $img_min = $this->resize($img, 60);
        imagewebp($img_min, $uploadDir.'/'.$fileInfo['filename'].'.min.webp', 80);
        imagedestroy($img_min);
        
        return $this->json(['status' => 200, 'message' => 'success'], 200);
        
    }
    
    
    public function resize($img, $height) {
    
        $getWidth = imagesx($img);
        $getHeight =  imagesy($img);
        
        $ratio = $height / $getHeight;
        $width = $getWidth * $ratio;
    
        $newImage = imagecreatetruecolor($width, $height);
        imagepalettetotruecolor($newImage);
        imagealphablending($newImage, false);
        imagecopyresampled($newImage, $img, 0, 0, 0, 0, $width, $height, $getWidth, $getHeight);
        
        return $newImage;
        
    }
    
}
