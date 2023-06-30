<?php

namespace System\Contracts;

interface IController{
	public function setEnvironment(array $urlParams, array $get, array $post, array $server) : void;
	public function render() : string;
}