<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function (ContainerConfigurator $configurator)
{
    $services = $configurator->services()
      ->defaults()
      ->autowire()
      ->autoconfigure()
    ;
	
	$namespace = 'BaksDev\Files\Cdn';
    
    $services->load($namespace.'\Controller\\',  __DIR__.'/../../Controller')
      ->tag('controller.service_arguments');
    
};

