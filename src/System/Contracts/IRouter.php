<?php

namespace System\Contracts;

interface IRouter{
	public function addRoute(
		string $url, 
		string $controllerName, 
		string $controllerMethod = 'index', 
		?array $paramsMap = null) : void;

	public function resolvePath(string $url) : array;
}