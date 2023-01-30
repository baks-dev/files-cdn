<?php

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;
use App\System\Type\Locale\Locale;

return function(RoutingConfigurator $routes) {
	$routes->import(__DIR__.'/../../Controller', 'annotation');
};