# BaksDev Files Cdn

[![Version](https://img.shields.io/badge/version-7.1.6-blue)](https://github.com/baks-dev/files-cdn/releases)
![php 8.3+](https://img.shields.io/badge/php-min%208.3-red.svg)

Модуль CDN файловых ресурсов 


## Установка

Рекомендуется установить на отдельный сервер с отдельным доменом!

``` bash
$ composer require baks-dev/files-cdn
```

## Настройки

Добавить директорию и установить права для загрузки файлов:

``` bash
$ sudo mkdir <path_to_project>/public/upload
$ chown -R unit:unit <path_to_project>/public/upload
``` 

Генерируем дайджест пароля:

``` bash
$ php bin/console security:hash-password
```

***

В файле конфигурации `<path_to_cdn_project>/config/packages/security.php` указываем настройку авторизации, указав результат хеширования пароля

* `<user>` - пользователь
* `<hash-password-result>` - результат хеширования пароля 

``` php
<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Config\SecurityConfig;

return static function(SecurityConfig $config) {
	
	$config->enableAuthenticatorManager(true);

	$config->firewall('dev')
		->pattern('^/(_(profiler|wdt)|css|images|js)/')
		->security(false)
	;

	$config->passwordHasher(PasswordAuthenticatedUserInterface::class)->algorithm('bcrypt');

	$config->provider('in_memory_users')
		->memory()
		->user('<user>')
		->password('<hash-password-result>')
		->roles(['ROLE_CDN'])
	;
	
	$config->firewall('main')
		->pattern('^/cdn/upload')
		->provider('in_memory_users')
		->httpBasic()
	;
};
```

***

В основном проекте в файле environment `<path_to_cdn_project>/.env` указываем хост CDN и пароль для авторизации доступа 
* `<user>` - пользователь
* `<you-plain-password>` - тектсовое представление пароля

``` dotenv
###> CDN ###
CDN_HOST=cdn.example.host
CDN_USER=<user>
CDN_PASS=<you-plain-password>
```

## Лицензия ![License](https://img.shields.io/badge/MIT-green)

The MIT License (MIT). Обратитесь к [Файлу лицензии](LICENSE.md) за дополнительной информацией.

