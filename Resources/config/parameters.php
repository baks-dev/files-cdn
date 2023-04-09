<?php
/*
 *  Copyright 2023.  Baks.dev <admin@baks.dev>
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

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function(ContainerConfigurator $configurator)
{
	
	/** Абсолютный Путь для загрузки аватарок профилей пользователя */
	$configurator->parameters()->set(
		'users_profile_avatar',
		'%kernel.project_dir%/public/upload/users_profile_avatar/'
	);
	
	/** Абсолютный Путь для загрузки обложек категорий */
	$configurator->parameters()->set(
		'product_category_cover',
		'%kernel.project_dir%/public/upload/product_category_cover/'
	);
	
	/** ОБЛОЖКИ товара галереи */
	$configurator->parameters()->set(
		'product_photo',
		'%kernel.project_dir%/public/upload/product_photo/'
	);
	
	
	/** ФАЙЛЫ товара галереи */
	$configurator->parameters()->set(
		'product_files',
		'%kernel.project_dir%/public/upload/product_files/'
	);
	

	/** ВИДЕО товара галереи */
	$configurator->parameters()->set(
		'product_video',
		'%kernel.project_dir%/public/upload/product_video/'
	);
	

	/** ОБЛОЖКИ торгового предложения */
	$configurator->parameters()->set(
		'product_offer_images',
		'%kernel.project_dir%/public/upload/product_offer_images/'
	);
	

	/** ОБЛОЖКИ множественных вариантов */
	$configurator->parameters()->set(
		'product_offer_variation_images',
		'%kernel.project_dir%/public/upload/product_offer_variation_images/'
	);
	
	
	/** ОБЛОЖКИ способов оплаты */
	$configurator->parameters()->set(
		'payment_cover',
		'%kernel.project_dir%/public/upload/payment_cover/'
	);

	
	/** ОБЛОЖКИ контактных регионов */
	$configurator->parameters()->set(
		'contacts_region_call_cover',
		'%kernel.project_dir%/public/upload/contacts_region_call_cover/'
	);
	
	/** ОБЛОЖКИ способа доставки */
	$configurator->parameters()->set(
		'delivery_cover',
		'%kernel.project_dir%/public/upload/delivery_cover/'
	);
	
	/** ОБЛОЖКИ брендов */
	$configurator->parameters()->set(
		'brand_cover',
		'%kernel.project_dir%/public/upload/brand_cover/'
	);
};