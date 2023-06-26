<?php

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return function (RoutingConfigurator $routes) {
    $routes->import(
        __DIR__.'/../../Controller',
        'attribute',
        false,
        __DIR__.'/../../Controller/**/*Test.php'
    );
};
