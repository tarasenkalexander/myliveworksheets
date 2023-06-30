<?php

namespace Modules\_base;

use System\Contracts\IController;
use System\Exceptions\Exc404;
use System\Template;
use Modules\Users\Api\Auth;
use System\Exceptions\ExcAccess;

class Controller implements IController{
	protected string $title = '';
	protected string $content = '';
	//protected ?array $user;
	protected array $env = [];
	protected Template $view;

	public function __construct(){
		$this->view = Template::getInstance();
	}

	public function setEnvironment(array $urlParams, array $get, array $post, array $server) : void{
		$this->env['params'] = $urlParams;
		$this->env['get'] = $get;
		$this->env['post'] = $post;
		$this->env['server'] = $server;
	}
	
	public function render() : string{
		return $this->view->render('Base/Views/v_main.twig', [
			'title' => $this->title,
			'content' => $this->content
		]);
	}

	public function __call(string $name, array $arguments){
		throw new Exc404("controller has not action = $name");
	}

	// public function checkAccess(){
	// 	if($this->user === null){
	// 		throw new ExcAccess('auth please!');
	// 	}
	// }
}