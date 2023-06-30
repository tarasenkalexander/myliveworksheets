<?php

namespace System;

use Twig\Loader\FilesystemLoader;
use Twig\Environment;

class Template{
	protected Environment $twig;
	protected array $globalVars = [];
	public static $instance;

	public static function getInstance() : static{
		if(static::$instance === null){
			static::$instance = new static();
		}

		return static::$instance;
	}

	public function addGlobalVar(string $name, mixed $value) : void{
		$this->globalVars[$name] = $value;
	}

	public function render($pathToTemplate, $vars = []) : string {
		return $this->twig->render($pathToTemplate, $vars + $this->globalVars);
	}

	protected function __construct(){
		$loader = new FilesystemLoader('Modules');
		
		$this->twig = new Environment($loader, [
			'cache' => 'cache/twig',
			'auto_reload' => true,
			'autoescape' => false
		]);
	}
}