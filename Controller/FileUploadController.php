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
class FileUploadController extends AbstractController
{
    #[Route('/cdn/upload/file', name: 'cdn.files.upload', methods: ['POST'])]
    public function index(
      Request $request,
      Filesystem $filesystem,
      MessageBusInterface $bus,
    ) : Response
    {
    
        /* Директория загрузки файла */
        $uploadDir = $request->get('dir');
        $uploadDir = $this->getParameter($uploadDir).$request->get('id');
    
    
        /**
         * Файл загрузки
         * @var UploadedFile $file
         */
        $file = $request->files->get('file');
    
        /* проверяем наличие папки, если нет - создаем */
        if(!$filesystem->exists($uploadDir))
        {
            try
            {
                $filesystem->mkdir($uploadDir);
            }
            catch(IOExceptionInterface $exception)
            {
                return $this->json(
                  [
                    'status' => 500,
                    'message' => "An error occurred while creating your directory"
                  ], 500);
            }
        }
    
        /* Если файл не существует */
        if(!file_exists($uploadDir.'/'.$file->getClientOriginalName()))
        {
            $file->move($uploadDir, $file->getClientOriginalName());
        }
		
        return $this->json(['status' => 200, 'message' => 'success'], 200);
		
    }
    
}
