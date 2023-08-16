<?php

namespace Koshiba\Framework\Core\Abstracts;

use Koshiba\Framework\Core\Connection;
use Valitron\Validator;

abstract class Model {
	protected Connection $link;

	/**
	 * Model table
	 * @var string
	 */
	protected string $table;

	/**
	 * Primary id of Table
	 * @var string
	 */
	protected string $primaryKey = 'id';

	/**
	 * Fields of table
	 * @var array
	 */
	public array $attributes = [];

	/**
	 * Errors that was met during Validation
	 * @var array
	 */
	protected array $errors = [];

	/**
	 * Rules to Validate Data
	 * @var array
	 */
	protected array $rules = [];

	public function __construct() {
		$this->link = Connection::instance();
	}

	public function load(array $data): void {
		foreach ($this->attributes as $name => $value) {
			if (isset($data[$name])) {
				$this->attributes[$name] = $data[$name];
			}
		}
	}

	public function save(): int|bool {
		if ($this->validate()) {
			$tbl = \R::dispense($this->table);
			foreach ($this->attributes as $name => $value) {
				$tbl->$name = $value;
			}
			return \R::store($tbl);
		}
		return false;
	}

	/**
	 * Validates data for Current Model
	 *
	 * @param array $data
	 *
	 * @return bool
	 */
	public function validate(array $data = []): bool {
		$data = $data != [] ? $data : $this->attributes;
		$v    = new Validator($data);
		$v->rules($this->rules);
		if ($v->validate()) {
			return true;
		} else {
			$this->errors = $v->errors();
			return false;
		}
	}

	public function getErrors(): void {
		$errors = '<ul class="col-md-6 list-group-flush">';
		foreach ($this->errors as $error) {
			foreach ($error as $err) {
				$errors .= "<li class='list-group-item-danger list-group-item'>$err</li>";
			}
		}
		$errors            .= '</ul>';
		$_SESSION['error'] = $errors;
	}

	/**
	 * Queries SQL Code with output of Success: True, Failure: False
	 *
	 * @param $sql
	 *
	 * @return bool
	 */
	public function query($sql): bool {
		return \R::exec($sql);
	}

	/**
	 * Gets all Entries from current Table
	 * @return array
	 */
	public function findAll(): array {
		return \R::findAll($this->table);
	}

	/**
	 * Finds one Entry from table with specified field value <br>
	 * <b>Default</b>: Primary key <i><b>id</b></i>
	 *
	 * @param int|string $sql
	 * @param array      $values
	 *
	 * @return array
	 */
	public function findOne(string|int $sql, array $values = []): array {
		$sql = is_int($sql) ? "$this->primaryKey=$sql LIMIT 1" : $sql . " Limit 1";
		$obj = \R::findOne($this->table, $sql, $values);
		if ($obj) {
			return $obj->getProperties();
		} else {
			return [];
		}
	}

	/**
	 * Find Entries by <b>Custom SQL</b>
	 *
	 * @param string $sql
	 * @param array  $params
	 *
	 * @return array
	 */
	public function findBySql(string $sql, array $params = []): array {
		return \R::findFromSQL($this->table, $sql, $params);
	}

	public function findLike(string $str, string $field, string $table = ''): array {
		$table = $table != '' ? $table : $this->table;
		return \R::findLike($table, [$field => [$str]]);
	}

	/**
	 * Returns Models table
	 * @return string
	 */
	public function getTable(): string {
		return $this->table;
	}
}