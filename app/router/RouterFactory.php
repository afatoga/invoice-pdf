<?php

declare(strict_types=1);

namespace App\Router;

use Nette;
use Nette\Application\Routers\Route;
use Nette\Application\Routers\RouteList;


final class RouterFactory
{
	use Nette\StaticClass;

	public static function createRouter(): RouteList
	{
		$router = new RouteList;
		$router->addRoute('registrace', 'Sign:up');
		$router->addRoute('prihlaseni', 'Sign:in');
		$router->addRoute('objednavky', 'Order:index');
		$router->addRoute('objednavka/<id>', 'Order:detail');
		$router->addRoute('produkty', 'Product:index');
		$router->addRoute('produkt/<id>', 'Product:edit');
		$router->addRoute('profil', 'User:detail');
		$router->addRoute('<presenter>/<action>[/<id>]', 'Homepage:default');
		
		return $router;
	}
}
