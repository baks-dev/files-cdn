<?php
/*
 *  Copyright 2024.  Baks.dev <admin@baks.dev>
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

namespace BaksDev\Files\Cdn\Commands;

use DateInterval;
use DateTimeImmutable;
use DirectoryIterator;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

#[AsCommand(
    name: 'baks:files-cdn:outdated:files',
    description: 'Поиск устаревших файлов, доступ к которым не предоставлялся в течении 1 года'
)]
class SearchOutdatedFilesCommand extends Command
{
    private string $upload;

    public function __construct(
        #[Autowire('%kernel.project_dir%/public/upload/')] string $upload,
    )
    {
        parent::__construct();
        $this->upload = $upload;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $now = new DateTimeImmutable();
        $oneYearAgo = $now->sub(new DateInterval('P1Y'));

        $warning = '';

        foreach(new DirectoryIterator($this->upload) as $uploads)
        {
            if($uploads->isDot() || !$uploads->isDir())
            {
                continue;
            }

            foreach(new DirectoryIterator($uploads->getRealPath()) as $module)
            {
                if($uploads->isDot() || !$uploads->isDir())
                {
                    continue;
                }

                $lastAccessTime = fileatime($module->getRealPath());

                if($lastAccessTime < $oneYearAgo->getTimestamp())
                {
                    $warning .= date("Y-m-d H:i:s", $lastAccessTime).': '.$module->getRealPath().PHP_EOL;
                }
            }
        }

        if($warning)
        {
            $io->warning('Найдены устаревшие файлы:');
            $io->text($warning);
        }
        else
        {
            $io->success('Устаревших файлов не найдено');
        }

        return Command::SUCCESS;
    }
}
