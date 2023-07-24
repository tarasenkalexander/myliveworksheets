<?php

namespace System;

use Controllers\Common\Errors\PageNotFoundController;
use Controllers\Common\HomeController;
use Controllers\Users\AuthController;
use Controllers\Sheets\CreateController;
use System\Contracts\IRouter;
use System\Exceptions\PageNotFoundException;

class Router implements IRouter
{
	protected string $baseUrl;
	protected array $routes = [];

	public function __construct(string $baseUrl = '')
	{
		$this->baseUrl = $baseUrl;
		$this->initiateRoutes();
	}

	private function initiateRoutes(): void
	{
		$this->addRoute("~^/?$~", HomeController::class);
		$this->addRoute("~^/?\?.*$~", HomeController::class);
		$this->addRoute("~^create/?$~", CreateController::class, 'create');
		$this->addRoute("~^login/?$~", AuthController::class, 'login');
		$this->addRoute("~^logout/?$~", AuthController::class, 'logout');
		$this->addRoute("~^register/?$~", AuthController::class, 'register');
		$this->addRoute("~^404/?$~", PageNotFoundController::class);
	}

	public function addRoute(string $regExp, string $controller, string $method = 'index', ?array $paramsMap = []): void
	{
		$this->routes[] = [
			'path' => $regExp,
			'c' => $controller,
			'm' => $method,
			'paramsMap' => $paramsMap
		];
	}

	private static function deleteSlashUrl(string $url)
	{
		$url = preg_replace("~/+~", '/', $url);

		if ($url) {
			return $url;
		}
	}

	private static function cutParamsUrl(string $url): string
	{
		$pos = strpos($url, '?');
		if (is_int($pos)) {
			$url = substr($url, 0, $pos);
		}

		return $url;
	}

	private static function cutBaseUrl(string $url): string
	{
		if (is_int(stripos($url, BASE_URL))) {
			$url = substr($url, strlen(BASE_URL));
		}

		return $url;
	}

	private static function parseRequestedUri(string $requestedUri): string
	{
		$requestedUri = self::deleteSlashUrl($requestedUri);
		$requestedUri = self::cutBaseUrl($requestedUri);
		$requestedUri = self::cutParamsUrl($requestedUri);

		return $requestedUri;
	}


	public function resolvePath(string $url): array
	{
		$relativeUrl = static::parseRequestedUri($url);
		$route       = $this->findPath($relativeUrl);
		return [
			'controller' => $route['c'],
			'method' => $route['m'],
			'params' => $route['params']
		];
	}

	protected function findPath(string $url): array
	{
		$activeRoute = null;

		foreach ($this->routes as $route) {
			$matches = [];
			if (preg_match($route['path'], $url, $matches)) {
				$route['params'] = [];
				foreach ($route['paramsMap'] as $index => $varName) {
					if (isset($matches[$index])) {
						$route['params'][$varName] = $matches[$index];
					}
				}
				$activeRoute = $route;
			}
		}

		if ($activeRoute === null) {
			throw new PageNotFoundException('route not found');
		}

		return $activeRoute;
	}
}