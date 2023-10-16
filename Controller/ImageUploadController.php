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

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[AsController]
#[IsGranted(new Expression('"ROLE_CDN" in role_names'))]
class ImageUploadController
{
    #[Route('/cdn/upload/image', name: 'cdn.image.upload', methods: ['POST'])]
    public function index(
        #[Autowire('%kernel.project_dir%/public/upload/')] string $upload,
        Request $request,
        Filesystem $filesystem,
    ): Response
    {

        $uploadDir = $upload.$request->get('dir');

        if(empty($uploadDir))
        {
            return new JsonResponse([
                'status' => 500,
                'message' => 'An error occurred while creating your directory',
            ], 500);
        }


        /** Если существует директория - не создаем WEBP */
        if($filesystem->exists($uploadDir))
        {
            return new JsonResponse([
                'status' => 200,
                'message' => 'success',
            ], 200);
        }

        try
        {
            $filesystem->mkdir($uploadDir);
        }
        catch(IOExceptionInterface $exception)
        {
            return new JsonResponse([
                'status' => 500,
                'message' => 'An error occurred while creating your directory',
            ], 500);
        }

        /**
         * Файл изображения.
         *
         * @var UploadedFile $file
         */
        $file = $request->files->get('image');

        /** Если имеется конвертируемый файл с указанной хеш-суммой  */
        if(file_exists($uploadDir.'/original.webp'))
        {
            return new JsonResponse([
                'status' => 200,
                'message' => 'success',
            ], 200);
        }

        $file->move($uploadDir, $file->getClientOriginalName());
        $img = $this->imageCreate($uploadDir.'/'.$file->getClientOriginalName());

        // Сохраняем оригинал

        imagesavealpha($img, true);
        imagepalettetotruecolor($img);
        imagealphablending($img, false);
        imagewebp($img, $uploadDir.'/original.webp', 100);

        $img_large = $this->resize($img, 1200);
        imagewebp($img_large, $uploadDir.'/large.webp', 80);
        imagedestroy($img_large);

        $img_medium = $this->resize($img, 640);
        imagewebp($img_medium, $uploadDir.'/medium.webp', 80);
        imagedestroy($img_medium);

        $img_small = $this->resize($img, 300);
        imagewebp($img_small, $uploadDir.'/small.webp', 80);
        imagedestroy($img_small);

        $img_min = $this->resize($img, 60);
        imagewebp($img_min, $uploadDir.'/min.webp', 80);
        imagedestroy($img_min);

        /** Удаляем оригинал файла */
        $filesystem->remove($uploadDir.'/'.$file->getClientOriginalName());

        return new JsonResponse([
            'status' => 200,
            'message' => 'success',
        ], 200);
    }



    public function imageCreate($filepath) : mixed
    {
        $type = exif_imagetype($filepath); // [] if you don't have exif you could use getImageSize()

        $allowedTypes = [
            1,  // [] gif
            2,  // [] jpg
            3,  // [] png
            6,   // [] bmp
            18,   // [] webp
        ];

        if(!in_array($type, $allowedTypes, true))
        {
            throw new FileException('Error type images');
        }

        return match ($type) {
            1 => imageCreateFromGif($filepath),
            2 => imageCreateFromJpeg($filepath),
            3 => imageCreateFromPng($filepath),
            6 => imageCreateFromBmp($filepath),
            18 => imageCreateFromWebp($filepath),
        };
    }


    public function resize($img, $height)
    {
        $getWidth = imagesx($img);
        $getHeight = imagesy($img);

        $ratio = $height / $getHeight;
        $width = $getWidth * $ratio;

        $newImage = imagecreatetruecolor($width, $height);
        imagepalettetotruecolor($newImage);
        imagealphablending($newImage, false);
        imagecopyresampled($newImage, $img, 0, 0, 0, 0, $width, $height, $getWidth, $getHeight);

        return $newImage;
    }
}