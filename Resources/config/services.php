<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function(ContainerConfigurator $configurator) {
    $services = $configurator->services()
        ->defaults()
        ->autowire()
        ->autoconfigure();

    $NAMESPACE = 'BaksDev\Files\Cdn\\';

    $MODULE = substr(__DIR__, 0, strpos(__DIR__, "Resources"));

    $services->load($NAMESPACE.'Controller\\', $MODULE.'Controller')
        ->tag('controller.service_arguments')
        ->exclude($MODULE.'Controller/**/*Test.php');
};

