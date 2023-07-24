<?php

namespace System\Base;

use Controllers\Users\AuthController;
use System\Contracts\IController;
use System\Exceptions\PageNotFoundException;
use System\Template;
use System\Exceptions\LoginRequiredException;

class Controller implements IController{
	protected string $title = '';
	protected string $content = '';
	protected ?array $user;
	protected array $env = [];
	protected Template $view;

	public function __construct(){
		$this->view = Template::getInstance();
		$this->user = AuthController::getUser();
	}

	public function setEnvironment(array $urlParams, array $get, array $post, array $server) : void{
		$this->env['params'] = $urlParams;
		$this->env['get'] = $get;
		$this->env['post'] = $post;
		$this->env['server'] = $server;
	}
	
	public function render() : string{
		return $this->view->render('main.twig', [
			'title' => $this->title,
			'content' => $this->content,
			'user' => $this->user
		]);
	}

	public function __call(string $name, array $arguments){
		throw new PageNotFoundException("controller has not action = $name");
	}

	public function checkAccess(){
		if($this->user === null){
			throw new LoginRequiredException('auth please!');
		}
	}
}