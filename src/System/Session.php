<?php

namespace System;

class Session{
	public static function set(string $name, mixed $value) : void{
		self::mbStart();
		$_SESSION[$name] = $value;
	}

	public static function get(string $name) : mixed{
		self::mbStart();
		return $_SESSION[$name] ?? null;
	}

	public static function slice(string $name) : mixed{
		self::mbStart();
		$val = null;

		if(isset($_SESSION[$name])){
			$val = $_SESSION[$name];
			unset($_SESSION[$name]);
		}
		
		return $val;
	}

	protected static function mbStart(){
		if(session_status() === PHP_SESSION_NONE){
			session_start();
		}
	}
}