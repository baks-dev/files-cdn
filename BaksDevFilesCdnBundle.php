<?php
/*
 * This file is part of the FreshCentrifugoBundle.
 *
 * (c) Artem Henvald <genvaldartem@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace BaksDev\Files\Cdn;

use DirectoryIterator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class BaksDevFilesCdnBundle extends AbstractBundle
{
    public function prependExtension(ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $path = __DIR__.'/Resources/config/';

        foreach (new DirectoryIterator($path) as $config) {
            if ($config->isDot() || $config->isDir()) {
                continue;
            }

            if ($config->isFile() && 'routes.php' !== $config->getFilename()) {
                $container->import($config->getPathname());
            }
        }
    }
}