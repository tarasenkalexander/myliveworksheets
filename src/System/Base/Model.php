<?php

namespace System\Base;

use System\Database\Connection;
use System\Database\QuerySelect;
use System\Database\SelectBuilder;

abstract class Model{
	protected static $instance;
	protected Connection $db;
	protected string $table;
	protected string $pk;

	public static function getInstance() : static{
		if(static::$instance === null){
			static::$instance = new static();
		}

		return static::$instance;
	}

	protected function __construct(){
		$this->db = Connection::getInstance();
	}

	public function all() : array{
		return $this->selector()->get();
	}

	public function get(int $id) : ?array{
		$res = $this->selector()->where("{$this->pk} = :id", ['id' => $id])->get();
		return $res[0] ?? null;
	}

	public function selector() : QuerySelect{
		$builder = new SelectBuilder($this->table);
		return new QuerySelect($this->db, $builder);
	}

	public function add(array $fields) : int{
		$names = [];
		$masks = [];

		foreach($fields as $field => $val){
			$names[] = $field;
			$masks[] = ":$field";
		}

		$namesStr = implode(', ', $names);
		$masksStr = implode(', ', $masks);

		$query = "INSERT INTO {$this->table} ($namesStr) VALUES ($masksStr)";
		$this->db->query($query, $fields);
		return $this->db->lastInsertId();
	}

	public function remove(int $id) : bool{
		$query = "DELETE FROM {$this->table} WHERE {$this->pk} =:id";
		$query = $this->db->query($query, ['id' => $id]);
		return $query->rowCount() > 0;
	}

	public function edit(int $id, array $fields) : bool{
		$pairs = [];

		foreach($fields as $field => $val){
			$pairs[] = "$field=:$field";
		}

		$pairsStr = implode(', ', $pairs);

		$query = "UPDATE {$this->table} SET $pairsStr WHERE {$this->pk} =:{$this->pk}";
		$this->db->query($query, $fields + [$this->pk => $id]);
		return true;
	}
}