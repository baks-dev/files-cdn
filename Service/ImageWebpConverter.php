<?php
/*
 *  Copyright 2025.  Baks.dev <admin@baks.dev>
 *
 *  Permission is hereby granted, free of charge, to any person obtaining a copy
 *  of this software and associated documentation files (the "Software"), to deal
 *  in the Software without restriction, including without limitation the rights
 *  to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 *  copies of the Software, and to permit persons to whom the Software is furnished
 *  to do so, subject to the following conditions:
 *
 *  The above copyright notice and this permission notice shall be included in all
 *  copies or substantial portions of the Software.
 *
 *  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 *  IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 *  FITNESS FOR A PARTICULAR PURPOSE AND NON INFRINGEMENT. IN NO EVENT SHALL THE
 *  AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 *  LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 *  OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 *  THE SOFTWARE.
 */

declare(strict_types=1);

namespace BaksDev\Files\Cdn\Service;

use GdImage;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

#[Autoconfigure(public: true)]
final class ImageWebpConverter
{
    private string|null $path;

    private string $file;

    private GdImage|false|null $img = null;

    /**
     * null - если директория относительно файла
     */
    public function path(?string $path): self
    {
        /** Если директория не существует - пробуем определить по относительному пути */
        if(empty($path) || false === is_dir($path))
        {
            $path = __DIR__;
        }

        $this->path = $path;

        return $this;
    }

    public function file(string $file): self
    {
        $this->file = $file;

        return $this;
    }

    public function decode(string $name, int $height): bool
    {
        if(empty($this->path))
        {
            $this->path = __DIR__;
        }

        $this->imageCreate();

        if(false === ($this->img instanceof GdImage))
        {
            return false;
        }

        $name = str_replace('.webp', '', $name);

        $img = $this->resize($this->img, $height);


        if($img instanceof GdImage)
        {
            $isSave = imagewebp($img, '/home/bundles.baks.dev/public/upload'.DIRECTORY_SEPARATOR.$name.'.webp', 80);
            imagedestroy($img);

            return $isSave;
        }

        return false;
    }


    private function imageCreate(): void
    {
        /*if($this->img instanceof GdImage)
        {
            return;
        }*/

        $filepath = $this->path.DIRECTORY_SEPARATOR.$this->file;

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

        $this->img = match ($type)
        {
            1 => imageCreateFromGif($filepath),
            2 => imageCreateFromJpeg($filepath),
            3 => imageCreateFromPng($filepath),
            6 => imageCreateFromBmp($filepath),
            18 => imageCreateFromWebp($filepath),
        };
    }

    private function resize($img, $height): GdImage|false
    {
        /* TODO: https://copyprogramming.com/howto/writing-exif-data-in-php */

        if(false === ($this->img instanceof GdImage))
        {
            return false;
        }

        $getWidth = imagesx($img);
        $getHeight = imagesy($img);

        $ratio = $height / $getHeight;
        $width = (int) ($getWidth * $ratio);

        $newImage = imagecreatetruecolor($width, $height);
        imagepalettetotruecolor($newImage);
        imagealphablending($newImage, false);
        imagecopyresampled($newImage, $img, 0, 0, 0, 0, $width, $height, $getWidth, $getHeight);

        return $newImage;
    }

}